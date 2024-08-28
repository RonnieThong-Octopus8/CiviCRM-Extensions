<?php

ini_set('max_execution_time', 600);

use phpseclib3\Net\SFTP;
use phpseclib3\Crypt\PublicKeyLoader;

function extest_fetch_files($campaign, $startDate, $endDate) {
    // Define SFTP settings for each campaign
    $campaigns = [
        'cpf' => [
            'host' => 'cpf-sbc.novomind.com',
            'private_key' => 'C:\\xampp\\htdocs\\Ronnie\\wp-content\\uploads\\civicrm\\ext\\extest\\privatekey\\CPF_Private_Key.ppk'
        ],
        'jtc' => [
            'host' => 'jtc05.ecomm.nmop.de',
            'private_key' => 'C:\\xampp\\htdocs\\Ronnie\\wp-content\\uploads\\civicrm\\ext\\extest\\privatekey\\JTC_Private_Key.ppk'
        ],
        // Add more campaigns as needed
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

    $local_directory = 'C:\\xampp\\htdocs\\Ronnie\\wp-content\\uploads\\civicrm\\ext\\extest\\recordings';

    // Load the private key
    $key = PublicKeyLoader::load(file_get_contents($sftp_private_key_path));

    // Establish SFTP connection
    $sftp = new SFTP($sftp_host, $sftp_port);
    if (!$sftp->login($sftp_user, $key)) {
        throw new Exception('Login failed');
    }

    foreach ($period as $date) {
        $formattedDate = $date->format('Y/m/d');
        $remote_directory = "/converted/$formattedDate";

        // Recursive function to download directory and maintain structure
        $downloadDirectory = function($sftp, $remoteDir, $localDir) use (&$downloadDirectory) {
            if (!is_dir($localDir)) {
                mkdir($localDir, 0777, true);
            }

            $files = $sftp->nlist($remoteDir);
            if ($files === false) {
                throw new Exception('Failed to list files in ' . $remoteDir);
            }

            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $remoteFile = "$remoteDir/$file";
                $localFile = "$localDir/$file";

                if ($sftp->is_dir($remoteFile)) {
                    $downloadDirectory($sftp, $remoteFile, $localFile);
                } else {
                    if (!$sftp->get($remoteFile, $localFile)) {
                        throw new Exception("Failed to download $remoteFile to $localFile");
                    }
                }
            }
        };

        // Check if the remote directory exists
        if ($sftp->is_dir($remote_directory)) {
            // Extract year, month, and day from the date
            $dateParts = explode('/', $formattedDate);
            $year = $dateParts[0];
            $month = $dateParts[1];
            $day = $dateParts[2];

            // Create the local directory structure with campaign prefix
            $local_subdir = $local_directory . DIRECTORY_SEPARATOR . $campaign . DIRECTORY_SEPARATOR . $year . DIRECTORY_SEPARATOR . $month . DIRECTORY_SEPARATOR . $day;
            $downloadDirectory($sftp, $remote_directory, $local_subdir);

            echo "Files downloaded successfully for $formattedDate.";
        } else {
            echo "Directory $remote_directory does not exist on the SFTP server.";
        }
    }
}
