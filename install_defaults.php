<?php
/**
* glFusion CMS
*
* DokuWiki Integration
*
* @license GNU General Public License version 2 or later
*     http://www.opensource.org/licenses/gpl-license.php
*
*  Copyright (C) 2006-2015 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*/

if (!defined ('VERSION')) {
    die ('This file can not be used on its own.');
}

/*
 * DokuWiki default settings
 *
 * Initial Installation Defaults used when loading the online configuration
 * records. These settings are only used during the initial installation
 * and not referenced any more once the plugin is installed
 *
 */

global $_DW_DEFAULT;
$_DW_DEFAULT = array();

$_DW_DEFAULT['enable_whats_new']            = 1;
$_DW_DEFAULT['whats_new_days']              = 14;
$_DW_DEFAULT['whatsnew_length']             = 24;
$_DW_DEFAULT['loginrequired']               = 0;
$_DW_DEFAULT['restrict_to_group']           = '';
$_DW_DEFAULT['disable_search_integration']  = 0;
$_DW_DEFAULT['displayblocks']               = 0;
$_DW_DEFAULT['public_dir']			        = '/dokuwiki/';


/**
* the dokuwiki plugin's config array
*/
global $_DW_CONF;
$_DW_CONF = array();

/**
* Initialize DokuWiki plugin configuration
*
* Creates the database entries for the configuation if they don't already
* exist. Initial values will be taken from $_DW_CONF if available (e.g. from
* an old config.php), uses $_DW_DEFAULT otherwise.
*
* @return   boolean     true: success; false: an error occurred
*
*/
function plugin_initconfig_dokuwiki()
{
    global $_DW_CONF, $_DW_DEFAULT;

    if (is_array($_DW_CONF) && (count($_DW_CONF) > 1)) {
        $_DW_DEFAULT = array_merge($_DW_DEFAULT, $_DW_CONF);
    }
    $c = config::get_instance();
    if (!$c->group_exists('dokuwiki')) {

        $c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, true, 'dokuwiki');
        $c->add('dw_public', NULL, 'fieldset', 0, 0, NULL, 0, true, 'dokuwiki');

        $c->add('enable_whats_new', $_DW_DEFAULT['enable_whats_new'], 'select',0, 0, 0, 10, true, 'dokuwiki');
        $c->add('whats_new_days', $_DW_DEFAULT['whats_new_days'],'text',0, 0, 0, 20, true, 'dokuwiki');
        $c->add('whatsnew_length', $_DW_DEFAULT['whatsnew_length'],'text',0, 0, 0, 30, true, 'dokuwiki');
        $c->add('loginrequired', $_DW_DEFAULT['loginrequired'], 'select',0, 0, 0, 40, true, 'dokuwiki');
        $c->add('restrict_to_group', $_DW_DEFAULT['restrict_to_group'],'text',0, 0, 0, 50, true, 'dokuwiki');
        $c->add('disable_search_integration', $_DW_DEFAULT['disable_search_integration'], 'select',0, 0, 0, 60, true, 'dokuwiki');
        $c->add('displayblocks', $_DW_DEFAULT['displayblocks'], 'select',0, 0, 1, 70, true, 'dokuwiki');
        $c->add('public_dir', $_DW_DEFAULT['public_dir'],'text',0, 0, 0, 80, true, 'dokuwiki');
    }

    return true;
}
?>