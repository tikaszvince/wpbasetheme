<?php


// Sidebars & Widgetizes Areas
function theme_register_sidebars() {
  register_sidebar(array(
    'id' => 'sidebar1',
    'name' => __('Sidebar 1', 'theme'),
    'description' => __('The first (primary) sidebar.', 'theme'),
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h4 class="widgettitle">',
    'after_title' => '</h4>',
  ));
} // don't remove this bracket!


//end
