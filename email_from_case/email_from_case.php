<?php

require_once 'email_from_case.civix.php';

use CRM_EmailFromCase_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function email_from_case_civicrm_config(&$config): void {
  _email_from_case_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function email_from_case_civicrm_install(): void {
  _email_from_case_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function email_from_case_civicrm_enable(): void {
  _email_from_case_civix_civicrm_enable();
}

// Register the extension
function civicrm_case_email_info() {
  return array(
      'title' => 'CiviCRM Case Email',
      'version' => '1.0',
      'author' => 'Your Name',
      'email' => 'your.email@example.com',
      'url' => 'https://example.com',
      'description' => 'Sends an email when a case of a specific type is created.',
  );
}

// Hook into CiviCRM events
function civicrm_case_email_hook($op, $objectName, $objectId, &$objectRef) {
  if ($op === 'create' && $objectName === 'Case') {
    CRM_Core_Error::debug_log_message("Detected case creation");
    send_case_email($objectId);
  }
}

function send_case_email($caseId) {
  CRM_Core_Error::debug_log_message("send_case_email: Start processing case ID {$caseId}");

  try {
    // Get case details dynamically using the case ID
    $caseDetails = civicrm_api4('Case', 'get', [
      'select' => ['subject', 'created_date', 'case_type_id:label', 'status_id:label', 'start_date', 'details'],
      'where' => [['id', '=', $caseId]],
      'limit' => 1,
      'checkPermissions' => TRUE,
    ]);

    if (empty($caseDetails[0])) {
      CRM_Core_Error::debug_log_message("send_case_email: No case details found for case ID {$caseId}");
      return;
    }

    $caseDetails = $caseDetails[0];
    $subject = "Case: " . $caseDetails['subject'];
    $dateCreated = $caseDetails['created_date'];
    $caseType = $caseDetails['case_type_id:label'];
    $caseStatus = $caseDetails['status_id:label'];
    $caseStartDate = $caseDetails['start_date'];
    $caseDetailsText = !empty($caseDetails['details']) ? $caseDetails['details'] : 'No details provided';

    // Construct the case view link
    $caseLink = "https://demo.cxmconnect.com/wp-admin/admin.php?page=CiviCRM&q=civicrm%2Fcontact%2Fview%2Fcase&reset=1&action=view&cid=6&id={$caseId}";

    // Initialize the email body
    $bodyText = "<p>Dear Team,</p>";
    $bodyText .= "<p>A new case has been submitted with the following details:</p>";
    $bodyText .= "<p><strong>Case ID:</strong> {$caseId}</p>";  // Including Case ID
    $bodyText .= "<p><strong>Date and time:</strong> $dateCreated</p>";
    $bodyText .= "<p><strong>Case Type:</strong> $caseType</p>";
    $bodyText .= "<p><strong>Case Status:</strong> $caseStatus</p>";
    $bodyText .= "<p><strong>Case Start Date:</strong> $caseStartDate</p>";
    $bodyText .= "<p><strong>Details:</strong> $caseDetailsText</p>";

    // Dynamically include custom fields and add "Issue: " in front
    $customFields = civicrm_api3('CustomValue', 'get', [
      'entity_id' => $caseId,
      'entity_table' => 'civicrm_case',
      'sequential' => 1,
    ]);
    if (!empty($customFields['values'])) {
      foreach ($customFields['values'] as $field) {
        $fieldLabel = $field['label'];
        $fieldValue = $field['latest'];
        $bodyText .= "<p><strong>Issue:</strong> $fieldLabel - $fieldValue</p>";
      }
    }

    // Add the case view link after the issues
    $bodyText .= "<p>Please refer to the following link to view the case:</p>";
    $bodyText .= "<p><a href='{$caseLink}'>{$caseLink}</a></p>";

    $bodyText .= "<p>Kind Regards,<br>Octopus8</p>";

    // Send email only to ronniethong@octopus8.com
    $params = [
      'toName' => 'Ronnie Thong',
      'toEmail' => 'ronniethong@octopus8.com',
      'from' => 'ronniethong@octopus8.com', // Using ronniethong@octopus8.com as the sender email
      'subject' => $subject,
      'html' => $bodyText,
    ];

    // Log email parameters
    CRM_Core_Error::debug_log_message("send_case_email: Email parameters - " . print_r($params, true));

    // Send email using the Mail API
    $result = CRM_Utils_Mail::send($params);

    // Log result of email send
    CRM_Core_Error::debug_log_message("send_case_email: Email send result - " . print_r($result, true));
  } catch (Exception $e) {
    // Log any exceptions
    CRM_Core_Error::debug_log_message("send_case_email: Exception - " . $e->getMessage());
  }

  CRM_Core_Error::debug_log_message("send_case_email: End processing case ID {$caseId}");
}

// Remove this line
// add_action('civicrm_post', 'civicrm_case_email_hook', 10, 4);

// Instead, use CiviCRM's hook registration
function email_from_case_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  if ($op === 'create' && $objectName === 'Case') {
    CRM_Core_Error::debug_log_message("Detected case creation");
    send_case_email($objectId);
  }
}

// Register the hook
function email_from_case_civicrm_pageRun(&$page) {
  // Additional registration if needed
}

