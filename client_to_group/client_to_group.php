<?php

require_once 'client_to_group.civix.php';

use CRM_ClientToGroup_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 */
function client_to_group_civicrm_config(&$config): void {
    _client_to_group_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 */
function client_to_group_civicrm_install(): void {
    _client_to_group_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 */
function client_to_group_civicrm_enable(): void {
    _client_to_group_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 */
function client_to_group_civicrm_disable(): void {
    _client_to_group_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_uninstall().
 */
function client_to_group_civicrm_uninstall(): void {
    _client_to_group_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_post().
 *
 * This hook is triggered after a contact is created or updated.
 *
 * @param string $op
 *   The type of operation being performed; usually "create" or "edit".
 * @param string $objectName
 *   The name of the object being created or edited (e.g., "Individual", "Contact").
 * @param int $objectId
 *   The ID of the object being created or edited.
 * @param object $objectRef
 *   The object that is being created or edited.
 */
function client_to_group_civicrm_post($op, $objectName, $objectId, &$objectRef) {
    // Log the operation details
    error_log("client_to_group_civicrm_post called: op = $op, objectName = $objectName, objectId = $objectId");

    // Only proceed if a contact was created or edited.
    if ($objectName == 'Individual' && ($op == 'create' || $op == 'edit')) {
        // Check if the contact subtype includes "Client".
        $contact_subtypes = civicrm_api3('Contact', 'getvalue', [
            'return' => 'contact_sub_type',
            'id' => $objectId,
        ]);

        error_log("Contact subtypes retrieved: " . print_r($contact_subtypes, true));

        if (is_array($contact_subtypes) && in_array('Client', $contact_subtypes)) {
            // Assign the contact to the group "Client_Group_3" (group_id = 3).
            $group_result = civicrm_api3('GroupContact', 'create', [
                'contact_id' => $objectId,
                'group_id' => 3,
            ]);
            error_log("Group assignment result: " . print_r($group_result, true));

            // Tag the contact with "CMS" (tag_id = 6).
            $tag_result = civicrm_api3('EntityTag', 'create', [
                'entity_id' => $objectId,
                'tag_id' => 6,
            ]);
            error_log("Tag assignment result: " . print_r($tag_result, true));
        }
    }
}
