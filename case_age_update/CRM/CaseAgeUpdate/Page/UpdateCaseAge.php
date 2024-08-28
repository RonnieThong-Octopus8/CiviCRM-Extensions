<?php

use CRM_CaseAgeUpdate_ExtensionUtil as E;

class CRM_CaseAgeUpdate_Page_UpdateCaseAge extends CRM_Core_Page {

  public function run() {
    // Set the page title
    CRM_Utils_System::setTitle(E::ts('Update Case Age'));

    // Assign the current time for display in the template
    $this->assign('currentTime', date('Y-m-d H:i:s'));

    // Check if the button was clicked
    if (isset($_POST['update_case_age'])) {
      $this->updateCaseAge();
    }

    // Display the template
    parent::run();
  }

  /**
   * Function to update the case age for all cases.
   */
  public function updateCaseAge() {
    // Fetch all cases
    $cases = civicrm_api3('Case', 'get', [
      'return' => ['id', 'start_date'],
      'options' => ['limit' => 0], // Ensure we fetch all cases
    ]);

    // Iterate over all cases and calculate the case age
    foreach ($cases['values'] as $case) {
      $caseId = $case['id'];
      $startDate = new DateTime($case['start_date']);
      $today = new DateTime();
      $interval = $startDate->diff($today);
      $caseAge = $interval->days;

      // Format the case age as "X day(s)"
      $formattedCaseAge = $caseAge . ' ' . ($caseAge == 1 ? 'day' : 'days');

      // Update the custom field with the formatted case age
      civicrm_api3('CustomValue', 'create', [
        'entity_id' => $caseId,
        'custom_2' => $formattedCaseAge,  // Replace with the correct custom field ID (2 in this case)
      ]);
    }

    // Add a status message to confirm the update
    CRM_Core_Session::setStatus(E::ts('Case age has been updated for all cases.'), E::ts('Success'), 'success');
  }
}
