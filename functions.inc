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
    die ('This file can not be used on its own!');
}

// Include missing security functions from glFusion
include_once ($_CONF['path'] . 'plugins/dokuwiki/security.inc');

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

include $_CONF['path_html'].$_DW_CONF['public_dir'].'conf/local.php';

if (isset($_USER['language']) ) {
    $lang = $_USER['language'];
} else {
    $lang = $_CONF['language'];
}
$conf['lang'] = $_CONF['iso_lang'];


function plugin_access_dokuwiki()
{
    global $_DW_CONF, $_CONF, $USERINFO;

    $display = '';

    // must validate user session and ensure it is properly protected to
    // prevent injection issues...
    $itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $pwd = $USERINFO['pass'];
    $dwsh = _hash_gensalt_private($pwd, $itoa64);

    if ( COM_isAnonUser() && ( $_DW_CONF['loginrequired'] == 1 || $_CONF['loginrequired'] == 1) )  {
        $display = COM_siteHeader();
        $display .= SEC_loginRequiredForm();
        $display .= COM_siteFooter();
        echo $display;
        exit;
    }
    if ($_DW_CONF['restrict_to_group'] != '' ) {
        if ( !SEC_inGroup($_DW_CONF['restrict_to_group']) ) {
            COM_errorLog("Someone has tried to access a restricted DokuWiki page.  User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: $REMOTE_ADDR",1);
            COM_handle404();
            exit;
        }
    }
    return true;
}
/**
* Implements the [wiki:] autotag.
*
*/
function plugin_autotags_dokuwiki ($op, $content = '', $autotag = '')
{
    global $_CONF, $_DW_CONF, $LANG_DW00, $conf;

    if ($op == 'tagname' ) {
        return array('wiki');
    } else if ($op == 'tagusage') {
        $tagUsage = array(
            array('namespace' => 'dokuwiki','usage' => 'page')
        );
        return $tagUsage;
    } else if ($op == 'desc' ) {
        return $LANG_DW00['desc_wiki'];
    } else if ($op == 'parse') {
        if ( ($autotag['tag'] == 'wiki') ) {
            $dwId    = (isset($autotag['parm1']) ? $autotag['parm1'] : '');
            $caption = (isset($autotag['parm2']) ? $autotag['parm2'] : '');
            if ( empty($dwId) ) {
                $content = str_replace ($autotag['tagstr'], '', $content);
                return $content;
            }
            if ( isset($conf['userewrite']) ) {
                switch ($conf['userewrite']) {
                    case 2 :
                        $link = '<a href="'.$_CONF['site_url'].$_DW_CONF['public_dir'].'doku.php/'.$dwId.'">';
                        break;
                    case 1 :
                        $link = '<a href="'.$_CONF['site_url'].$_DW_CONF['public_dir'].''.$dwId.'">';
                        break;
                    default :
                        $link = '<a href="'.$_CONF['site_url'].$_DW_CONF['public_dir'].'doku.php?id='.$dwId.'">';
                        break;
                 }
            } else {
                $link = '<a href="'.$_CONF['site_url'].$_DW_CONF['public_dir'].'doku.php?id='.$dwId.'">';
            }

            if ( empty($caption) || $caption == '' ) {
                $caption = $dwId;
            }
            $dwTag = $link.$caption.'</a>';
            $content = str_replace ($autotag['tagstr'], $dwTag, $content);
            return $content;
        } else {
            return $content;
        }
    }
}


/**
* This will put an option for the plugin in the command and control block on
* moderation.php
*
* Add the plugin name, icon and link to the command and control block in
* moderation.php
*
* @return   array   Array containing (plugin name, admin url, url of plugin icon)
*
*/
function plugin_cclabel_dokuwiki()
{
    global $_CONF, $_DW_CONF;

    if (SEC_inGroup('Root') ) {
        return array('DokuWiki',
            $_CONF['site_url'].$_DW_CONF['public_dir'].'doku.php?do=admin&amp;id=start',
            $_CONF['site_url'].$_DW_CONF['public_dir'].'dokuwiki.png');
    } else {
        return '';
    }
}


/**
* Return the current version of code.
* Used in the Plugin Editor to show the registered version and code version
*/
function plugin_chkVersion_dokuwiki()
{
    global $_DW_CONF;

    return $_DW_CONF['pi_version'];
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
*
*/
function plugin_dopluginsearch_dokuwiki($query, $datestart, $dateend, $topic, $type, $author, $keyType, $page, $perpage)
{
    global $conf, $QUERY, $ID, $LANG_DW00, $_CONF, $_DW_CONF, $_USER;

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
    if ($type <> 'all' AND $type <> 'dokuwiki') {
        return '';
    }
    if ($keyType == 'any') {
        $searchQuery = str_replace(' ', " " . 'OR' . " ", $query);
    } else if ($keyType == 'all') {
        $searchQuery = str_replace(' ', " " . 'AND' . " ", $query);
    } else {
        $searchQuery = '"'.$query.'"';
    }

    $pages = plugin_executepluginsearch_dokuwiki($query, 0, $perpage, $keyType);
    
    $search = new SearchCriteria('dokuwiki', 'DokuWiki','text');
    $num_results = _return_search_count($searchQuery);
    //$search->setNumResults($num_results);
    $search->setResults($pages);
    $search->setRank(4);
    return $search;
}


/**
* this searches for pages matching the user query and returns an array of
* for the header and table rows back to search.php where it will be formated and
* printed
*
*/
function plugin_executepluginsearch_dokuwiki($query, $offset, $limit,$keyType)
{
    global $conf, $QUERY, $ID, $LANG_DW00, $_CONF, $_DW_CONF, $_USER;

    $results = array();

    if ( $_DW_CONF['disable_search_integration'] == 1) {
        return '';
    }
    if ( COM_isAnonUser() && ( $_DW_CONF['loginrequired'] == 1 || $_CONF['loginrequired'] == 1) )  {
        return '';
    }
    if ( $_DW_CONF['restrict_to_group'] != '' ) {
        if (!SEC_inGroup($_DW_CONF['restrict_to_group'])) {
            return '';
        }
    }
    if ( empty($type) ) {
        $type = 'all';
    }
    if ($type <> 'all' AND $type <> 'dokuwiki') {
        return '';
    }
    require_once (DOKU_INC.'inc/init.php');

    if ($keyType == 'any') {
        $searchQuery = str_replace(' ', " OR ", $query);
    } else if ($keyType == 'all') {
        $searchQuery = str_replace(' ', " AND ", $query);
    } else {
        $searchQuery = '"'.$query.'"';
    }
    $QUERY = $searchQuery;
    //check if search is restricted to namespace
    if ( preg_match('/([^@]*)@([^@]*)/',$QUERY,$match) ) {
        $id = cleanID($match[1]);
        if ( empty($id) ) {
            return '';
        }
    } else {
        $id = cleanID($QUERY);
    }
    $data = array();
    $data = ft_pageSearch($QUERY,$poswords);

    if ( count($data) < $offset ) {
        return '';
    }
    if ( count($data) ) {
        $num = 0;
        $found = 0;
        foreach( $data as $id => $cnt ) {
            if ( $num < $offset ) {
                $num++;
                continue;
            }
            if ( $found >= $limit ) {
                break;
            }
            $row = array();
            $fqn = wikiFN($id);
            $date = stat($fqn);
            $row['id'] = $id;
            $row['title'] = $id;
            $row['hits'] = $cnt;
            $row['description'] = ft_snippet($id,$poswords);
            $row['date'] = $date['mtime'];

            if ( isset($conf['userewrite']) ) {
                switch ($conf['userewrite']) {
                    case 2 :
                        $row['url'] = $_CONF['site_url'].$_DW_CONF['public_dir'].'doku.php/'.$id;
                        break;
                    case 1 :
                        $row['url'] = $_CONF['site_url'].$_DW_CONF['public_dir'].''.$id;
                        break;
                    default :
                        $row['url'] = $_CONF['site_url'].$_DW_CONF['public_dir'].'doku.php?id='.$id;
                        break;
                 }
            } else {
                $row['url'] = $_CONF['site_url'].$_DW_CONF['public_dir'].'doku.php?id='.$id;
            }
            $result[] = $row;
            $found++;
            $num++;
        }
    } else {
        return '';
    }

    $direction = SORT_ASC;
    $column = array();
    foreach ($result as $sortarray) {
        $column[] = $sortarray[ 'date' ];
    }
    array_multisort($column, $direction, $result);

    return $result;
}

/**
* Return the number of items found in search
*
*/
function _return_search_count($query)
{
    global $QUERY, $ID, $LANG_DW00,$_CONF,$_DW_CONF,$_USER;

    if ( $_DW_CONF['disable_search_integration'] == 1) {
        return 0;
    }
    if ( COM_isAnonUser() && ( $_DW_CONF['loginrequired'] == 1 || $_CONF['loginrequired'] == 1) )  {
        return 0;
    }
    if ($_DW_CONF['restrict_to_group'] != '' ) {
        if (!SEC_inGroup($_DW_CONF['restrict_to_group'])) {
            return 0;
        }
    }
    require_once DOKU_INC.'inc/init.php';

    //check if search is restricted to namespace
    if ( preg_match('/([^@]*)@([^@]*)/',$QUERY,$match) ) {
        $id = cleanID($match[1]);
        if(empty($id)) {
            return 0;
        }
    } else {
        $id = cleanID($QUERY);
    }

    $QUERY = $query;
    //check if search is restricted to namespace
    if ( preg_match('/([^@]*)@([^@]*)/',$QUERY,$match) ) {
        $id = cleanID($match[1]);
        if(empty($id)) {
            return 0;
        }
    } else {
        $id = cleanID($QUERY);
    }
    $data = array();
    $data = ft_pageSearch($QUERY,$poswords);
    return (count($data));
}

/**
* glFusion is asking us to provide any items that show up in the type
* drop-down on search.php.  Let's users search the dokuwiki pages.
*
* @return   array   (plugin name/entry title) pair for the dropdown
*
*/
function plugin_searchtypes_dokuwiki()
{
    global $_CONF, $_DW_CONF, $_USER, $LANG_DW00;

    $tmp = array();

    if ( COM_isAnonUser() && ( $_DW_CONF['loginrequired'] == 1 || $_CONF['loginrequired'] == 1) )  {
        return '';
    }
    if ($_DW_CONF['restrict_to_group'] != '' ) {
        if (!SEC_inGroup($_DW_CONF['restrict_to_group'])) {
            return '';
        }
    }

    $tmp['dokuwiki'] = $LANG_DW00['menulabel'];
    return $tmp;
}

/**
* Return information for an wiki page
*
* @param    string  $id         page ID
* @param    string  $what       comma-separated list of properties
* @param    int     $uid        user ID or 0 = current user
* @param    array   $options    (reserved for future extensions)
* @return   mixed               string or array of strings with the information
*
*/
function plugin_getiteminfo_dokuwiki($id, $what, $uid = 0, $options = array())
{
    global $_CONF, $_TABLES, $conf, $_DW_CONF,$LANG_DW00;

    require_once DOKU_INC.'inc/init.php';

    $properties = explode(',', $what);

    $props = array();
    foreach ($properties as $p) {
        switch ($p) {
            case 'excerpt':
                $props[$p] = p_wiki_xhtml_summary($id);
                break;
            case 'description':
                $props[$p] = p_wiki_xhtml($id);
                break;
            case 'raw-description' :
                $props[$p] =  rawWiki($id);
                break;
            case 'id':
                $props['id'] = $id;
                break;
            case 'title':
                $props['title'] = $id;
                break;
            case 'url':
                if ( isset($conf['userewrite']) ) {
                    switch ($conf['userewrite']) {
                        case 2 :
                            $props['url'] = $_CONF['site_url'].$_DW_CONF['public_dir'].'doku.php/'.$id;
                            break;
                        case 1 :
                            $props['url'] = $_CONF['site_url'].$_DW_CONF['public_dir'].''.$id;
                            break;
                        default :
                            $props['url'] = $_CONF['site_url'].$_DW_CONF['public_dir'].'doku.php?id='.$id;
                            break;
                     }
                } else {
                    $props['url'] = $_CONF['site_url'].$_DW_CONF['public_dir'].'doku.php?id='.$id;
                }
                break;
            case 'label':
                $props['label'] = $LANG_DW00['menulabel'];
                break;
            case 'status' :
                $props['status'] = 1;
                break;
            default:
                $props[$p] = '';
                break;
        }
    }

    $mapped = array();
    foreach ($props as $key => $value) {
        if ($id == '*') {
            if ($value != '') {
                $mapped[$key] = $value;
            }
        } else {
            $mapped[$key] = $value;
        }
    }

    if ($id == '*') {
        $retval[] = $mapped;
    } else {
        $retval = $mapped;
    }

    if (($id != '*') && (count($retval) == 1)) {
        $retval = $retval[0];
    }

    return $retval;
}


/**
* Returns the administrative option for this plugin
*
* Adds the plugin to the Admin menu
*
* @return   array   Array containing (plugin name, plugin admin url, # of items
*                   in plugin or '')
*
*/
function plugin_getadminoption_dokuwiki()
{
    global $_CONF, $_DW_CONF;

    if (SEC_inGroup('Root')) {
        return array('DokuWiki',$_CONF['site_url'] . $_DW_CONF['public_dir'] . 'doku.php?do=admin&amp;id=start','');
    }
}


/**
* Returns the items for this plugin that should appear on the main menu
*
* NOTE: this MUST return the url/value pairs in the following format
* $<arrayname>[<label>] = <url>
*
* @return   mixed   menu entry, or boolean false if disabled / hidden
*
*/
function plugin_getmenuitems_dokuwiki()
{
    global $_CONF, $_DW_CONF, $_USER, $LANG_DW00;

    $menuitems = array();

    if ( COM_isAnonUser() && ( $_DW_CONF['loginrequired'] == 1 || $_CONF['loginrequired'] == 1) )  {
        return '';
    }
    if ($_DW_CONF['restrict_to_group'] != '' ) {
        if (!SEC_inGroup($_DW_CONF['restrict_to_group'])) {
            return '';
        }
    }
    $menuitems[$LANG_DW00['menulabel']] = $_CONF['site_url'] . $_DW_CONF['public_dir'] . 'doku.php';
    return $menuitems;
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

/**
* Return new pages for the What's New block
*
* @return   string  HTML list of new pages
*
*/
function plugin_getwhatsnew_dokuwiki()
{
    global $conf, $QUERY, $ID, $LANG_DW00, $_CONF, $_DW_CONF;

    if ( $_DW_CONF['enable_whats_new'] != true ) return;

    if ( COM_isAnonUser() && ( $_DW_CONF['loginrequired'] == 1 || $_CONF['loginrequired'] == 1) )  {
        return;
    }
    if ($_DW_CONF['restrict_to_group'] != '' ) {
        if (!SEC_inGroup($_DW_CONF['restrict_to_group'])) {
            return;
        }
    }
    if (!file_exists($_CONF['path_html'] . $_DW_CONF['public_dir'] . 'data/meta/_dokuwiki.changes') ) {
		return '';
    }
    $count = 0;
    $cutoff = time() - (86400 * $_DW_CONF['whats_new_days']);

    require_once DOKU_INC.'inc/init.php';

    if ( !isset($_DW_CONF['whatsnew_length']) || $_DW_CONF['whatsnew_length'] == 0 ) {
        $_DW_CONF['whatsnew_length'] = $_CONF['title_trim_length'];
    }

    $recent = array();
    $items  = array();
    $recent = getRecents(0,20,'',0);

    foreach ($recent as $item => $stuff) {
        if ( $stuff['date'] > $cutoff ) {
            if ( isset($conf['userewrite']) ) {
                switch ($conf['userewrite']) {
                    case 2 :
                        $items[] = '<a href="'.$_CONF['site_url'].$_DW_CONF['public_dir'].'doku.php/'.$stuff['id'] . '" title="' . $stuff['id'].'">' . COM_truncate($stuff['id'],$_DW_CONF['whatsnew_length'],'') . '</a>';
                        break;
                    case 1 :
                        $items[] = '<a href="'.$_CONF['site_url'].$_DW_CONF['public_dir'].''.$stuff['id'] . '" title="' . $stuff['id'].'">' . COM_truncate($stuff['id'],$_DW_CONF['whatsnew_length'],'') . '</a>';
                        break;
                    default :
                        $items[] = '<a href="'.$_CONF['site_url'].$_DW_CONF['public_dir'].'doku.php?id='.$stuff['id'] . '" title="' . $stuff['id'].'">' . COM_truncate($stuff['id'],$_DW_CONF['whatsnew_length'],'') . '</a>';
                        break;
                 }
            } else {
                $items[] = '<a href="'.$_CONF['site_url'].$_DW_CONF['public_dir'].'doku.php?id='.$stuff['id'] . '" title="' . $stuff['id'].'">' . COM_truncate($stuff['id'],$_DW_CONF['whatsnew_length'],'') . '</a>';
            }

            $count++;
        }
    }
    if ( $count == 0 ) {
        return $LANG_DW00['no_whats_new'];
    }
    
    return $items;
}

/*
* return the administrative heading
*/
function _dw_admin_header()
{
    global $_CONF, $_DW_CONF, $LANG_ADMIN, $LANG_DW00;
    //USES_lib_admin();
    require_once($_CONF['path_system'] . 'lib-admin.php');
    
    $retval = '';
    $menu_arr = array(
            array('url'  => $_CONF['site_admin_url'],
                  'text' => $LANG_ADMIN['admin_home']),
    );
    $retval  .= ADMIN_createMenu($menu_arr, $LANG_DW00['admin'],
                                $_CONF['site_url'].$_DW_CONF['public_dir'].'dokuwiki.png');
    $retval .'<br/>';
    return $retval;
}


/*
* return the wiki page footer
*/
function _dw_footer()
{
    global $_DW_CONF;
    $finalPage = '';
    $ptitle = tpl_pagetitle(null,1);
    $wikiPage = ob_get_clean();

    switch( $_DW_CONF['displayblocks'] ) {
        case 0 :
        case 2 :
            $what = 'menu';
            break;
        case 1 :
        case 3 :
            $what = 'none';
            break;
        default :
            $what = 'menu';
            break;
    }
    
    switch( $_DW_CONF['displayblocks'] ) {
        case 0 :
        case 3 :
            $rightblock = false;
            break;
        case 1 :
        case 2 :
            $finalPage .= COM_siteFooter( true );
            $rightblock = true;
            break;
        default :
            $rightblock = false;
            break;
    }
    
    $display = COM_createHTMLDocument(
        $wikiPage,
        array(
            'what'          => $what, 
            'pagetitle'     => $ptitle,
            'rightblock'    => $rightblock,
        )
    );
    
    COM_output($display);
    exit;

}


/**
 * Upgrade plugin
 *
 * @return  string   plugin version number
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
	COM_errorLog('DokuWiki plugin was successfully updated to version ' . $code_version . '.');

    return true;
}






function plugin_getheadercode_dokuwiki()
{
    global $_DW_HEADER, $_DW_CONF;

    // Return dokuwiki header info originally created in function _tpl_metaheaders_action and file template.php

    return $_DW_HEADER;
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
        'groups' => array('DokuWiki Admin'),
        /* give the full name of the feature, as in the db */
        'features'   => array('dokuwiki.admin'),
        /* give the full name of the block, including 'phpblock_', etc */
        'php_blocks' => array(),
        /* give all vars with their name */
        'vars' => array()
    );

    return $out;
}

?>