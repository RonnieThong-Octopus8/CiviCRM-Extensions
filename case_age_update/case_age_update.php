<?php

require_once 'case_age_update.civix.php';

use CRM_CaseAgeUpdate_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function case_age_update_civicrm_config(&$config): void {
  _case_age_update_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function case_age_update_civicrm_install(): void {
  _case_age_update_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function case_age_update_civicrm_enable(): void {
  _case_age_update_civix_civicrm_enable();
}
