<?php Namespace WordPress\Plugin\Fancy_Gallery;

abstract class WPML {

  static public function Init(){
    Add_Filter('gettext_with_context', Array(__CLASS__, 'Filter_Gettext_with_Context'), 1, 4);
  }

  static public function isActive(){
    return Defined('ICL_SITEPRESS_VERSION');
  }

  static public function Filter_Gettext_with_Context($translation, $text, $context, $domain){
    # If you are using WPML the post type slug MUST NOT be translated! You can translate your slug in WPML
    If (self::isActive() && $context == 'URL slug' && $domain == I18n::getTextDomain())
      return $text;
    Else
      return $translation;
  }

}

WPML::Init();