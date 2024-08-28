# Auto Create Contact Extension

## Overview

The Auto Create Contact extension for CiviCRM automatically creates contact records when specific conditions are met. This extension is designed to reduce manual data entry, ensuring consistency and efficiency in your contact management processes.

## Features

* Automated Contact Creation: Automatically create a new contact when a predefined condition is triggered, such as when new interaction data is logged or during data imports.

* Customizable Conditions: Define the specific conditions under which a contact should be created. This can be based on various triggers like form submissions, data imports, or other CRM activities.

* Consistent Data Management: Ensures that your contact database remains up-to-date with minimal manual intervention, reducing the risk of errors and duplicate entries.

## Installation Guide

### Prerequisites

Before installing the Auto Create Contact extension, ensure you have the following:

* CiviCRM: A running instance of CiviCRM on your CMS (e.g., WordPress, Drupal, Joomla).

* Admin Access: Administrative privileges to install and configure extensions within CiviCRM.

### Installation Steps

* Download the Extension: You can download the extension directly from the CiviCRM Extensions directory or clone it from the repository. * If cloning from a repository, navigate to your CiviCRM extensions directory:
```
cd /path/to/civicrm/extensions
git clone https://github.com/yourusername/auto_create_contact.git
```
* Enable the Extension: * Log in to your CiviCRM instance as an administrator. * Navigate to Administer > System Settings > Extensions. * Locate the Auto Create Contact extension and click "Install".

* Configure the Extension: * After installation, go to Administer > Customize Data and Screens > Auto Create Contact. * Define the conditions under which new contacts should be created. This could be based on specific form submissions, data imports, or other triggers within CiviCRM. * Set any additional configuration options as required.

## Usage Guide

Once the Auto Create Contact extension is installed and configured:

* Trigger Contact Creation: The extension will monitor the defined conditions. When a condition is met, such as when new interaction data is logged or during data imports, a new contact will be automatically created.

* Review Created Contacts: You can review the newly created contacts in the CiviCRM contact list. These contacts will be created with the data provided by the trigger event.

* Adjust Configuration: If needed, revisit the configuration settings to adjust the conditions under which contacts are created. This can be done via the Auto Create Contact settings page.

## Troubleshooting

### Common Issues and Solutions

* Contacts Not Being Created: Ensure that the conditions you defined are correct and that the extension is enabled.

* Duplicate Contacts: If duplicates are being created, review the conditions to ensure they are not being triggered multiple times for the same event.
