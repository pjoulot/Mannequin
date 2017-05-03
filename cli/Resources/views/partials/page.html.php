<?php $view->extend('partials/html'); ?>
<?php $view['slots']->start('styles'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.3.1/css/foundation.min.css" />
<?php $view['slots']->stop(); ?>
<?php $view['slots']->start('scripts'); ?>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.3.1/js/foundation.min.js"></script>
<script type="text/javascript">
  jQuery(document).ready(function() {
    jQuery(document).foundation();
  });
</script>
<?php $view['slots']->stop(); ?>
<div class="top-bar">
  <div class="top-bar-title">
    <span data-responsive-toggle="responsive-menu" data-hide-for="medium">
      <button class="menu-icon dark" type="button" data-toggle></button>
    </span>
    <strong>Patterns</strong>
  </div>
  <div id="responsive-menu">
    <div class="top-bar-left">
      <?php print $view->render('partials/navigation', ['collection' => $root]); ?>
    </div>
  </div>
</div>
<div class="row">
  <div class="columns small-12">
    <?php $view['slots']->output('_content'); ?>
  </div>
</div>