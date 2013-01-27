<?php
/************* INCLUDE NEEDED FILES ***************/
require_once('library/4image.hcard.microformat.php');
// This has to be the first include so theme additionals could inject settings
require_once('library/theme-admin.php');
require_once('library/start.php');
require_once('library/translation/translation.php');
require_once('library/sidebar.php');
require_once('library/comment.php');
require_once('library/blocks.php');
require_once('library/widgets.php');

// additional functions, filters and actions
require_once('library/theme-additionals.php');

// abd at the and init Theme_Admin
Theme_Admin::init();

// Thumbnail sizes
function theme_get_image_sizes() {
  return array(
    'theme-thumb-single-head' => array(
      'width' => 620,
      'height' => null,
      'crop' => false,
    ),
    'theme-thumb-single-head-cropped' => array(
      'width' => 620,
      'height' => 310,
      'crop' => true,
    ),
    'theme-thumb-600' => array(
      'width' => 600,
      'height' => 150,
      'crop' => true,
    ),
    'theme-thumb-300' => array(
      'width' => 300,
      'height' => 170,
      'crop' => true,
    ),
    'theme-thumb-80' => array(
      'width' => 80,
      'height' => 65,
      'crop' => true,
    ),
  );
}
foreach( theme_get_image_sizes() as $name => $set ) {
  add_image_size($name, $set['width'], $set['height'], $set['crop']);
}
//end

