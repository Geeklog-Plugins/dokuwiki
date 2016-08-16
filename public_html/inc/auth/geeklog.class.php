<?php
/**
 * Geeklog authentication backend
 *
 * @license   GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author    Andreas Gohr <andi@splitbrain.org>
 * @author    Ben <cordiste@free.fr>
 * Based on the original Dokuwiki Plugin
 * Copyright (C) 2006-2008 by the following authors:
 * @author    Mark R. Evans <mevans@ecsnet.com>
 */

if (file_exists('../lib-common.php')) require_once ('../lib-common.php');

class auth_geeklog extends auth_basic {
    var $cnf = null;

    /**
     * Constructor
     */
    function auth_geeklog(){
        global $conf;

        $this->cando['external'] = true;
        $this->cando['logoff']   = true;
        $this->success = true;
        return true;
    }

  /**
   * Just checks against the $_USER variable
   */
  function trustExternal($user,$pass,$sticky=false){
    global $_USER;
    global $USERINFO;
    global $conf;
    global $lang;
    $sticky ? $sticky = true : $sticky = false; //sanity check

    if ( isset($_USER) && $_USER['uid'] > 1 ) {
        $USERINFO['pass'] = rand();
        $USERINFO['user'] = $_USER['username'];
        $USERINFO['name'] = ($_USER['fullname'] == '' ? $_USER['username'] : $_USER['fullname']);
        $USERINFO['mail'] = $_USER['email'];
        $USERINFO['grps'] = array();

        $grps = SEC_getUserGroups();
        $i = 0;
        foreach ($grps as $name=>$id) {
            $gName = preg_replace("/\s/e" , "" , $name);
            $gName2 = preg_replace("/-/","",$gName);
            $USERINFO['grps'][$i] = $gName2;
	  	    $i++;
        }
        $_SERVER['REMOTE_USER'] = $_USER['username'];
        $_SESSION[DOKU_COOKIE]['auth']['user'] = $_USER['username'];
        $_SESSION[DOKU_COOKIE]['auth']['pass'] = $pass;
        $_SESSION[DOKU_COOKIE]['auth']['info'] = $USERINFO;

        return true;
    } else {
        $USERINFO['user'] = 'Guest';
        $USERINFO['name'] = 'Guest';
        $USERINFO['pass'] = rand();
        $USERINFO['mail'] = 'unknown';
        $USERINFO['grps'] = array();
        $USERINFO['grps'][0] = 'AllUsers';
        $USERINFO['grps'][1] = 'ALL';

        $_SERVER['REMOTE_USER'] ='Guest';
        $_SESSION[DOKU_COOKIE]['auth']['user'] = 'Guest';
        $_SESSION[DOKU_COOKIE]['auth']['info'] = $USERINFO;

        return true;
    }
  }

  function logOff(){
      global $USERINFO;

      $USERINFO['user'] = 'Guest';
      $USERINFO['name'] = 'Guest';
      $USERINFO['pass'] = rand();
      $USERINFO['mail'] = 'unknown';
      $USERINFO['grps'] = array();
      $USERINFO['grps'][0] = 'AllUsers';
      $USERINFO['grps'][1] = 'ALL';

      $_SERVER['REMOTE_USER'] ='Guest';
      $_SESSION[DOKU_COOKIE]['auth']['user'] = 'Guest';
      $_SESSION[DOKU_COOKIE]['auth']['info'] = $USERINFO;
  }

  /**
   * Return user info
   *
   * Returns info about the given user needs to contain
   * at least these fields:
   *
   * name string  full name of the user
   * mail string  email addres of the user
   * grps array   list of groups the user is in
   *
   * @author  Andreas Gohr <andi@splitbrain.org>
   * @return  array containing user data or false
   */
  function getUserData($user) {
    global $_TABLES, $_CONF;

    $userdata = array();
    $uid = 1;

    $userdata['pass'] = rand();
    $userdata['user'] = $user;

    if ( $user == 'Guest' ) {
        $userdata['name'] = 'Guest';
        $userdata['mail'] = 'unknown';
    } else {
        $result = DB_query("SELECT uid,username,fullname,email FROM {$_TABLES['users']} WHERE username='".addslashes($user)."'");
        $numRows = DB_numRows($result);
        if ($numRows > 0 && $user != 'Guest') {
            $row = DB_fetchArray($result);
            if ( $_CONF['show_fullname'] == 1 ) {
                $userdata['name'] = ($row['fullname'] == '' ? $row['username'] : $row['fullname']);
            } else {
                $userdata['name'] = $user;
            }
            $userdata['mail'] = $row['email'];
            $uid = $row['uid'];
        } else {
            $userdata['name'] = 'Guest';
            $userdata['mail'] = 'unknown';
        }
    }
    $userdata['grps'] = array();

    $grps = SEC_getUserGroups($uid);
    $i = 0;
    foreach ($grps as $name=>$id) {
        $gName = preg_replace("/\s/e" , "" , $name);
        $gName2 = preg_replace("/-/","",$gName);
        $userdata['grps'][$i] = $gName2;
  	    $i++;
    }
    return $userdata;
  }
}

//Setup VIM: ex: et ts=4 enc=utf-8 :
?>