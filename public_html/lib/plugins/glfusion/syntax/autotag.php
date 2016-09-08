<?php
/**
 * Plugin autotags: glFusion Auto-tags for DokuWiki
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 */

if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

class syntax_plugin_glfusion_autotag extends DokuWiki_Syntax_Plugin
{
    function getInfo()
    {
        return array('author' => 'Mark R. Evans',
                     'email'  => 'mark@glfusion.org',
                     'date'   => '2014-05-21',
                     'name'   => 'glFusion',
                     'desc'   => 'glFusion Integration Plugin',
                     'url'    => 'http://www.glfusion.org');
    }

    function getType()
    {
        return 'substition';
    }

    function getSort(){ return 299; }

    function connectTo($mode)
    {
        $this->Lexer->addSpecialPattern('\[[a-z_\-]*:[a-zA-Z0-9_\-].*?\]', $mode, 'plugin_glfusion_autotag');
    }

    function handle($match, $state, $pos, Doku_Handler $handler)
    {
        return $match;
    }

    function render($mode, Doku_Renderer $renderer, $data)
    {
        if($mode == 'xhtml'){
            $text=$this->_glfusion($renderer, $data);
            $renderer->doc .= $text;
            return true;
        }
        return false;
    }

    function _glfusion(Doku_Renderer $renderer, $tag)
    {
        global $_CONF;
        require_once $_CONF['path_system'].'lib-plugins.php';

        $newhtml = PLG_replaceTags($tag);

        if(preg_match('/^<a /', $newhtml))
            return "<a class='wikilink1' " . substr($newhtml, 2);

        return $newhtml;
    }
}
?>