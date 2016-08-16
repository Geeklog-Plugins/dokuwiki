<?php
// +---------------------------------------------------------------------------+
// | Dokuwiki Plugin 1.8.0                                                     |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// |                                                                           |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010 by the following authors:                              |
// |                                                                           |
// | Ben - ben AT geeklog DOT fr                                               |
// |                                                                           |
// | Based on the original Dokuwiki Plugin                                     |
// | Copyright (C) 2006-2008 by the following authors:                         |
// | Mark R. Evans - mark AT glfusion DOT org                                  |
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

// this file can't be used on its own
if (strpos ($_SERVER['PHP_SELF'], 'functions.inc') !== false)
{
    die ('This file can not be used on its own.');
}

/**
* Language file Include
*/
$langfile = $_CONF['path'] . 'plugins/dokuwiki/language/' . $_CONF['language'] . '.php';

if (file_exists ($langfile)) {
    include_once ($langfile);
} else {
    include_once ($_CONF['path'] . 'plugins/dokuwiki/language/english.php');
}

/**
 * Load our config
 */
require_once $_CONF['path_system'].'classes/config.class.php';
$c = config::get_instance();
$_DW_CONF = $c->get_config('dokuwiki');


if(!defined('DOKU_INC')) define('DOKU_INC',$_CONF['path_html'] . $_DW_CONF['public_dir']);

// set the language for the wiki

if (isset($_USER['language']) ) {
    $lang = $_USER['language'];
} else {
    $lang = $_CONF['language'];
}
$conf['lang'] = $_CONF['iso_lang'];

/**
* This will put an option for the plugin in the command and control block on moderation.php
*
* Add the plugin name, icon and link to the command and control block in moderation.php
*
* @return   array   Array containing (plugin name, admin url, url of plugin icon)
*
*/

function plugin_cclabel_dokuwiki() {
    global $_CONF, $_DW_CONF;

    if (SEC_inGroup('Root') ) {
        return array('DokuWiki',
            $_CONF['site_url'] . $_DW_CONF['public_dir'] . 'doku.php?do=admin&amp;id=start',
            $_CONF['site_url'] . $_DW_CONF['public_dir'] . 'dokuwiki.png');
    } else {
        return '';
    }
}

/**
  * Whats New Block API Support
  * Return the Headline and Byline for the new section in the Whatsnew Block
*/
function plugin_whatsnewsupported_dokuwiki() {
    global $_CONF, $_DW_CONF, $_USER, $LANG_DW00;

    if ( $_DW_CONF['enable_whats_new'] != 1 ) {
        return;
    }
    if ( COM_isAnonUser() && ( $_DW_CONF['loginrequired'] == 1 || $_CONF['loginrequired'] == 1) )  {
        return '';
    }

    if ($_DW_CONF['restrict_to_group'] != '' ) {
        if (SEC_inGroup($_DW_CONF['restrict_to_group'])) {
            $label = sprintf($LANG_DW00['whats_new_time'],$_DW_CONF['whats_new_days']);
            return array($LANG_DW00['whats_new_prompt'],$label);
        }
    } else {
        $label = sprintf($LANG_DW00['whats_new_time'],$_DW_CONF['whats_new_days']);
        return array($LANG_DW00['whats_new_prompt'],$label);
    }
    return '';
}

function plugin_getwhatsnew_dokuwiki() {
    global $conf;
    global $QUERY;
    global $ID;
    global $LANG_DW00;
    global $_CONF;
    global $_DW_CONF;

	if ( COM_isAnonUser() && ( $_DW_CONF['loginrequired'] == 1 || $_CONF['loginrequired'] == 1) )  {
        return;
    }
	
    if (!file_exists($_CONF['path_html'] . $_DW_CONF['public_dir'] . 'data/meta/_dokuwiki.changes') ) {
		return $LANG_DW00['no_whats_new'] . '<br />';
    }

    $count = 0;
    $cutoff = time() - (86400 * $_DW_CONF['whats_new_days']);

    require_once(DOKU_INC.'inc/init.php');
    require_once(DOKU_INC.'inc/common.php');
    require_once(DOKU_INC.'inc/events.php');
    require_once(DOKU_INC.'inc/pageutils.php');
    require_once(DOKU_INC.'inc/html.php');
    require_once(DOKU_INC.'inc/auth.php');
    require_once(DOKU_INC.'inc/actions.php');
    require_once(DOKU_INC.'inc/changelog.php');

    if ( !isset($_DW_CONF['whatsnew_length']) || $_DW_CONF['whatsnew_length'] == 0 ) {
        $_DW_CONF['whatsnew_length'] = 20;
    }

    $recent = array();
    $items  = array();
    $recent = getRecents(0,20,'',0);
	
    foreach ($recent as $item => $stuff) {
        if ( $stuff['date'] > $cutoff ) {
            $items[] = '<a href="' . $_CONF['site_url'] . $_DW_CONF['public_dir'] . 'doku.php?id=' . $stuff['id'] . '" title="' . $stuff['id'] . '">' . COM_truncate($stuff['id'],$_DW_CONF['whatsnew_length'],'') . '</a>';
            $count++;
        }
    }
    if ( $count == 0 ) {
        return $LANG_DW00['no_whats_new'] .  '<br />';
    }
    return $items;
}



/**
* Returns the administrative option for this plugin
*
* Adds the plugin to the Admin menu
*
* Universal Plugin:  Modify/Uncomment use it
*
* @return   array   Array containing (plugin name, plugin admin url, # of items in plugin or '')
*
*/
function plugin_getadminoption_dokuwiki() {
    global $_CONF, $_DW_CONF;

    if (SEC_inGroup('Root')) {
        return array('DokuWiki',$_CONF['site_url'] . $_DW_CONF['public_dir'] . 'doku.php?do=admin&amp;id=start','');
    }
}

function plugin_getmenuitems_dokuwiki() {
    global $_CONF, $_DW_CONF,$_USER,$LANG_DW00;

    if ( COM_isAnonUser() && ( $_DW_CONF['loginrequired'] == 1 || $_CONF['loginrequired'] == 1) )  {
        return '';
    }

    if ($_DW_CONF['restrict_to_group'] != '' ) {
        if (SEC_inGroup($_DW_CONF['restrict_to_group'])) {
            $menuitems[$_DW_CONF['menulabel']] = $_CONF['site_url'] . $_DW_CONF['public_dir'] . 'doku.php';
            return $menuitems;
        }
    } else {
        $menuitems[$_DW_CONF['menulabel']] = $_CONF['site_url'] . $_DW_CONF['public_dir'] . 'doku.php';
        return $menuitems;
    }
    return '';
}


function plugin_searchtypes_dokuwiki() {
    global $_CONF, $_DW_CONF, $_USER, $LANG_DW00;

    if ( COM_isAnonUser() && ( $_DW_CONF['loginrequired'] == 1 || $_CONF['loginrequired'] == 1) )  {
        return '';
    }
    if ($_DW_CONF['restrict_to_group'] != '' ) {
        if (!SEC_inGroup($_DW_CONF['restrict_to_group'])) {
            return '';
        }
    }

    $tmp['dokuwiki'] = $_DW_CONF['menulabel'];
    return $tmp;
}


/**
* this searches for pages matching the user query and returns an array of
* for the header and table rows back to search.php where it will be formated and
* printed
*
* @query            string          Keywords user is looking for
* @datestart        date/time       Start date to get results for
* @dateend          date/time       End date to get results for
* @topic            string          The topic they were searching in
* @type             string          Type of items they are searching
* @author           string          Get all results by this author
* @keyType        string          search key type: 'all', 'phrase', 'any'
* @param    int     $page       page number of current search (deprecated)
* @param    int     $perpage    number of results per page (deprecated)
*
*/

function plugin_dopluginsearch_dokuwiki($query, $datestart, $dateend, $topic, $type, $author, $keyType, $page, $perpage) {

    global $_CONF, $_DW_CONF;

    if ( $_DW_CONF['disable_search_integration'] == 1) {
        return '';
    }
    if ( COM_isAnonUser() && ( $_DW_CONF['loginrequired'] == 1 || $_CONF['loginrequired'] == 1) )  {
        return '';
    }
    if ($_DW_CONF['restrict_to_group'] != '' ) {
        if (!SEC_inGroup($_DW_CONF['restrict_to_group'])) {
            return '';
        }
    }

    // Bail if we aren't supppose to do our search
    if ($type <> 'all' AND $type <> 'dokuwiki') {
        return '';
    }

	$pages = DW_searchItems($query, $keyType);
	$search = new SearchCriteria('dokuwiki', $_DW_CONF['menulabel'],'text');
	$search->setSQL('--');
    $search->setFTSQL('--');
    $search->setResults($pages);
    $search->setRank(4);
	
	return $search;
}

function DW_searchItems( $query, $keyType )
{
    global $_CONF, $_DW_CONF, $LANG_DW00, $formattedResults;
	
	require_once (DOKU_INC.'inc/init.php');
    require_once (DOKU_INC.'inc/common.php');
    require_once (DOKU_INC.'inc/events.php');
    require_once (DOKU_INC.'inc/pageutils.php');
    require_once (DOKU_INC.'inc/html.php');
    require_once (DOKU_INC.'inc/auth.php');
    require_once (DOKU_INC.'inc/actions.php');
    require_once ($_CONF['path_html'] . $_DW_CONF['public_dir'] . 'inc/search.php');
    require_once ($_CONF['path_html'] . $_DW_CONF['public_dir'] . 'inc/fulltext.php');
	
	if ($keyType == 'any') {
        $searchQuery = str_replace(' ', " OR ", $query);
    } else if ($keyType == 'all') {
        $searchQuery = str_replace(' ', " AND ", $query);
    } else {
        $searchQuery = '"'.$query.'"';
    }
	
	$QUERY = $searchQuery;
    //check if search is restricted to namespace
    if(preg_match('/([^@]*)@([^@]*)/',$QUERY,$match)) {
        $id = cleanID($match[1]);
        if(empty($id)) {
            return '';
        }
    } else {
        $id = cleanID($QUERY);
    }

    //do quick pagesearch
    $data = array();
    //do fulltext search
    $data = ft_pageSearch($QUERY,$poswords);

    if(count($data)){
        foreach($data as $id => $cnt){
            $fqn = wikiFN($id);
            $date = stat($fqn);
			$formattedResults[] = array(LF_SOURCE_NAME=>$_DW_CONF['menulabel'],
                                        LF_SOURCE_TITLE=>$LANG_DW00['search_type'],
                                        'title'=>$id,
                                        'description'=>ft_snippet($id,$poswords),
                                        'date'=>$date['mtime'],
                                        'url'=>$_CONF['site_url'] . $_DW_CONF['public_dir']
                                    . 'doku.php?id=' . urlencode($id),
                                        'hits'=>$cnt,
                                        'uid'=>''
                                    );
        }
    } else {
        return '';
    }

    return $formattedResults;
}

function dokuwiki_perm()
{
    global $_DW_CONF, $_CONF, $_USER, $LANG_LOGIN;

    if ( COM_isAnonUser() && ( $_DW_CONF['loginrequired'] == 1 || $_CONF['loginrequired'] == 1) )  {
        $display = COM_siteHeader();
        $display .= SEC_loginRequiredForm();
        $display .= COM_siteFooter();
        echo $display;
        exit;
    }
}

function plugin_autotags_dokuwiki ($op, $content = '', $autotag = '') {
    global $_CONF, $_DW_CONF, $LANG_DW00;

    if ($op == 'tagname' ) {
        return array('wiki');
    } elseif ($op == 'description') {
        return array (
            'wiki' => $LANG_DW00['autotag_desc_wiki']
            );
    } else if ($op == 'parse') {
        $dwId = $autotag['parm1'];
        $caption = $autotag['parm2'];

        $link = '<a href="' . $_CONF['site_url'] . $_DW_CONF['public_dir'] . 'doku.php?id=' . $dwId . '">';
        if ( empty($caption) || $caption == '' ) {
            $caption = $dwId;
        }
        $dwTag = $link . $caption . '</a>';
        $content = str_replace ($autotag['tagstr'], $dwTag, $content);
        return $content;
    }
}

/**
 * Returns the current plugin code version
 *
 * @return string    plugin version
 */
function plugin_chkVersion_dokuwiki()
{
    global $_CONF;

    require_once $_CONF['path'] . 'plugins/dokuwiki/autoinstall.php';

    $inst_parms = plugin_autoinstall_dokuwiki('dokuwiki');

    return $inst_parms['info']['pi_version'];
}

/**
* Update the plugin
*
* @return   int     Number of message to display or true for success
*
*/
function plugin_upgrade_dokuwiki()
{
    global $_CONF, $_TABLES;

    $installed_version = DB_getItem($_TABLES['plugins'], 'pi_version',
                                    "pi_name = 'dokuwiki'");
    $code_version = plugin_chkVersion_dokuwiki();
    if ($installed_version == $code_version) {
        // nothing to do
        return true;
    }

    require_once $_CONF['path'] . 'plugins/dokuwiki/autoinstall.php';

    if (! plugin_compatible_with_this_version_dokuwiki('dokuwiki')) {
        return 3002;
    }

    // other update code goes here

    // update plugin version number
    $inst_parms = plugin_autoinstall_dokuwiki('dokuwiki');
    $pi_gl_version = $inst_parms['info']['pi_gl_version'];
	
	if (! isset($_DW_CONF['displayblocks'])) {
	    require_once $_CONF['path'] . 'plugins/dokuwiki/install_defaults.php';
        $c = config::get_instance();
		
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


    DB_query("UPDATE {$_TABLES['plugins']} SET pi_version = '$code_version', pi_gl_version = '$pi_gl_version' WHERE pi_name = 'dokuwiki'");
	DB_query("UPDATE {$_TABLES['plugins']} SET pi_homepage='http://geeklog.fr' WHERE pi_name='dokuwiki'",1);
	COM_errorLog('Dokuwiki plugin was successfully updated to version ' . $code_version . '.');

    return true;
}
/**
* Automatic uninstall function for plugins
*
* This code is automatically uninstalling the plugin.
* It passes an array to the core code function that removes
* tables, groups, features and php blocks from the tables.
* Additionally, this code can perform special actions that cannot be
* foreseen by the core code (interactions with other plugins for example)
*
* @return   array   Plugin information
*
*/
function plugin_autouninstall_dokuwiki()
{
    $out = array (
        /* give the name of the tables, without $_TABLES[] */
        'tables' => array(),
        /* give the full name of the group, as in the db */
        'groups' => array('Dokuwiki Admin'),
        /* give the full name of the feature, as in the db */
        'features' => array(),
        /* give the full name of the block, including 'phpblock_', etc */
        'php_blocks' => array(),
        /* give all vars with their name */
        'vars' => array()
    );

    return $out;
}
?>