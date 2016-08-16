<?php
/**
 * Plugin autotags: glFusion Auto-tags for DokuWiki
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 */
 
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

class syntax_plugin_autotags extends DokuWiki_Syntax_Plugin 
{
    function getInfo()
    {
        return array(
                 'author' => 'Aidan Hannigan',
                 'email'  => 'fusion@hannigan.uk.com',
                 'date'   => '2009-06-10',
                 'name'   => 'autotags',
                 'desc'   => 'Enable glFusion auto tags in DokuWiki',
                 'url'    => 'http://www.glfusion.org',
                 );
    }

    function getType()
    {
        return 'substition';
    }
 
    function getSort(){ return 299; }
 
    function connectTo($mode) 
    {
        $this->Lexer->addSpecialPattern('\[[a-z_\-]*:[a-zA-Z0-9_\-].*?\]', $mode, 'plugin_autotags');
    }
 
    function handle($match, $state, $pos, &$handler)
    {
        return $match;
    }
 
    function render($mode, &$renderer, $data) 
    {
        if($mode == 'xhtml'){
            $text=$this->_autotags($renderer, $data);
            $renderer->doc .= $text;
            return true;
        }
        return false;
    }
 
    function _autotags(&$renderer, $tag) 
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