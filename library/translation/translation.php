<?php

load_theme_textdomain('theme', get_template_directory().'/library/translation');
$locale = get_locale();
$locale_file = get_template_directory()."/library/translation/{$locale}.php";
if ( is_readable($locale_file) ) {
  require_once($locale_file);
}

$_theme_translation_overrides = array(
  //'domain' => array(
  //  'message' => 'Translation'
  //),
);
add_filter('gettext', 'theme_translation_overrides', 10, 3);
function theme_translation_overrides($translated, $safe_text, $domain) {
  global $_theme_translation_overrides;
  if (
    isset( $_theme_translation_overrides[$domain] )
    && isset( $_theme_translation_overrides[$domain][$safe_text] )
  ) {
    return $_theme_translation_overrides[$domain][$safe_text];
  }
  return $translated;
}
//end
