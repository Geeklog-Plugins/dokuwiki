<?php
/**
 * glFusion Integration Plugin
 *
 * @author     Mark R. Evans <mark@glfusion.org>
 */

if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once DOKU_PLUGIN.'action.php';

class action_plugin_glfusion_glfusion extends DokuWiki_Action_Plugin {

    /**
     * return some info
     */
    public function getInfo(){
        return array('author' => 'Mark R. Evans',
                     'email'  => 'mark@glfusion.org',
                     'date'   => '2014-05-21',
                     'name'   => 'glFusion',
                     'desc'   => 'glFusion Integration Plugin',
                     'url'    => 'http://www.glfusion.org');
    }

    /**
     * Register its handlers with the DokuWiki's event controller
     */
    public function register( Doku_Event_Handler $controller ) {
        $controller->register_hook('IO_WIKIPAGE_WRITE', 'AFTER', $this,'_glfusion',array());
    }

    /**
     * Hook page save action.
     *
     * @author Mark R. Evans <mark@glfusion.org>
     */
    public function _glfusion(&$event, $param) {
        if ( $event->data[3] ) return false;

        if ( $event->data[1] != '' ) {
            $id = $event->data[1] . ':'. $event->data[2];
        } else {
            $id = $event->data[2];
        }
        if ( $event->data[0][1] == '' ) {
            PLG_itemDeleted($id,'dokuwiki');
        } else {
            PLG_itemSaved( $id,'dokuwiki');
        }
        CACHE_remove_instance('whatsnew');
    }
}
?>