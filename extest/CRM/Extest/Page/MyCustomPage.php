<?php

use CRM_Extest_ExtensionUtil as E;

class CRM_Extest_Page_MyCustomPage extends CRM_Core_Page {

  public function run() {
    // Set the page title
    CRM_Utils_System::setTitle(E::ts('Fetch Recordings'));

    // Check if the form is submitted
    if (!empty($_POST['fetch_files']) && !empty($_POST['start_date']) && !empty($_POST['end_date']) && !empty($_POST['campaign'])) {
      $startDate = $_POST['start_date'];
      $endDate = $_POST['end_date'];
      $campaign = $_POST['campaign'];

      // Fetch the recordings for the specified date range and campaign
      try {
          extest_fetch_files($campaign, $startDate, $endDate);
          CRM_Core_Session::setStatus('Files downloaded successfully for dates from ' . $startDate . ' to ' . $endDate . ' for campaign ' . strtoupper($campaign), 'Success', 'success');
      } catch (Exception $e) {
          CRM_Core_Session::setStatus('Error: ' . $e->getMessage(), 'Error', 'error');
      }
    }

    parent::run();
  }

}
