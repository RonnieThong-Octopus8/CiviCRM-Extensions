<?php

ini_set('max_execution_time', 800); // Set maximum execution time to 800 seconds
require_once 'vendor/autoload.php';

use CRM_AutoCreateContact_ExtensionUtil as E;
use phpseclib3\Net\SFTP;
use phpseclib3\Crypt\PublicKeyLoader;

class CRM_AutoCreateContact_Page_AutoCreatePage extends CRM_Core_Page {

  public function run() {
    CRM_Utils_System::setTitle(E::ts('Fetch and Process Recordings'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            $startDate = $input['start_date'] ?? null;
            $endDate = $input['end_date'] ?? null;
            $campaign = $input['campaign'] ?? null;

            if (!$startDate || !$endDate || !$campaign) {
                throw new Exception('Missing required parameters.');
            }

            $activitiesCreated = [];
            $result = $this->fetchAndProcessFiles($campaign, $startDate, $endDate, $activitiesCreated);

            echo json_encode([
                'status' => 'success',
                'message' => 'Files processed successfully.',
                'contacts' => $result['contacts'],
                'activities' => $activitiesCreated
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
        exit;
    }

    parent::run();
  }

  private function fetchAndProcessFiles($campaign, $startDate, $endDate, &$activitiesCreated) {
    $campaigns = [
        'cpf' => [
            'host' => 'cpf-sbc.novomind.com',
            'private_key' => 'C:\\xampp\\htdocs\\Ronnie\\wp-content\\uploads\\civicrm\\ext\\auto_create_contact\\privatekey\\CPF_Private_Key.ppk'
        ],
        'jtc' => [
            'host' => 'jtc05.ecomm.nmop.de',
            'private_key' => 'C:\\xampp\\htdocs\\Ronnie\\wp-content\\uploads\\civicrm\\ext\\auto_create_contact\\privatekey\\JTC_Private_Key.ppk'
        ],
    ];

    if (!isset($campaigns[$campaign])) {
        throw new Exception('Invalid campaign selected');
    }

    $sftp_host = $campaigns[$campaign]['host'];
    $sftp_port = 7387;
    $sftp_user = 'transfer';
    $sftp_private_key_path = $campaigns[$campaign]['private_key'];

    $start = new DateTime($startDate);
    $end = new DateTime($endDate);
    $interval = DateInterval::createFromDateString('1 day');
    $period = new DatePeriod($start, $interval, $end->add($interval));

    $local_directory = 'C:\\xampp\\htdocs\\Ronnie\\wp-content\\uploads\\civicrm\\ext\\auto_create_contact\\recordings';

    $key = PublicKeyLoader::load(file_get_contents($sftp_private_key_path));

    $sftp = new SFTP($sftp_host, $sftp_port);
    if (!$sftp->login($sftp_user, $key)) {
        throw new Exception('Login failed');
    }

    $contactsCreated = [];

    foreach ($period as $date) {
        $formattedDate = $date->format('Y/m/d');
        $remote_directory = "/converted/$formattedDate";

        if ($sftp->is_dir($remote_directory)) {
            $files = $sftp->nlist($remote_directory);
            if ($files === false) {
                throw new Exception('Failed to list files in ' . $remote_directory);
            }

            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $remoteFile = "$remote_directory/$file";

                if ($this->shouldProcessFile($file)) {
                    $localFilePath = $this->downloadFile($sftp, $remoteFile, $local_directory, $campaign, $date);
                    if ($localFilePath !== false) {
                        $this->processFile($file, $localFilePath, $contactsCreated, $activitiesCreated);
                    }
                }
            }
        } else {
            echo "Directory $remote_directory does not exist on the SFTP server.";
        }
    }

    return ['contacts' => $contactsCreated];
  }

  private function shouldProcessFile($fileName) {
    $parts = explode('_', $fileName);
    if (count($parts) < 3) {
        return false;
    }

    $phoneNumber = $parts[2];

    // Check for contact by phone number
    $contactResult = civicrm_api3('Contact', 'get', [
        'contact_type' => 'Individual',
        'phone' => ['LIKE' => '%' . $phoneNumber],
        'options' => ['limit' => 1],
    ]);

    if ($contactResult['is_error'] == 0 && $contactResult['count'] > 0) {
        $contactID = $contactResult['values'][array_key_first($contactResult['values'])]['contact_id'];

        $activitiesResult = civicrm_api3('Activity', 'get', [
            'source_contact_id' => $contactID,
            'options' => ['limit' => 0],
        ]);

        if ($activitiesResult['is_error'] == 0) {
            foreach ($activitiesResult['values'] as $activity) {
                $activityAttachments = civicrm_api3('Attachment', 'get', [
                    'entity_table' => 'civicrm_activity',
                    'entity_id' => $activity['id'],
                    'options' => ['limit' => 0],
                ]);

                if ($activityAttachments['is_error'] == 0) {
                    foreach ($activityAttachments['values'] as $attachment) {
                        if ($attachment['name'] === $fileName) {
                            return false;
                        }
                    }
                }
            }
        }
    }

    return true;
  }

  private function downloadFile($sftp, $remoteFile, $localDir, $campaign, $date) {
    $year = $date->format('Y');
    $month = $date->format('m');
    $day = $date->format('d');

    $local_subdir = $localDir . DIRECTORY_SEPARATOR . $campaign . DIRECTORY_SEPARATOR . $year . DIRECTORY_SEPARATOR . $month . DIRECTORY_SEPARATOR . $day;
    if (!is_dir($local_subdir)) {
        mkdir($local_subdir, 0777, true);
    }

    $localFile = $local_subdir . DIRECTORY_SEPARATOR . basename($remoteFile);

    if ($sftp->get($remoteFile, $localFile)) {
        return $localFile;
    } else {
        throw new Exception("Failed to download $remoteFile to $localFile");
    }
  }

  private function processFile($fileName, $filePath, &$contactsCreated, &$activitiesCreated) {
    $parts = explode('_', $fileName);
    if (count($parts) < 3) {
        return; // Ensure there are enough parts in the filename
    }

    // Extracting the phone number (assuming it's consistently located)
    $phoneNumber = $parts[2];

    // Check for existing contact by phone number
    $contactResult = civicrm_api3('Contact', 'get', [
        'contact_type' => 'Individual',
        'phone' => ['LIKE' => '%' . $phoneNumber],
        'options' => ['limit' => 1],
    ]);

    // Determine contact ID
    if ($contactResult['is_error'] == 0 && $contactResult['count'] > 0) {
        $contactID = $contactResult['values'][array_key_first($contactResult['values'])]['contact_id'];
    } else {
        // Create a new contact with the phone number
        $contactResult = civicrm_api3('Contact', 'create', [
            'contact_type' => 'Individual',
            'first_name' => $phoneNumber,
            'phone' => [
                [
                    'phone' => $phoneNumber,
                    'location_type_id' => 3, // Assuming '3' is the ID for the primary location
                    'is_primary' => 1,
                ]
            ],
        ]);
        $contactID = $contactResult['id'];
        $contactsCreated[$contactID] = $phoneNumber;
    }

    // Check if the file is already attached
    if (!$this->fileAlreadyAttached($contactID, $fileName)) {
        $subject = $this->generateSubjectFromFilename($fileName); // Generate the subject based on the filename

        // Create an activity attached to the contact
        $activityResult = civicrm_api3('Activity', 'create', [
            'source_contact_id' => $contactID,
            'activity_type_id' => '67', // Placeholder for actual activity type ID
            'subject' => $subject,
            'activity_date_time' => date('Y-m-d H:i:s'),
            'status_id' => 'Completed',
        ]);

        if ($activityResult['is_error'] == 0) {
            $activityID = $activityResult['id'];
            $attachmentResult = civicrm_api3('Attachment', 'create', [
                'entity_table' => 'civicrm_activity',
                'entity_id' => $activityID,
                'name' => $fileName,
                'mime_type' => 'audio/mpeg',
                'options' => ['move-file' => $filePath],
            ]);

            if ($attachmentResult['is_error'] != 0) {
                $errorMessage = 'Attachment creation failed for file ' . $fileName . ': ' . $attachmentResult['error_message'];
                CRM_Core_Session::setStatus(ts($errorMessage), ts('Error'), 'error');
                error_log($errorMessage);
            } else {
                $activitiesCreated[] = ['activity_id' => $activityID, 'contact_id' => $contactID];
            }
        } else {
            $errorMessage = 'Activity creation failed: ' . $activityResult['error_message'];
            CRM_Core_Session::setStatus(ts($errorMessage), ts('Error'), 'error');
            error_log($errorMessage);
        }
    }
}

private function generateSubjectFromFilename($fileName) {
    $parts = explode('_', $fileName);
    $datePart = $parts[0] . '_' . $parts[1];
    $callDirection = (strpos(strtolower($fileName), 'outbound') !== false) ? "OUTBOUND" : "INBOUND";
    return $datePart . ' ' . $callDirection;
}


  private function fileAlreadyAttached($contactID, $fileName) {
    $activitiesResult = civicrm_api3('Activity', 'get', [
        'source_contact_id' => $contactID,
        'options' => ['limit' => 0],
    ]);

    if ($activitiesResult['is_error'] == 0) {
        foreach ($activitiesResult['values'] as $activity) {
            $activityAttachments = civicrm_api3('Attachment', 'get', [
                'entity_table' => 'civicrm_activity',
                'entity_id' => $activity['id'],
                'options' => ['limit' => 0],
            ]);

            if ($activityAttachments['is_error'] == 0) {
                foreach ($activityAttachments['values'] as $attachment) {
                    if ($attachment['name'] === $fileName) {
                        return true;
                    }
                }
            }
        }
    }

    return false;
  }
}
