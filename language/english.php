<?php
// +---------------------------------------------------------------------------+
// | DokuWiki Integration to glFusion CMS                                      |
// +---------------------------------------------------------------------------+
// | This is the English language page for the DokuWiki Integration Plugin     |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2006-2015 by the following authors:                         |
// |                                                                           |
// | Mark R. Evans              - mark AT glfusion DOT org                     |
// +---------------------------------------------------------------------------|
// |                                                                           |
// | If you translate this file, please consider uploading a copy at           |
// |    http://www.glfusion.org so others can benefit from your                |
// |    translation.  Thank you!                                               |
// |                                                                           |
// | This program is free software; you can redistribute it and/or             |
// | modify it under the terms of the GNU General Public License               |
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
// | GNU General Public License for more details.                              |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software               |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA|
// |                                                                           |
// +---------------------------------------------------------------------------|

if (!defined ('VERSION')) {
    die ('This file can not be used on its own.');
}

$LANG_DW00 = array (
    'menulabel'         => 'DokuWiki',
    'plugin'            => 'DokuWiki',
    'installdoc'        => "For detailed installation instructions, please see the <a href=\"http://www.geeklog.net/wiki/doku.php?id=dokuwikiplugin:start\">DokuWiki Integration Plugin Documentation</a>",
    'readme'            => 'DokuWiki Integration Plugin Installation',
    'overview'          => 'DokuWiki is a full featured wiki system.  This plugin provides an integration with Geeklog\'s user authentication system and other Geeklog features.<br /><br />DokuWiki uses ordinary files for the storage of wiki pages and other information associated with those pages (e.g. images, search indexes, old revisions, etc). In order to operate successfully DokuWiki must have write access to the directories that hold those files. This installer is not capable of setting up directory permissions. That normally needs to be done directly on a command shell or if you are using hosting, through FTP or your hosting control panel (e.g. cPanel).',
    'install_header'    => 'DokuWiki Integration Install/Uninstall',
    'install_success'   => 'DokuWiki Installation Successful.',
    'install_failed'    => 'Installation Failed -- See your error log for additional details.',
    'uninstall_msg'     => 'Plugin Successfully Uninstalled',
    'install'           => 'Install',
    'uninstall'         => 'Uninstall',
    'readme'            => 'DokuWiki Integration Plugin Installation',
    'no_search_results' => 'No items were found to match your search criteria.',
    'whats_new_prompt'  => 'DokuWiki',
    'whats_new_time'    => 'last %s days',
    'no_whats_new'      => 'No new items',
    'desc_wiki'         => 'Link: to a wiki page. link_text defaults to the page name. Usage: [wiki:pagename {link_text}]',
    'admin'             => 'DokuWiki Administration - Set access controls on pages, modify DokuWiki plugins, and change the DokuWiki configuration settings.',
);

// Localization of the Admin Configuration UI
$LANG_configsections['dokuwiki'] = array(
    'label'                 => 'DokuWiki',
    'title'                 => 'DokuWiki Configuration'
);
$LANG_confignames['dokuwiki'] = array(
    'enable_whats_new'      => 'Enable What\'s New Block Support',
    'whats_new_days'        => 'Number of days to include',
    'whatsnew_length'       => 'Maximum length of what\'s new entry',
    'loginrequired'         => 'Login Required to view wiki',
    'restrict_to_group'     => 'Restrict Access to this group',
    'disable_search_integration'    => 'Disable Geeklog Search Integration',
    'displayblocks'         => 'Blocks to display',
    'public_dir'            => 'Directory where wiki resides'
);

$LANG_configsubgroups['dokuwiki'] = array(
    'sg_main'               => 'Configuration Settings'
);

$LANG_fs['dokuwiki'] = array(
    'dw_public'                 => 'General Settings',
    'dw_integration'            => 'DokuWiki Integration',
);
// Note: entries 0, 1, and 12 are the same as in $LANG_configselects['Core']
$LANG_configselects['dokuwiki'] = array(
    0 => array('True' => 1, 'False' => 0),
    1 => array('Left Navigation Only' => 0, 'Right Navigation Only' => 1, 'Display both Left and Right' => 2, 'None' => 3)
);
$PLG_dokuwiki_MESSAGE1 = 'DokuWiki Integration plugin upgrade: Update completed successfully.';
$PLG_dokuwiki_MESSAGE2 = 'DokuWiki Integration plugin upgrade: We are unable to update this version automatically. Refer to the plugin documentation.';
$PLG_dokuwiki_MESSAGE3 = 'DokuWiki Integration plugin upgrade failed - check error.log';
?>