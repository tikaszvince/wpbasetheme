<?php


// Sidebars & Widgetizes Areas
function theme_register_sidebars() {
  register_sidebar(array(
    'id' => 'sidebar',
    'name' => __('Default sidebar', 'theme'),
    'description' => __('The first (primary) sidebar.', 'theme'),
    'before_widget' => '<section id="%1$s" class="widget %2$s">',
    'after_widget' => '</section>',
    'before_title' => '<h4 class="widgettitle">',
    'after_title' => '</h4>',
  ));
  register_sidebar(array(
    'id' => 'footerwidgets',
    'name' => __('Footer widgets', 'theme'),
    'description' => __('Widgets in footer area.', 'theme'),
    'before_widget' => '<section id="%1$s" class="widget span4 %2$s">',
    'after_widget' => '</section>',
    'before_title' => '<h4 class="widgettitle">',
    'after_title' => '</h4>',
  ));

  if ( function_exists('theme_additional_register_sidebars') ) {
    theme_additional_register_sidebars();
  }
}

//end
