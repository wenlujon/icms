<?php
Namespace WordPress\Plugin\Fancy_Gallery;

class Mocking_Bird {
  private
    $core, # Pointer to the core object
    $post_type; # The post type we talk about

  public function __construct($core){
    $this->core = $core;
    $this->post_type = $this->core->gallery_post_type->name;
    Add_Action('admin_init', Array($this, 'checkCreatedPost'));
    Add_Action('untrash_post', Array($this, 'checkUntrashedPost'));
    Add_Action('admin_footer', Array($this, 'changeCreateGalleryLink'));
    Add_Action('admin_bar_menu', Array($this, 'removeAdminBarCreateGalleryButton'), 999);
  }

  function Pro_Notice($message = 'setting', $output = True){
    $arr_message = Array(
      'upgrade' => I18n::t('Upgrade to Pro'),
      'upgrade_url' => '%s',
      'feature' => I18n::t('This feature is available in the <a href="%s" target="_blank">premium version</a>.'),
      'unlock' => SPrintF('<a href="%%s" title="%s" class="unlock" target="_blank"><span class="dashicons dashicons-lock"></span></a>', I18n::t('Unlock this feature')),
      'setting' => I18n::t('This setting is changeable in the <a href="%s" target="_blank">premium version</a>.'),
      'custom_tax' => I18n::t('Do you need a special taxonomy for your website? No problem! Just <a href="%s" target="_blank">get in touch</a>.'),
      'widget' => I18n::t('This widget is available in the <a href="%s" target="_blank">premium version</a>. For now there will be no output in the front end of your website.'),
      'count_limit' => I18n::t('In the <a href="%s" target="_blank">Premium Version of Fancy Gallery</a> you can take advantage of the gallery management without any limitations.'),
    );

    If (IsSet($arr_message[$message])){
      $message = SPrintF($arr_message[$message], I18n::t('http://dennishoppe.de/en/wordpress-plugins/fancy-gallery', 'Link to the authors website'));
      If ($output) Echo $message;
      Else return $message;
    }
    Else
      return False;
  }

  function getNumberOfGalleries($limit = -1){
    Static $count;
    If ($count)
      return $count;
    Else
      return $count = Count(Get_Posts(Array('post_type' => $this->post_type, 'post_status' => 'any', 'numberposts' => $limit)));
  }

  function checkGalleryCount(){
    return $this->getNumberOfGalleries(3) < 3;
  }

  function checkCreatedPost(){
    If (BaseName($_SERVER['SCRIPT_NAME']) == 'post-new.php' && IsSet($_GET['post_type']) && $_GET['post_type'] == $this->post_type && !$this->checkGalleryCount())
      $this->printGalleryCountLimit();
  }

  function checkUntrashedPost($post_id){
    If (Get_Post_Type($post_id) == $this->post_type && !$this->checkGalleryCount()) $this->printGalleryCountLimit();
  }

  function printGalleryCountLimit(){
    WP_Die(
      SPrintF('<p>%s</p><p>%s</p>',
        $this->Pro_Notice('count_limit', False),
        SPrintF('<a href="%s" class="button">%s</a>', Admin_URL('edit.php?post_type=' . $this->post_type), I18n::t('&laquo; Back to your galleries'))
      )
    );
  }

  function changeCreateGalleryLink(){
    If (!$this->checkGalleryCount()): ?>
    <script type="text/javascript">
    (function($){
      $('a[href*="post-new.php?post_type=<?php Echo $this->post_type ?>"]')
        .text('<?php $this->Pro_Notice('upgrade') ?>')
        .attr({
          'title': '<?php $this->Pro_Notice('upgrade') ?>',
          'href': '<?php $this->Pro_Notice('upgrade_url') ?>',
          'target': '_blank'
        })
        .css({
          'color': '#46b450',
          'font-weight': 'bold'
        });
    })(jQuery);
    </script>
    <?php EndIf;
  }

  function removeAdminBarCreateGalleryButton($admin_bar){
    If (!$this->checkGalleryCount()) $admin_bar->Remove_Node(SPrintF('new-%s', $this->post_type));
  }

}