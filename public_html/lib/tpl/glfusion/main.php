<?php
/**
 * glFusion Template
 *
 * Integration with glFusion CMS
 *
 * @link   http://www.glfusion.org
 * @author Mark R. Evans - mark AT glfusion DOT org
 */

// must be run from within DokuWiki
if (!defined('DOKU_INC')) die();
?>
<?php tpl_metaheaders(); ob_start() ?>
<?php /*old includehook*/ @include(dirname(__FILE__).'/topheader.html')?>
<div class="dokuwiki">
  <?php html_msgarea()?>

  <div class="stylehead">
    <div class="header"><div class="clearer"></div></div>
    <?php if ( $ACT=='admin') echo _dw_admin_header();?>

    <?php /*old includehook*/ @include(dirname(__FILE__).'/header.html')?>

    <div class="bar" id="bar__top">
      <div class="bar-left" id="bar__topleft">
        <?php tpl_button('edit')?>
        <?php tpl_button('history')?>
      </div>

      <div class="bar-right" id="bar__topright">
        <?php tpl_button('recent')?>
        <?php tpl_searchform()?>&#160;
      </div>

      <div class="clearer"></div>
    </div>

    <?php if($conf['breadcrumbs']){?>
    <div class="breadcrumbs">
      <?php tpl_breadcrumbs()?>
      <?php //tpl_youarehere() //(some people prefer this)?>
    </div>
    <?php }?>

    <?php if($conf['youarehere']){?>
    <div class="breadcrumbs">
      <?php tpl_youarehere() ?>
    </div>
    <?php }?>

  </div>
  <?php flush()?>

  <?php /*old includehook*/ @include(dirname(__FILE__).'/pageheader.html')?>

  <div class="page">
    <!-- wikipage start -->
    <?php tpl_content()?>
    <!-- wikipage stop -->
  </div>

  <div class="clearer"></div>

  <?php flush()?>

  <div class="stylefoot">

    <div class="meta">
      <div class="user">
        <?php tpl_userinfo()?>
      </div>
      <div class="doc">
        <?php tpl_pageinfo()?>
      </div>
    </div>

   <?php /*old includehook*/ @include(dirname(__FILE__).'/pagefooter.html')?>

    <div class="bar" id="bar__bottom">
      <div class="bar-left" id="bar__bottomleft">
        <?php tpl_button('edit')?>
        <?php tpl_button('history')?>
        <?php tpl_button('revert')?>
     </div>
      <div class="bar-right" id="bar__bottomright">
        <?php if ( !COM_isAnonUser()) { tpl_button('subscribe'); }?>
        <?php tpl_button('media')?>
        <?php tpl_button('admin')?>
        <?php // tpl_button('profile')?>
        <?php // tpl_button('login')?>
        <?php tpl_button('index')?>
        <?php tpl_button('top')?>&#160;
      </div>
      <div class="clearer"></div>
    </div>

  </div>

  <?php tpl_license(false);?>
</div>
<?php /*old includehook*/ // @include(dirname(__FILE__).'/footer.html')?>

<div class="no"><?php /* provide DokuWiki housekeeping, required in all templates */ tpl_indexerWebBug()?></div>
<div style="clear:both;"></div><?php _dw_footer(); ?>
