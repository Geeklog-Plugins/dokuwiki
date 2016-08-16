<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
/**
 * DokuWiki Default Template
 *
 * This is the template for the media manager popup
 *
 * You should leave the doctype at the very top - It should
 * always be the very first line of a document.
 *
 * @link   http://dokuwiki.org/templates
 * @author Andreas Gohr <andi@splitbrain.org>
 */
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $conf['lang']?>" lang="<?php echo $conf['lang']?>" dir="ltr">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>
    <?php echo hsc($lang['mediaselect'])?>
    [<?php echo strip_tags($conf['title'])?>]
  </title>
  <?php
  //Geeklog CMS  
  tpl_metaheaders_geeklog()?>
  <link rel="shortcut icon" href="<?php echo DOKU_TPL?>images/favicon.ico" />
    <script type="text/javascript">
    function $() {
      var elements = new Array();

      for (var i = 0; i < arguments.length; i++) {
        var element = arguments[i];
        if (typeof element == 'string')
          element = document.getElementById(element);

        if (arguments.length == 1)
          return element;

        elements.push(element);
      }

      return elements;
    }
    function dw_addEvent(element, type, handler) {
        // assign each event handler a unique ID
        if (!handler.$$guid) handler.$$guid = dw_addEvent.guid++;
        // create a hash table of event types for the element
        if (!element.events) element.events = {};
        // create a hash table of event handlers for each element/event pair
        var handlers = element.events[type];
        if (!handlers) {
            handlers = element.events[type] = {};
            // store the existing event handler (if there is one)
            if (element["on" + type]) {
                handlers[0] = element["on" + type];
            }
        }
        // store the event handler in the hash table
        handlers[handler.$$guid] = handler;
        // assign a global event handler to do all the work
        element["on" + type] = handleEvent;
    };
    // a counter used to create unique IDs
    dw_addEvent.guid = 1;
  </script>
</head>

<body>
<div id="media__manager" class="dokuwiki">
    <div id="media__left">
        <?php html_msgarea()?>
        <h1><?php echo hsc($lang['mediaselect'])?></h1>

        <?php /* keep the id! additional elements are inserted via JS here */?>
        <div id="media__opts"></div>

        <?php tpl_mediaTree() ?>
    </div>

    <div id="media__right">
        <?php tpl_mediaContent() ?>
    </div>
</div>
</body>
</html>
