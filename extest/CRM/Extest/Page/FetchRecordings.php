<?php

use CRM_Extest_ExtensionUtil as E;

class CRM_Extest_Page_FetchRecordings extends CRM_Core_Page {

  public function run() {


    // Redirect to the custom page
    CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/mycustompage', 'reset=1'));
    parent::run();
  }

}
