<?php
Namespace WordPress\Plugin\Fancy_Gallery;

class Options {
  private
    $arr_option_box, # Meta boxes for the option page
    $page_slug = '', # Slug for the options page
    $core; # Pointer to the core object

  public function __construct($core){
    $this->core = $core;
    $this->page_slug = Sanitize_Title(Str_Replace(Array('\\', '/', '_'), '-', __CLASS__));

    # Option boxes
    $this->arr_option_box = Array(
      'main' => Array(),
      'side' => Array()
    );

    Add_Action('admin_menu', Array($this, 'Add_Options_Page'));
  }

  public function Add_Options_Page(){
    $handle = Add_Options_Page (
      I18n::t('Fancy Gallery Options'),
      I18n::t('Fancy Gallery'),
      'manage_options',
      $this->page_slug,
      Array($this, 'Print_Options_Page')
    );

    # Add JavaScript to this handle
    Add_Action ('load-' . $handle, Array($this, 'Load_Options_Page'));

    # Add option boxes
    $this->Add_Option_Box(I18n::t('Lightbox'), Core::$plugin_folder . '/options-page/lightbox.php');
    $this->Add_Option_Box(I18n::t('Templates'), Core::$plugin_folder . '/options-page/templates.php', 'main', 'closed');
    $this->Add_Option_Box(I18n::t('User rights'), Core::$plugin_folder . '/options-page/user-rights.php', 'main', 'closed');

    $this->Add_Option_Box(I18n::t('Taxonomies'), Core::$plugin_folder . '/options-page/taxonomies.php', 'side');
    $this->Add_Option_Box(I18n::t('Gallery "Excerpts"'), Core::$plugin_folder . '/options-page/excerpt.php', 'side');
    $this->Add_Option_Box(I18n::t('Archive Url'), Core::$plugin_folder . '/options-page/archive-link.php', 'side');
  }

  private function Get_Options_Page_Url($parameters = Array()){
    $url = Add_Query_Arg(Array('page' => $this->page_slug), Admin_Url('options-general.php'));
    If (Is_Array($parameters) && !Empty($parameters)) $url = Add_Query_Arg($parameters, $url);
    return $url;
  }

  public function Load_Options_Page(){
    # Check if the user trys to delete a template
    If (IsSet($_GET['delete']) && $this->core->Get_Template_Properties ($_GET['delete'])){ # You can only delete Fancy Gallery Templates!
      Unlink($_GET['delete']);
      WP_Redirect( $this->Get_Options_Page_Url(Array('template_deleted' => 'true')) );
    }
    ElseIf (IsSet($_GET['delete'])){
      WP_Die(I18n::t('Error while deleting: ' . HTMLSpecialChars($_GET['delete'])));
    }

    # If the Request was redirected from a "Save Options"-Post
    If (IsSet($_REQUEST['options_saved'])) Flush_Rewrite_Rules();

    # If this is a Post request to save the options
    $options_saved = $this->Save_Options();
    If ($options_saved)
      WP_Redirect( $this->Get_Options_Page_Url(Array('options_saved' => 'true')) );

    WP_Enqueue_Script('dashboard');
    WP_Enqueue_Style('dashboard');

    WP_Enqueue_Script('fancy-gallery-options-page', $this->core->base_url . '/options-page/options-page.js', Array('jquery'), $this->core->version, True);
    WP_Enqueue_Style('fancy-gallery-options-page', $this->core->base_url . '/options-page/options-page.css' );

    # Remove incompatible JS Libs
    WP_Dequeue_Script('post');
  }

  public function Print_Options_Page(){
    ?>
    <div class="wrap">
      <h1><?php Echo I18n::t('Fancy Gallery Settings') ?></h1>

      <?php If (IsSet($_GET['options_saved'])): ?>
      <div id="message" class="updated fade">
        <p><strong><?php _e('Settings saved.') ?></strong></p>
      </div>
      <?php EndIf ?>

      <?php If (IsSet($_GET['template_installed'])): ?>
      <div id="message" class="updated fade">
        <p><strong><?php echo I18n::t('Template installed.') ?></strong></p>
      </div>
      <?php EndIf ?>

      <?php If (IsSet($_GET['template_deleted'])): ?>
      <div id="message" class="updated fade">
        <p><strong><?php echo I18n::t('Template deleted.') ?></strong></p>
      </div>
      <?php EndIf ?>

      <form method="post" action="" enctype="multipart/form-data">
      <div class="metabox-holder">
        <div class="postbox-container left meta-box-sortables">
          <?php ForEach ($this->arr_option_box['main'] AS $box) : ?>
          <div class="postbox should-be-<?php Echo $box['state'] ?>">
            <button type="button" class="handlediv button-link" aria-expanded="true">
              <span class="screen-reader-text"><?php PrintF(I18n::t('Toggle panel: %s'), $box['title']) ?></span>
              <span class="toggle-indicator" aria-hidden="true"></span>
            </button>
            <h3 class="hndle"><span><?php Echo $box['title'] ?></span></h3>
            <div class="inside"><?php Include $box['file'] ?></div>
          </div>
          <?php EndForEach ?>
        </div>

        <div class="postbox-container right meta-box-sortables">
          <?php ForEach ($this->arr_option_box['side'] AS $box) : ?>
          <div class="postbox should-be-<?php Echo $box['state'] ?>">
            <button type="button" class="handlediv button-link" aria-expanded="true">
              <span class="screen-reader-text"><?php PrintF(I18n::t('Toggle panel: %s'), $box['title']) ?></span>
              <span class="toggle-indicator" aria-hidden="true"></span>
            </button>
            <h3 class="hndle"><span><?php Echo $box['title'] ?></span></h3>
            <div class="inside"><?php Include $box['file'] ?></div>
          </div>
          <?php EndForEach ?>
        </div>
      </div>

      <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>">
      </p>

      </form>
    </div>
    <?php
  }

  public function Add_Option_Box($title, $include_file, $column = 'main', $state = 'opened'){
    # Check the input
    If (!Is_File($include_file)) return False;
    If (Empty($title)) $title = '&nbsp;';

    # Column (can be 'side' or 'main')
    If ($column != 'main') $column = 'side';

    # State (can be 'opened' or 'closed')
    If ($state != 'opened') $state = 'closed';

    # Add a new box
    $this->arr_option_box[$column][] = Array('title' => $title, 'file' => $include_file, 'state' => $state);
  }

  private function Save_Options(){
    # Check if this is a post request
    If (Empty($_POST)) return False;

    # Add Capabilities
    If (IsSet($_POST['capabilities']) && Is_Array($_POST['capabilities'])){
      ForEach ($_POST['capabilities'] AS $role_name => $arr_role){
        If (!$role = get_role($role_name)) Continue;
        ForEach ((Array) $arr_role AS $capability => $yes_no){
          If ($yes_no == 'yes')
            $role->add_cap($capability);
          Else
            $role->remove_cap($capability);
        }
      }
      Unset ($_POST['capabilities']);
    }

    # Clean the Post array
    $_POST = StripSlashes_Deep($_POST);
    $options = Array_Filter($_POST, function($value){ return $value == '0' || !Empty($value); });

    # Save Options
    Update_Option (__CLASS__, $options);
    Delete_Option ('wp_plugin_fancy_gallery_pro');
    Delete_Option ('wp_plugin_fancy_gallery');

    # We delete the update cache
    $this->core->Clear_Plugin_Update_Cache();

    return True;
  }

  private function Default_Options(){
    return Array(
      'lightbox' => True,
      'continuous' => False,
      'title_description' => True,
      'close_button' => True,
      'indicator_thumbnails' => True,
      'slideshow_speed' => 4000, # Slideshow speed in milliseconds
      'preload_images' => 2,
      'animation_speed' => 400,
      'stretch_images' => False,

      'gallery_taxonomy' => Array(),

      'disable_excerpts' => False,
      'excerpt_thumb_width' => Get_Option('thumbnail_size_w'),
      'excerpt_thumb_height' => Get_Option('thumbnail_size_h'),
      'excerpt_image_number' => 3,
      'asynchronous_loading' => 'all',

      'deactivate_archive' => False
    );
  }

  public function Get($key = Null, $default = False){
    # Read Options
    $arr_option = Array_Merge (
      (Array) $this->Default_Options(),
      (Array) Get_Option('wp_plugin_fancy_gallery_pro'),
      (Array) Get_Option('wp_plugin_fancy_gallery'),
      (Array) Get_Option(__CLASS__)
    );

    # Locate the option
    If ($key == Null)
      return $arr_option;
    ElseIf (IsSet($arr_option[$key]))
      return $arr_option[$key];
    Else
      return $default;
  }

}