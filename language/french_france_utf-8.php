<?php
// +---------------------------------------------------------------------------+
// | Dokuwiki Plugin 1.7.1                                                     |
// +---------------------------------------------------------------------------+
// | This is the English language page for the DokuWiki Integration Plugin     |
// +---------------------------------------------------------------------------|
// | Copyright (C) 2010 by the following authors:                              |
// |                                                                           |
// | Ben - cordiste AT free DOT fr                                             |
// |                                                                           |
// | Based on the original Dokuwiki Plugin                                     |
// | Copyright (C) 2006-2008 by the following authors:                         |
// | Mark R. Evans - mark AT glfusion DOT org                                  |
// +---------------------------------------------------------------------------|
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

$LANG_DW00 = array (
    'plugin'            => 'DokuWiki',
    'no_search_results' => 'Aucun élément n\'a été trouvé.',
    'whats_new_prompt'  => 'Wiki',
    'whats_new_time'    => 'derniers %s jours',
    'no_whats_new'      => 'Rien de nouveau',
	'search_type'       => 'Page',
	'autotag_desc_wiki' => '[wiki: nom-de-la-page titre alternatif] - Affiche un lien vers une page du wiki en utilisant le nom de la page comme titre. Un titre alternatif peut être spécifié mais n\'est pas nécessaire.',
);
$PLG_dokuwiki_MESSAGE1 = 'DokuWiki Integration plugin upgrade: Update completed successfully.';
$PLG_dokuwiki_MESSAGE2 = 'DokuWiki Integration plugin upgrade: We are unable to update this version automatically. Refer to the plugin documentation.';
$PLG_dokuwiki_MESSAGE3 = 'DokuWiki Integration plugin upgrade failed - check error.log';

/**
*   Localization of the Admin Configuration UI
*   @global array $LANG_configsections['dokuwiki']
*/
$LANG_configsections['dokuwiki'] = array(
    'label' => 'Dokuwiki',
    'title' => 'Dokuwiki Configuration'
);

/**
*   Configuration system prompt strings
*   @global array $LANG_confignames['dokuwiki']
*/
$LANG_confignames['dokuwiki'] = array(
    'menulabel'                   => 'Menu label',
    'enable_whats_new'            => 'Enable what\'s new',
	'whats_new_days'              => 'What\'s new days',
	'whatsnew_length'             => 'What\'s new lengh',
	'loginrequired'               => 'Login require',
	'restrict_to_group'           => 'Retrict access to group',
	'disable_search_integration'  => 'Disable search integration',
	'public_dir'                  => 'Public directory',
	'displayblocks'               => 'Display blocks',
);

/**
*   Configuration system subgroup strings
*   @global array $LANG_configsubgroups['dokuwiki']
*/
$LANG_configsubgroups['dokuwiki'] = array(
    'sg_main' => 'Main Settings',
);

/**
*   Configuration system fieldset names
*   @global array $LANG_fs['dokuwiki']
*/
$LANG_fs['dokuwiki'] = array(
    'fs_main'            => 'General Settings',
 );

/**
*   Configuration system selection strings
*   Note: entries 0, 1, and 12 are the same as in 
*   $LANG_configselects['Core']
*
*   @global array $LANG_configselects['dokuwiki']
*/
$LANG_configselects['dokuwiki'] = array(
    0 => array('True' => 1, 'False' => 0),
    1 => array('True' => TRUE, 'False' => FALSE),
    3 => array('Yes' => 1, 'No' => 0),
    4 => array('On' => 1, 'Off' => 0),
    12 => array('No access' => 0, 'Read-Only' => 2, 'Read-Write' => 3),
    20 => array('Left only' => 0, 'Right only' => 1, 'Both, left and right' => 2, 'None' => 3),
);
?>