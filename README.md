# CiviCRM Extensions

## Overview

This repository contains a collection of custom extensions for CiviCRM, designed to enhance the functionality and usability of CiviCRM installations. Each extension addresses specific use cases and can be individually installed and configured within your CiviCRM instance.

## Available Extensions

 1. API Key Management (apikey-1.5.0) Manages API keys for users, allowing secure and controlled access to the CiviCRM API. This extension helps in generating, managing, and revoking API keys for various users or applications.

 2. Auto Create Contact (auto_create_contact) Automatically creates a contact record when certain conditions are met, such as when a new interaction is logged or when data is imported from an external source. This extension reduces manual data entry and ensures consistency.

 3. Case Age Update (case_age_update) Displays the age of open cases in CiviCRM, helping organizations track and prioritize cases based on how long they have been open. The extension uses color coding to visually differentiate case age (e.g., green for within a day, yellow for more than a day, red for more than two days).

 4. Client to Group (client_to_group) Automatically adds newly created contacts of type "Client" to a designated group within CiviCRM. This extension streamlines the organization of contacts and ensures that clients are correctly grouped upon creation.

 5. Email from Case (email_from_case) Enables automatic email notifications based on case activity. When a new case is created or updated, this extension sends an email to predefined recipients with relevant case details, such as case ID and a link to view the case.

 6. Extension Test (extest) A simple extension used for testing and development purposes. It includes basic functionality to demonstrate how extensions can be created, configured, and deployed in CiviCRM.

## Installation Guide

### Prerequisites

**CiviCRM**: Ensure that you have a CiviCRM installation running on your CMS (e.g., WordPress, Drupal, Joomla).
**Access to Admin Interface**: You need administrative access to install and configure extensions.
### Installation Steps

**Clone the Repository**:

ruby
Copy code
git clone https://github.com/yourusername/civicrm-extensions.git /path/to/civicrm/extensions/
**Enable the Extensions**:

Log in to your CiviCRM instance as an administrator.
Navigate to Administer > System Settings > Extensions.
Locate the extensions you wish to enable and click "Install" next to each one.
**Configure the Extensions**:

After installation, configure the extensions as needed. This may involve setting up API keys, defining groups, or setting notification preferences.
For configuration options, navigate to Administer > Customize Data and Screens > CiviCRM Extensions.
## Updating Extensions

### Pull the Latest Changes

If you’ve cloned this repository, you can update your local copy by running:

css
Copy code
git pull origin main
### Reinstall or Refresh the Extension

Navigate to Administer > System Settings > Extensions.
Click "Uninstall" next to the extension you wish to update, then reinstall it.
Alternatively, you can refresh the extension without uninstalling it.
## Contributing

If you’d like to contribute to this repository, please fork the repository, make your changes, and submit a pull request. Contributions to improve functionality, fix bugs, or add new features are always welcome.

## License

This repository is licensed under the MIT License. See the `LICENSE` file for more details.

## Contact

For more information, questions, or support, please contact:

**Your Name**

Ronnie Thong
ronniethong@octopus8.com
