<?php get_header(); ?>
  <h1 class="page-title">
    <?php echo __('Search Results for:', 'theme'), ' <span>'.get_search_query().'</span>'; ?>
  </h1>
  <div id="content"><div class="row-fluid">
    <div id="main" class="span8 first clearfix" role="main">
      <?php get_template_part('loop','index'); ?>
    </div> <!-- end #main -->
    <?php get_sidebar(); ?>
  </div></div> <!-- end #content -->
<?php get_footer(); ?>
