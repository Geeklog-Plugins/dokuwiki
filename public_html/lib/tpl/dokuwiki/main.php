<?php
/**
 * DokuWiki Default Template 2012
 *
 * @link     http://dokuwiki.org/template
 * @author   Anika Henke <anika@selfthinker.org>
 * @author   Clarence Lee <clarencedglee@gmail.com>
 * @license  GPL 2 (http://www.gnu.org/licenses/gpl.html)
 */

if (!defined('DOKU_INC')) die(); /* must be run from within DokuWiki */

$hasSidebar = page_findnearest($conf['sidebar']);
$showSidebar = $hasSidebar && ($ACT=='show');

// must be run from within DokuWiki
if (!defined('DOKU_INC')) die();
?>
    <?PHP tpl_metaheaders(); ?>
    <div id="dokuwiki__site"><div id="dokuwiki__top" class="site <?php echo tpl_classes(); ?> <?php
        echo ($showSidebar) ? 'showSidebar' : ''; ?> <?php echo ($hasSidebar) ? 'hasSidebar' : ''; ?>">

        <?php include('tpl_header.php') ?>

        <div class="wrapper group">

            <?php if($showSidebar): ?>
                <!-- ********** ASIDE ********** -->
                <div id="dokuwiki__aside"><div class="pad aside include group">
                    <h3 class="toggle"><?php echo $lang['sidebar'] ?></h3>
                    <div class="content"><div class="group">
                        <?php tpl_flush() ?>
                        <?php tpl_includeFile('sidebarheader.html') ?>
                        <?php tpl_include_page($conf['sidebar'], true, true) ?>
                        <?php tpl_includeFile('sidebarfooter.html') ?>
                    </div></div>
                </div></div><!-- /aside -->
            <?php endif; ?>

            <!-- ********** CONTENT ********** -->
            <div id="dokuwiki__content"><div class="pad group">
                <?php html_msgarea() ?>

                <div class="pageId"><span><?php echo hsc($ID) ?></span></div>

                <div class="page group">
                    <?php tpl_flush() ?>
                    <?php tpl_includeFile('pageheader.html') ?>
                    <!-- wikipage start -->
                    <?php tpl_content() ?>
                    <!-- wikipage stop -->
                    <?php tpl_includeFile('pagefooter.html') ?>
                </div>

                <div class="docInfo"><?php tpl_pageinfo() ?></div>

                <?php tpl_flush() ?>
            </div></div><!-- /content -->

            <hr class="a11y" />

            <!-- PAGE ACTIONS -->
            <div id="dokuwiki__pagetools">
                <h3 class="a11y"><?php echo $lang['page_tools']; ?></h3>
                <div class="tools">
                    <ul>
                        <?php
                            $data = array(
                                'view'  => 'main',
                                'items' => array(
                                    'edit'      => tpl_action('edit',      true, 'li', true, '<span>', '</span>'),
                                    'revert'    => tpl_action('revert',    true, 'li', true, '<span>', '</span>'),
                                    'revisions' => tpl_action('revisions', true, 'li', true, '<span>', '</span>'),
                                    'backlink'  => tpl_action('backlink',  true, 'li', true, '<span>', '</span>'),
                                    'subscribe' => tpl_action('subscribe', true, 'li', true, '<span>', '</span>'),
                                    'top'       => tpl_action('top',       true, 'li', true, '<span>', '</span>')
                                )
                            );

                            // the page tools can be amended through a custom plugin hook
                            $evt = new Doku_Event('TEMPLATE_PAGETOOLS_DISPLAY', $data);
                            if($evt->advise_before()){
                                foreach($evt->data['items'] as $k => $html) echo $html;
                            }
                            $evt->advise_after();
                            unset($data);
                            unset($evt);
                        ?>
                    </ul>
                </div>
            </div>
        </div><!-- /wrapper -->

        <?php include('tpl_footer.php') ?>
    </div></div><!-- /site -->

    <div class="no"><?php tpl_indexerWebBug() /* provide DokuWiki housekeeping, required in all templates */ ?></div>
    <div id="screen__mode" class="no"></div><?php /* helper to detect CSS media query in script.js */ ?>
<div style="clear:both;"></div> <?PHP _dw_footer(); ?>
