<?php

// phpcs:disable
/*
+--------------------------------------------------------------------------+
| Copyright IT Bliss LLC (c) 2013                                          |
+--------------------------------------------------------------------------+
| This program is free software: you can redistribute it and/or modify     |
| it under the terms of the GNU Affero General Public License as published |
| by the Free Software Foundation, either version 3 of the License, or     |
| (at your option) any later version.                                      |
|                                                                          |
| This program is distributed in the hope that it will be useful,          |
| but WITHOUT ANY WARRANTY; without even the implied warranty of           |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
| GNU Affero General Public License for more details.                      |
|                                                                          |
| You should have received a copy of the GNU Affero General Public License |
| along with this program.  If not, see <http://www.gnu.org/licenses/>.    |
+--------------------------------------------------------------------------+
*/
// phpcs:enable

require_once 'apikey.civix.php';
use CRM_Apikey_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 */
function apikey_civicrm_config(&$config) {
  _apikey_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 */
function apikey_civicrm_install() {
  return _apikey_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 */
function apikey_civicrm_enable() {
  return _apikey_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_tabset().
 */
function apikey_civicrm_tabset($tabsetName, &$tabs, $context) {
  if ($tabsetName == 'civicrm/contact/view') {
    $contactID = $context['contact_id'];
    $isAdmin = CRM_Core_Permission::check([['administer CiviCRM', 'edit all contacts']]);
    $canEdit = CRM_Core_Permission::check(['edit own API key', 'edit all API keys']);
    $canView = CRM_Core_Permission::check(['view own API key', 'view all API keys']);
    if ($isAdmin || $canEdit || $canView) {
      $url = CRM_Utils_System::url('civicrm/contact/view/apikey', "reset=1&cid={$contactID}&snippet=1");
      $tabs[] = [
        'id' => 'apiKey',
        'url' => $url,
        'title' => E::ts('API Key'),
        'weight' => 300,
        'icon' => 'crm-i fa-key',
        'contact_type' => ['Individual'],
        'count' => $contactID && CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_Contact', $contactID, 'api_key') ? 1 : 0,
      ];
    }
  }
}

/**
 * Implements hook_civicrm_permission().
 */
function apikey_civicrm_permission(&$permissions, &$all_permissions = NULL) {
  $prefix = E::ts('APIKey') . ': ';
  $permissions += [
    'view own API key' => [
      'label' => $prefix . E::ts('View own API key'),
      'description' => E::ts('Allows contacts to view their own API key on their contact summary screen/record.'),
    ],
    'edit own API key' => [
      'label' => $prefix . E::ts('Edit own API key'),
      'description' => E::ts('Allows contacts to edit their own API key on their contact summary screen/record'),
    ],
    'view site key' => [
      'label' => $prefix . E::ts('View site key'),
      'description' => E::ts('Allows contacts to view the site key on their contact summary screen/record.'),
    ],
    'view all API keys' => [
      'label' => $prefix . E::ts('View all API keys'),
      'description' => E::ts('Allows users to view API keys for all contacts.'),
    ],
    'edit all API keys' => [
      'label' => $prefix . E::ts('Edit all API keys'),
      'description' => E::ts('Allows users to edit API keys for all contacts.'),
    ],
  ];
}
