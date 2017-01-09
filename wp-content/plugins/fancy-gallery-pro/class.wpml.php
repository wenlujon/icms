<?php Namespace WordPress\Plugin\Fancy_Gallery;

class WPML {
  private
    $i18n, # Pointer to the i18n object
    $wpml_is_active = False; # Will become true if WPML is active

  public function __construct($i18n){
    $this->i18n = $i18n;

    # Define filters
    Add_Action('widgets_init', Array($this, 'Find_WPML'));
    Add_Filter('gettext_with_context', Array($this, 'Filter_Gettext_with_Context'), 1, 4);
  }

  public function Find_WPML(){
    $this->wpml_is_active = Defined('ICL_SITEPRESS_VERSION');
  }
  
  public function Is_Active(){
    return $this->wpml_is_active;
  }

  public function Filter_Gettext_with_Context($translation, $text, $context, $domain){
    # If you are using WPML the post type slug MUST NOT be translated! You can translate your slug in WPML
    If ($this->wpml_is_active && $context == 'URL slug' && $domain == $this->i18n->Get_Text_Domain())
      return $text;
    Else
      return $translation;
  }

}