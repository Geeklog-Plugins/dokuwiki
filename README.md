## DokuWiki Integration Plugin for Geeklog CMS
Version: 1.9.0 (based on glFusion DokuWiki plugin v4.0.1)

Integration Developed by: Mark R. Evans - http://www.glfusion.org

Converted for use with Geeklog by: Tom Homer - https://www.geeklog.net

> **DokuWiki requires that your Geeklog site use UTF-8 encoding.  If your site is not configured for UTF-8 encoding, do not install this plugin.**

### OVERVIEW

DokuWiki is a standards compliant, simple to use Wiki, mainly aimed at creating documentation of any kind. It is targeted at developer teams, work groups and small companies. It has a simple but powerful syntax which makes sure the data files  remain readable outside the Wiki and eases the creation of structured texts. All data is stored in plain text files - no database is required.

The DokuWiki Integration Plugin for Geeklog provides an integration with your Geeklog v2.1.1+ website. This plugin is based on DokuWiki 2016-06-26a "Elenor of Tsort", the latest release available at the time of publishing this plugin release.

This plugin will provide the following features:

- Integrated user authentication - All Geeklog users will be mapped to DokuWiki. There will be no need to have DokuWiki provide any user administration.
- DokuWiki's contents will be searched using Geeklog's Search and Advanced Search.
- Geeklog's administrators (members of the Root group) will have administrative capabilities in DokuWiki.
- DokuWiki will be integrated into the Geeklog site's layout (this is accomplished by using a custom DokuWiki skin).

### SYSTEM REQUIREMENTS

DokuWiki has the following system requirements:

- PHP 5.3.3 and higher.
- PHP's GD extension for use with libGD 2 (a graphics library) is recommended but not needed
- DokuWiki should work in PHP's Safe Mode, depending on your hosts configuration you may need to use the safemodehack option
- DokuWiki is designed to run with PHP's Option register_globals set to off.

## READ THIS IMPORTANT NOTICE!

> DokuWiki requires that your Geeklog site use UTF-8 encoding.  **If your site is not configured for UTF-8 encoding, do not install this plugin.**

> DokuWiki must have write permissions to the public_html/dokuwiki/data/ directory and all directories below it. The plugin installer will check these permissions, please ensure they are writable by your web server.

This integration is based on Geeklog v2.1.1 and it will not work with older versions of Geeklog.

This plugin includes all the required DokuWiki distribution files, so there is no need to download the standalone DokuWiki release. This plugin is based on DokuWiki 2016-06-26a "Elenor of Tsort" which is the latest release at the time of this writing.

Several internal DokuWiki files have been modified to make DokuWiki work as a plugin to Geeklog.

### INSTALLATION

The DokuWiki Integration Plugin uses the Geeklog automated plugin installer. Simply upload the distribution using the Geeklog plugin installer located in the **Plugin Administration** page.

Once you have the files loaded onto your web server, you will need to rename or copy the following .dist files to their corresponding .php file:

- public_html/conf/acl.auth.php.dist to acl.auth.php
- public_html/conf/local.php.dist to local.php

Once you have renamed the distribution files (new installs only, upgrades do not need to copy these files), you can now tell Geeklog to install the plugin. Go to the Plugin Administration screen and select the install icon from the plugin list. 

DokuWiki must have write permissions to the public_html/dokuwiki/data/ and public_html/dokuwiki/conf/ directories and all directories below it. DokuWiki will check for these permissions, please ensure they are writable by your web server. 

### Upgrading DokuWiki

The upgrade process is identical to the installation process, simply upload the distribution from the Plugin Administration page. All your wiki pages will be left as they are since they will not be copied over.

Also, once the upgrade is complete, you should go into the DokuWiki admin link and double check your configuration options.  There may be a couple of new options you need to set.

### Configuring DokuWiki

There are two areas of DokuWiki configuration:
 - Geeklog Integration Options
 - DokuWiki core configuration options

#### Geeklog Integration Options

All Geeklog Integration's are completed in the Geeklog Configuration Manager.  The following options are available for DokuWiki:

 Option           | Description
 -----------------|------------
What's New Days   | Set this to the number of days that will be included in the What's New block.
What's New Length | Set this to the maximum number of characters wide you will allow listings in the What's New block. Truncated items will have a … appended to the end.
Login Required    | Set this to TRUE to require a user to be logged into the site before granting access to DokuWiki. Set to 0 to allow non-logged in users access.
Restrict to Group | If you want only allow access to DokuWiki for a specific group, place that group name in the input field. Leave the entry blank to allow all groups access to DokuWiki. DokuWiki does not support spaces or _ (underscores) in the Group names. So these items are stripped from the Geeklog groups. For example, the Geeklog group Logged-in Users will become LoggedinUsers. 
Disable Search Integration | Set this to TRUE to disable search integration with Geeklog's main search feature. Set to FALSE to allow DokuWiki results to be displayed in Geeklog's main search.
Geeklog Left / Right Blocks  | Select whether the left / right / both / or none display.
Public Directory  | If you would like to rename the directory where DokuWiki resides inside Geeklog's public_html/ directory, change the name here.

#### DokuWiki Configuration

All DokuWiki configuration options are available by selecting the DokuWiki icon from the Admin Menu or the Command and Control Screen.  For a full set of details on each configuration option, please reference the https://www.dokuwiki.org/config

There are a few protected DokuWiki configuration options that cannot be edited:

- User Manager is disabled by the plugin - user management is done in Geeklog
- The following Configuration Management fields cannot be edited
  - Use access control lists
  - Autogenerate passwords
  - Authentication backend
  - Password encryption method
  - Superuser

All other DokuWiki configuration settings can be customized to meet your specific needs. For full details on each configuration option, please see https://www.dokuwiki.org/config

### DokuWiki Templates

DokuWiki's layout can be customized through templates (aka. skins). The DokuWiki Integration Plugin must use a customized DokuWiki template to properly integrate the look and feel of the Geeklog site. Included with the plugin are two customized skins; glfusion and dokuwiki.  glfusion is the original skin used by DokuWiki until late 2012.  The **dokuwiki** skin is a customized version of the standard DokuWiki theme that works with Geeklog.

### Using DokuWiki

For all usage questions and documentation on how to utilize DokuWiki, please refer to the DokuWiki Web Site at https://www.dokuwiki.org/wiki:dokuwiki