<?php
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

/**
 * glFusion authentication backend
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Mark R. Evans <mark@glfusion.org>
 * @copyright  2012-2016
 */

class auth_plugin_authglfusion extends DokuWiki_Auth_Plugin {
    /** @var array user cache */
    protected $users = null;

    /** @var array filter pattern */
    protected $_pattern = array();

    /**
     * Constructor
     *
     * Carry out sanity checks to ensure the object is
     * able to operate. Set capabilities.
     *
     * @author  Christopher Smith <chris@jalakai.co.uk>
     */
    public function __construct() {
        parent::__construct();
        global $config_cascade;

        $this->cando['external'] = true;
        $this->cando['logoff']   = true;
        $this->success = true;
    }

    public function trustExternal( $user, $pass, $sticky = false ) {
        global $_USER, $USERINFO, $conf, $lang;
        $sticky ? $sticky = true : $sticky = false;
        plugin_access_dokuwiki();
        if ( !COM_isAnonUser()) {
            $USERINFO['pass'] = _hash_gensalt_private(rand(),$this->itoa64);
            $USERINFO['user'] = $_USER['username'];
            $USERINFO['name'] = ($_USER['fullname'] == '' ? $_USER['username'] : $_USER['fullname']);
            $USERINFO['mail'] = $_USER['email'];
            $USERINFO['grps'] = array();
            $grps = SEC_getUserGroups();
            $i = 0;
            foreach ($grps as $name=>$id) {
                $USERINFO['grps'][$i] = preg_replace_callback(
                                            '/\s/',
                                            function ($m) {
                                             return "_";
                                            },
                                            $name
                                        );
    	  	    $i++;
            }
            $_SERVER['REMOTE_USER'] = $_USER['username'];
            $_SESSION[DOKU_COOKIE]['auth']['user'] = $_USER['username'];
            $_SESSION[DOKU_COOKIE]['auth']['pass'] = $pass;
            $_SESSION[DOKU_COOKIE]['auth']['info'] = $USERINFO;
            return true;
        }
        return true;
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
     * @param string $user
     * @return array|bool
     */
    public function getUserData($user, $requireGroups=true) {
        global $_TABLES, $_CONF, $_USER;

        $userdata = array();
        $uid = 1;

        $userdata['pass'] = _hash_gensalt_private(rand(),$this->itoa64);
        $userdata['user'] = $user;

        if ( $user == 'Guest' ) {
            $userdata['name'] = 'Guest';
            $userdata['mail'] = '';
        } else {
            if ( COM_isAnonUser() ) {
                $userdata['name'] = 'Guest';
                $userdata['mail'] = '';
            } else if ( strcasecmp($user,$_USER['username']) == 0 ) {
                $userdata['name'] = ($_USER['fullname'] == '' ? $_USER['username'] : $_USER['fullname']);
                $userdata['mail'] = $_USER['email'];
                $uid = $_USER['uid'];
            } else {
                $result = DB_query("SELECT uid,username,fullname,email FROM {$_TABLES['users']} WHERE username='".DB_escapeString($user)."'");
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
                    $userdata['mail'] = '';
                }
            }
        }
        $userdata['grps'] = array();
        $grps = SEC_getUserGroups($uid);
        $i = 0;
        foreach ($grps as $name=>$id) {
            $userdata['grps'][$i] = preg_replace_callback(
                                        '/\s/',
                                        function ($m) {
                                         return "_";
                                        },
                                        $name
                                    );
  	        $i++;
        }
        return $userdata;
    }

    /**
     * Return a count of the number of user which meet $filter criteria
     *
     * @author  Chris Smith <chris@jalakai.co.uk>
     *
     * @param array $filter
     * @return int
     */
    public function getUserCount($filter = array()) {

        if($this->users === null) $this->_loadUserData();

        if(!count($filter)) return count($this->users);

        $count = 0;
        $this->_constructPattern($filter);

        foreach($this->users as $user => $info) {
            $count += $this->_filter($user, $info);
        }

        return $count;
    }

    /**
     * Bulk retrieval of user data
     *
     * @author  Chris Smith <chris@jalakai.co.uk>
     *
     * @param   int   $start index of first user to be returned
     * @param   int   $limit max number of users to be returned
     * @param   array $filter array of field/pattern pairs
     * @return  array userinfo (refer getUserData for internal userinfo details)
     */
    public function retrieveUsers($start = 0, $limit = 0, $filter = array()) {

        if($this->users === null) $this->_loadUserData();

        ksort($this->users);

        $i     = 0;
        $count = 0;
        $out   = array();
        $this->_constructPattern($filter);

        foreach($this->users as $user => $info) {
            if($this->_filter($user, $info)) {
                if($i >= $start) {
                    $out[$user] = $info;
                    $count++;
                    if(($limit > 0) && ($count >= $limit)) break;
                }
                $i++;
            }
        }

        return $out;
    }

    /**
     * Only valid pageid's (no namespaces) for usernames
     *
     * @param string $user
     * @return string
     */
    public function cleanUser($user) {
        global $conf;
        return cleanID(str_replace(':', $conf['sepchar'], $user));
    }

    /**
     * Only valid pageid's (no namespaces) for groupnames
     *
     * @param string $group
     * @return string
     */
    public function cleanGroup($group) {
        global $conf;
        return cleanID(str_replace(':', $conf['sepchar'], $group));
    }

    /**
     * Load all user data
     *
     * loads the user file into a datastructure
     *
     * @author  Andreas Gohr <andi@splitbrain.org>
     */
    protected function _loadUserData() {
        global $config_cascade;

        $this->users = array();

        if(!@file_exists($config_cascade['glfusionauth.users']['default'])) return;

        $lines = file($config_cascade['glfusionauth.users']['default']);
        foreach($lines as $line) {
            $line = preg_replace('/#.*$/', '', $line); //ignore comments
            $line = trim($line);
            if(empty($line)) continue;

            $row    = explode(":", $line, 5);
            $groups = array_values(array_filter(explode(",", $row[4])));

            $this->users[$row[0]]['pass'] = $row[1];
            $this->users[$row[0]]['name'] = urldecode($row[2]);
            $this->users[$row[0]]['mail'] = $row[3];
            $this->users[$row[0]]['grps'] = $groups;
        }
    }

    /**
     * return true if $user + $info match $filter criteria, false otherwise
     *
     * @author   Chris Smith <chris@jalakai.co.uk>
     *
     * @param string $user User login
     * @param array  $info User's userinfo array
     * @return bool
     */
    protected function _filter($user, $info) {
        foreach($this->_pattern as $item => $pattern) {
            if($item == 'user') {
                if(!preg_match($pattern, $user)) return false;
            } else if($item == 'grps') {
                if(!count(preg_grep($pattern, $info['grps']))) return false;
            } else {
                if(!preg_match($pattern, $info[$item])) return false;
            }
        }
        return true;
    }

    /**
     * construct a filter pattern
     *
     * @param array $filter
     */
    protected function _constructPattern($filter) {
        $this->_pattern = array();
        foreach($filter as $item => $pattern) {
            $this->_pattern[$item] = '/'.str_replace('/', '\/', $pattern).'/i'; // allow regex characters
        }
    }
}