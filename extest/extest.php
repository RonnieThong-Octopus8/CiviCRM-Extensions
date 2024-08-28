<?php

require_once 'extest.civix.php';
require_once __DIR__ . '/vendor/autoload.php'; // Include Composer's autoload file
require_once 'extest.sftp.php';

use CRM_Extest_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function extest_civicrm_config(&$config): void {
    _extest_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function extest_civicrm_install(): void {
    _extest_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function extest_civicrm_enable(): void {
    _extest_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
function extest_civicrm_navigationMenu(&$menu) {
    _extest_civix_insert_navigation_menu($menu, 'Administer', [
        'label' => 'Fetch Recordings',
        'name' => 'fetch_recordings',
        'url' => 'civicrm/fetchrecordings',
        'permission' => 'administer CiviCRM',
        'operator' => NULL,
        'separator' => 0,
    ]);
    _extest_civix_navigationMenu($menu);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function extest_civicrm_xmlMenu(&$files) {
    _extest_civix_civicrm_xmlMenu($files);
    $files[] = __DIR__ . "/xml/Menu/extest.xml";
}
