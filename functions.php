<?php
/************* INCLUDE NEEDED FILES ***************/
require_once('library/start.php'); // if you remove this, theme will break
require_once('library/translation/translation.php'); // this comes turned off by default

// Thumbnail sizes
add_image_size('theme-thumb-600', 600, 150, true);
add_image_size('theme-thumb-300', 300, 100, true);

require_once('library/sidebar.php');
require_once('library/comment.php');
require_once('library/blocks.php');

//end

