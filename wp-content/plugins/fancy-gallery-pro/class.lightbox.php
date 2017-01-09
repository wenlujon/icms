<?php Namespace WordPress\Plugin\Fancy_Gallery;

class Lightbox {
  private
    $core; # Pointer to the core object

  public function __construct($core){
    $this->core = $core;

    Add_Action('wp_enqueue_scripts', Array($this, 'Enqueue_Frontend_Components'));
    Add_Action('wp_footer', Array($this, 'WP_Footer'));
  }

  public function Enqueue_Frontend_Components(){
    $this->core->Enqueue_Frontend_Script(Core::$base_url . '/lightbox/js/blueimp-gallery.min.js');
    $this->core->Enqueue_Frontend_StyleSheet(Core::$base_url . '/lightbox/css/blueimp-gallery.min.css');
    $this->core->Enqueue_Frontend_StyleSheet(Core::$base_url . '/lightbox/css/blueimp-patches.css');
  }

  public function WP_Footer(){
    ?>
    <div class="blueimp-gallery blueimp-gallery-controls fancy-gallery-lightbox-container" style="display:none">
      <div class="slides"></div>

      <div class="title-description">
        <?php If ($this->core->options->Get('title_description') == 'on'): ?>
        <div class="title"></div>
        <div class="description"></div>
        <?php EndIf ?>

        <?php /* If ($this->core->options->Get('share_links') == 'on'): ?>
        <div class="share-links">
          <a href="https://pinterest.com/pin/create/button/?url=%1$s&media=%2$s" class="facebook share icon">F</a>
          <a href="https://twitter.com/share?url=%1$s" class="twitter share icon">T</a>
          <a href="https://www.facebook.com/sharer/sharer.php?u=%s" class="pintarest share icon">P</a>
        </div>
        <?php EndIf */ ?>
      </div>

      <a class="prev" title="<?php Echo $this->core->t('Previous image') ?>"> ‹ </a>
      <a class="next" title="<?php Echo $this->core->t('Next image') ?>"> › </a>

      <?php If ($this->core->options->Get('close_button') == 'on'): ?>
      <a class="close" title="<?php _e('Close') ?>"> × </a>
      <?php EndIf ?>

      <a class="play-pause"></a>

      <?php If ($this->core->options->Get('indicator_thumbnails') == 'on'): ?>
      <ol class="indicator"></ol>
      <?php EndIf ?>
    </div>
    <?php
  }

}