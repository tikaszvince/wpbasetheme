<?php get_header(); ?>
  <h1 class="page-title"><?php _e('Epic 404 - Article Not Found', 'theme'); ?></h1>
  <div id="content"><div class="row-fluid">
    <div id="main" class="span12 first clearfix" role="main">
      <?php theme_error_no_post(false); ?>
    </div> <!-- end #main -->
  </div></div> <!-- end #content -->

<?php get_footer(); ?>
