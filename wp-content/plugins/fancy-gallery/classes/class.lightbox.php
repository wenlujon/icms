<?php
Namespace WordPress\Plugin\Fancy_Gallery;

class Lightbox {
  private
    $core; # Pointer to the core object

  public function __construct($core){
    $this->core = $core;

    Add_Action('wp_enqueue_scripts', Array($this, 'Enqueue_Frontend_Styles'));
    Add_Action('wp_footer', Array($this, 'WP_Footer'));
  }

  public function Enqueue_Frontend_Styles(){
    $this->core->Enqueue_Frontend_Script($this->core->base_url . '/lightbox/js/blueimp-gallery.min.js');
    $this->core->Enqueue_Frontend_StyleSheet($this->core->base_url . '/lightbox/css/blueimp-gallery.min.css');
    $this->core->Enqueue_Frontend_StyleSheet($this->core->base_url . '/lightbox/css/blueimp-patches.css');
  }

  public function WP_Footer(){
    ?>
    <div class="blueimp-gallery blueimp-gallery-controls fancy-gallery-lightbox-container" style="display:none">
      <div class="slides"></div>
      <div class="title-description">
        <div class="title"></div>
        <div class="description"></div>
      </div>
      <a class="prev"> ‹ </a>
      <a class="next"> › </a>
      <a class="close"> × </a>
      <a class="play-pause"></a>
      <ol class="indicator"></ol>
    </div>
    <?php
  }

}