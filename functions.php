<?php
ini_set('display_errors', 1);
/************* INCLUDE NEEDED FILES ***************/
// This has to be the first include so theme additionals could inject settings
require_once('library/theme-admin.php');
require_once('library/4image.hcard.microformat.php');
require_once('library/image-sizes.php');
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

//end

