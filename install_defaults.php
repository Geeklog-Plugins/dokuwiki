<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | dokuwiki plugin 1.7.1                                                     |
// +---------------------------------------------------------------------------+
// | install_defaults.php                                                      |
// |                                                                           |
// | Initial Installation Defaults used when loading the online configuration  |
// | records. These settings are only used during the initial installation     |
// | and not referenced any more once the plugin is installed.                 |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2008 by the following authors:                              |
// |                                                                           |
// | Authors: Dirk Haun        - dirk AT haun-online DOT de                    |
// +---------------------------------------------------------------------------+
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
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+
//

if (strpos(strtolower($_SERVER['PHP_SELF']), 'install_defaults.php') !== false) {
    die('This file can not be used on its own!');
}

/*
 * dokuwiki default settings
 *
 * Initial Installation Defaults used when loading the online configuration
 * records. These settings are only used during the initial installation
 * and not referenced any more once the plugin is installed
 *
 */
 
/**
*   Default values to be used during plugin installation/upgrade
*   @global array $_DW_DEFAULT
*/
global $_DB_table_prefix, $_DW_DEFAULT, $LANG_DW00;

$_DW_DEFAULT = array();

/*
 * Menu label
 *
 */

$_DW_DEFAULT['menulabel']           = 'Wiki';

/*
 * Enable What's New
 *
 * If this is set to 1, DokuWiki will be integrated into the geeklog
 * What's New block. Set to 0 to disable integration.
 */

$_DW_DEFAULT['enable_whats_new']           = 1;

/*
 * What's New Days
 *
 * Set this to the number of days that will be included in the What's
 * New block.
 */

$_DW_DEFAULT['whats_new_days']             = 14;

/*
 * What's New Length
 *
 * Set this to the maximum number of characters wide you will allow
 * listings in the What's New block. Truncated items will have a ...
 * appended to the end.
 */

$_DW_DEFAULT['whatsnew_length']            = 24;

/*
 * Login Required
 *
 * Set this to 1 to require a user to be logged into the site
 * before granting access to DokuWiki. Set to 0 to allow non-logged in
 * users access.
 */

$_DW_DEFAULT['loginrequired']              = 0;

/*
 * Restrict to Group
 *
 * If you want only allow access to DokuWiki for a specific group,
 * place that group name in the ''.  Leave the entry as '' to allow
 * all groups access to DokuWiki.
 */

$_DW_DEFAULT['restrict_to_group']          = '';

/*
 * Disable Search Integration
 *
 * Set this to 1 to disable search integration with geeklog's main
 * search feature.  Set to 0 to allow DokuWiki results to be displayed
 * in geeklog's main search.
 */

$_DW_DEFAULT['disable_search_integration'] = 0;

/*
 * Public Dir
 *
 * If you would like to rename the directory where DokuWiki resides
 * inside geeklog's public_html/ directory, change the name here.
 */

$_DW_DEFAULT['public_dir']			= '/dokuwiki/';

/*
 * geeklog blocks to display (navigation - left / extra - right)
 *
 * Select whether the left / right / both / or none display.
 *
 * 0 -> Display Left navigation only
 * 1 -> Display Right navigation only
 * 2 -> Display both Left and Right
 * 3 -> None
 */

$_DW_DEFAULT['displayblocks'] = 0;

/**
* Initialize dokuwiki plugin configuration
*
* Creates the database entries for the configuation if they don't already
* exist. 
*
* @return   boolean     true: success; false: an error occurred
*
*/
function plugin_initconfig_dokuwiki()
{
    global $_CONF, $_DW_DEFAULT;
	
    $c = config::get_instance();
    if (!$c->group_exists('dokuwiki')) {

        //This is main subgroup #0
		$c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, true, 'dokuwiki');
		
		//Main settings   
		$c->add('fs_main', NULL, 'fieldset', 0, 0, NULL, 0, true, 'dokuwiki');
		$c->add('menulabel', $_DW_DEFAULT['menulabel'],
                'text', 0, 0, 0, 1, true, 'dokuwiki');
        $c->add('enable_whats_new', $_DW_DEFAULT['enable_whats_new'],
                'select', 0, 0, 3, 2, true, 'dokuwiki');
		$c->add('whats_new_days', $_DW_DEFAULT['whats_new_days'],
                'text', 0, 0, 0, 3, true, 'dokuwiki');
		$c->add('whatsnew_length', $_DW_DEFAULT['whatsnew_length'],
                'text', 0, 0, 0, 4, true, 'dokuwiki');
		$c->add('loginrequired', $_DW_DEFAULT['loginrequired'],
                'select', 0, 0, 3, 5, true, 'dokuwiki');
		$c->add('restrict_to_group', $_DW_DEFAULT['restrict_to_group'],
                'text', 0, 0, 0, 6, true, 'dokuwiki');
		$c->add('disable_search_integration', $_DW_DEFAULT['disable_search_integration'],
                'select', 0, 0, 3, 7, true, 'dokuwiki');
		$c->add('public_dir', $_DW_DEFAULT['public_dir'],
                'text', 0, 0, 0, 8, true, 'dokuwiki');
		$c->add('displayblocks', $_DW_DEFAULT['displayblocks'],
                'select', 0, 0, 20, 9, true, 'dokuwiki');
	}

    return true;
}

?>