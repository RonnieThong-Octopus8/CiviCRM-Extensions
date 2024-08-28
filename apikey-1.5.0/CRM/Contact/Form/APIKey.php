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

use CRM_Apikey_ExtensionUtil as E;

class CRM_Contact_Form_APIKey extends CRM_Core_Form {

  function preProcess() {
    $isAdmin = CRM_Core_Permission::check([['administer CiviCRM', 'edit all contacts']]);
    $canEdit = CRM_Core_Permission::check([['edit own API key', 'edit all API keys']]);
    if (!($isAdmin || $canEdit)) {
      CRM_Core_Error::statusBounce(E::ts('You do not have permission to edit API keys for this contact.'));
    }
    $this->_action = CRM_Utils_Request::retrieve('action', 'String', $this, FALSE, 'add');
    $this->_contactId = CRM_Utils_Request::retrieve('cid', 'Positive', $this, TRUE);
    $this->_apiKey = NULL;
    if ($this->_contactId) {
      $this->_apiKey = CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_Contact', $this->_contactId, 'api_key');
    }
  }

  function buildQuickForm() {
    $this->applyFilter('__ALL__', 'trim');
    $this->add(
      'text',
      'api_key',
      E::ts('API Key'),
      ['size' => "32", 'maxlength' => "32"]
    );
    $buttons = [
      [
        'type' => 'upload',
        'name' => E::ts('Save'),
        'subName' => 'view',
        'isDefault' => TRUE,
      ],
      [
        'type' => 'cancel',
        'name' => E::ts('Cancel'),
      ],
    ];
    $this->addButtons($buttons);
  }

  /**
   * This function sets the default values for the form.
   * 
   * Note: In edit/view mode the default values are retrieved from the database.
   *
   * @access public
   *
   * @return None
   */
  function setDefaultValues() {
    return ['api_key' => $this->_apiKey];
  }

  /**
   * Form submission of new/edit api is processed.
   *
   * @access public
   *
   * @return None
   */
  public function postProcess() {
    //Get the submitted values in an array.
    $params = $this->controller->exportValues($this->_name);
    if (!empty($this->_contactId)) {
      CRM_Core_DAO::setFieldValue('CRM_Contact_DAO_Contact', $this->_contactId, 'api_key', $params['api_key']);
    }

    if (!empty($params['api_key'])) {
      CRM_Core_Session::setStatus("This API key has been updated.");
    } else {
      CRM_Core_Session::setStatus("This API key has been deleted.");
    }
    $url = CRM_Utils_System::url('civicrm/contact/view', "reset=1&cid={$this->_contactId}");
    CRM_Core_Session::singleton()->pushUserContext($url);
  }

}
