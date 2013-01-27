<?php
remove_filter('language_attributes', 'fbcomments_schema');
?>
<!DOCTYPE html>
<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js ie lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js ie lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js ie lt-ie9"><![endif]-->
<!--[if (IE 9)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js ie ie9"><![endif]-->
<!--[if (gte IE 9)|(gt IEMobile 7)|!(IEMobile)|!(IE)]><!--><html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->
<head>
<meta charset="utf-8">
<title><?php wp_title(''); ?></title>

<!-- Google Chrome Frame for IE -->
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

<!-- mobile meta (hooray!) -->
<meta name="HandheldFriendly" content="True" />
<meta name="MobileOptimized" content="320" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<!-- icons & favicons (for more: http://themble.com/support/adding-icons-favicons/) -->
<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/img/favicon.ico" />
<link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/img/apple-touch-icon.png" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<!-- wordpress head functions -->
<?php wp_head(); ?>
<!-- end of wordpress head -->

<!-- drop Google Analytics Here -->
<!-- end analytics -->

</head>

<body <?php body_class(); ?>>
<div id="container" class="container-fluid">
  <header class="header page-header" role="banner"><div class="inner-header">
    <a id="logo" class="h1" href="<?php echo home_url(); ?>" rel="nofollow">
      <img src="<?php echo get_template_directory_uri(); ?>/img/logo.header.png" alt="<?php bloginfo('name'); ?>" title="<?php bloginfo('name'); ?>" />
    </a>
    <!-- if you'd like to use the site description you can un-comment it below -->
    <?php // bloginfo('description'); ?>

    <nav role="navigation" class="page-navigation navbar">
      <div class="navbar-inner"><div class="container">
        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
          Menü
          <i><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></i>
        </a>
        <div class="nav-collapse">
          <?php theme_main_nav(); ?>
        </div>
      </div></div><!-- /.container /.navbar-inner -->
    </nav>

    <aside class="utils">
      <section class="socials clearfix">
        <?php theme_social_links() ?>
      </section>
      <section class="search clearfix">
        <?php get_search_form() ?>
      </section>
    </aside>
  </div></header> <!-- end header -->
