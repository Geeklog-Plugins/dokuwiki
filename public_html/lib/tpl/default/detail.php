<?php
/**
 * DokuWiki Image Detail Template
 *
 * This is the template for displaying image details
 *
 * You should leave the doctype at the very top - It should
 * always be the very first line of a document.
 *
 * @link   http://dokuwiki.org/templates
 * @author Andreas Gohr <andi@splitbrain.org>
 */

// must be run from within DokuWiki
if (!defined('DOKU_INC')) die();

if (file_exists('../../../lib-common.php')) require_once ('../../../lib-common.php');
//Geeklog header
ob_start();
tpl_metaheaders();
$mheader = ob_get_clean();
$ptitle = tpl_pagetitle(null,1);
global $_DW_CONF;
switch( $_DW_CONF['displayblocks'] ) {
	case 0 : // left only
	case 2 :
		echo COM_siteHeader('menu',$ptitle,$mheader);
		break;
	case 1 : // right only
	case 3 :
		echo COM_siteHeader('none',$ptitle,$mheader);
		break;
	default :
		echo COM_siteHeader('menu',$ptitle,$mheader);
		break;
}
?>
<div class="dokuwiki">
  <?php html_msgarea()?>

  <div class="page">
    <?php if($ERROR){ print $ERROR; }else{ ?>

    <h1><?php echo hsc(tpl_img_getTag('IPTC.Headline',$IMG))?></h1>

    <div class="img_big">
      <?php tpl_img(900,700) ?>
    </div>

    <div class="img_detail">
      <p class="img_caption">
        <?php print nl2br(hsc(tpl_img_getTag('simple.title'))); ?>
      </p>

      <p>&larr; <?php echo $lang['img_backto']?> <?php tpl_pagelink($ID)?></p>

      <dl class="img_tags">
        <?php
          $t = tpl_img_getTag('Date.EarliestTime');
          if($t) print '<dt>'.$lang['img_date'].':</dt><dd>'.dformat($t).'</dd>';

          $t = tpl_img_getTag('File.Name');
          if($t) print '<dt>'.$lang['img_fname'].':</dt><dd>'.hsc($t).'</dd>';

          $t = tpl_img_getTag(array('Iptc.Byline','Exif.TIFFArtist','Exif.Artist','Iptc.Credit'));
          if($t) print '<dt>'.$lang['img_artist'].':</dt><dd>'.hsc($t).'</dd>';

          $t = tpl_img_getTag(array('Iptc.CopyrightNotice','Exif.TIFFCopyright','Exif.Copyright'));
          if($t) print '<dt>'.$lang['img_copyr'].':</dt><dd>'.hsc($t).'</dd>';

          $t = tpl_img_getTag('File.Format');
          if($t) print '<dt>'.$lang['img_format'].':</dt><dd>'.hsc($t).'</dd>';

          $t = tpl_img_getTag('File.NiceSize');
          if($t) print '<dt>'.$lang['img_fsize'].':</dt><dd>'.hsc($t).'</dd>';

          $t = tpl_img_getTag('Simple.Camera');
          if($t) print '<dt>'.$lang['img_camera'].':</dt><dd>'.hsc($t).'</dd>';

          $t = tpl_img_getTag(array('IPTC.Keywords','IPTC.Category','xmp.dc:subject'));
          if($t) print '<dt>'.$lang['img_keywords'].':</dt><dd>'.hsc($t).'</dd>';

        ?>
      </dl>
      <?php //Comment in for Debug// dbg(tpl_img_getTag('Simple.Raw'));?>
    </div>

  <?php } ?>
  </div>
</div>
<?php
// Geeklog footer
global $_DW_CONF;
switch( $_DW_CONF['displayblocks'] ) {
    case 0 : // left only
    case 3 : // none
        echo COM_siteFooter();
        break;
    case 1 : // right only
    case 2 : // left and right
        echo COM_siteFooter( true );
        break;
    default :
        echo COM_siteFooter();
        break;
}
?>

