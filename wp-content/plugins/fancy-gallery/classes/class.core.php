<?php
Namespace WordPress\Plugin\Fancy_Gallery;

class Core {
  public
    $base_url, # url to the plugin directory
    $version = '1.5.37', # Current release number
    $gallery, # The current gallery object while running shortcode
    $template_dir,
    $arr_stylesheets = Array(), # Array with stylesheet urls which should be loaded asynchronously
    $arr_javascripts = Array(), # Array with javascript urls which should be loaded asynchronously
    $gallery_post_type, # Pointer to the Gallery Post Type object
    $lightbox, # Pointer to the Lightbox object
    $options; # Pointer to the Options object
  
  public static
    $plugin_file,
    $plugin_folder;

  function __construct($plugin_file){
    self::$plugin_file = $plugin_file;
    self::$plugin_folder = DirName(self::$plugin_file);
    
    # Read base
    $this->Load_Base_Url();

    # Template directory
    $this->template_dir = WP_CONTENT_DIR . '/fancy-gallery-templates';

    # Helper classes
    #I18n::Load_TextDomain();
    $this->options = New Options($this);
    $this->gallery_post_type = New Gallery_Post_Type($this);
    If ($this->options->Get('lightbox') == 'on') $this->lightbox = new Lightbox($this);
    $this->mocking_bird = New Mocking_Bird($this);

    # This Plugin supports post thumbnails
    Add_Theme_Support('post-thumbnails');

    # Set Hooks
    Register_Activation_Hook(self::$plugin_file, Array($this, 'Plugin_Activation'));
    Add_Filter('the_content', Array($this, 'Filter_Content'), 11);
    Add_Filter('the_content_feed', Array($this, 'Filter_Feed_Content'));
    Add_Filter('the_excerpt_rss', Array($this, 'Filter_Feed_Content'));
    Add_Action('wp_enqueue_scripts', Array($this, 'Enqueue_Frontend_Scripts'));
    Add_Action('widgets_init', Array($this, 'Register_Widgets'));
    Add_Filter('post_class', Array($this, 'Filter_Post_Class'));
    Add_Action('pre_get_posts', Array($this, 'Filter_Attachment_Query'));
    Add_Filter('get_the_categories', Array($this, 'Filter_Get_The_Categories'));
    Add_Filter('the_category', Array($this, 'Filter_The_Category'), 10, 3);
    Add_Filter('get_the_tags', Array($this, 'Filter_Get_The_Tags'));
    Add_Filter('the_tags', Array($this, 'Filter_The_Tags'), 10, 5);

    Add_ShortCode('gallery', Array($this, 'ShortCode_Gallery'));

    If (!$this->options->Get('disable_excerpts')) Add_Filter('get_the_excerpt', Array($this, 'Filter_Excerpt'), 9);

    # Add to GLOBALs
    $GLOBALS[__CLASS__] = $this;
  }

  public function t($text, $content = False){
    return I18n::t($text, $content);
  }

  public function Plugin_Activation(){
    $this->gallery_post_type->Update_Post_Type_Name();
    $this->gallery_post_type->Register_Taxonomies();
    $this->gallery_post_type->Register_Post_Type();
    Flush_Rewrite_Rules();
  }
  
  private function Load_Base_Url(){
    $absolute_plugin_folder = RealPath(self::$plugin_folder);

    If (StrPos($absolute_plugin_folder, ABSPATH) === 0)
      $this->base_url = Get_Bloginfo('wpurl').'/'.SubStr($absolute_plugin_folder, Strlen(ABSPATH));
    Else
      $this->base_url = Plugins_Url(BaseName(self::$plugin_folder));

    $this->base_url = Str_Replace("\\", '/', $this->base_url); # Windows Workaround
  }

  function Register_Widgets(){
    Register_Widget('WordPress\Plugin\Fancy_Gallery\Widget\Random_Images');
    Register_Widget('WordPress\Plugin\Fancy_Gallery\Widget\Taxonomies');
    Register_Widget('WordPress\Plugin\Fancy_Gallery\Widget\Taxonomy_Cloud');
  }

  public function Enqueue_Frontend_StyleSheet($stylesheet_url){
    If (In_Array($this->options->Get('asynchronous_loading'), Array('all', 'css')))
      $this->arr_stylesheets[] = $stylesheet_url;
    Else
      WP_Enqueue_Style(BaseName($stylesheet_url, '.css'), $stylesheet_url);
  }

  public function Dequeue_Frontend_Stylehseet($stylesheet_url){
    If ($index = Array_Find($stylesheet_url, $this->arr_stylesheets) !== False)
      Unset($this->arr_stylesheets[$index]);
  }
  
  public function Enqueue_Frontend_Script($script_url){
    If (In_Array($this->options->Get('asynchronous_loading'), Array('all', 'js')))
      $this->arr_javascripts[] = $script_url;
    Else
      WP_Enqueue_Script(BaseName($script_url, '.js'), $script_url, Array('jquery'), Null, $this->options->Get('script_position') != 'header');
  }
  
  public function Dequeue_Frontend_Script($script_url){
    If ($index = Array_Find($script_url, $this->arr_javascripts) !== False)
      Unset($this->arr_javascripts[$index]);
  }

  public function Enqueue_Frontend_Scripts(){
    # Check for HTML5 galleries
    If (!Current_Theme_Supports('html5', 'gallery')){
      $this->Enqueue_Frontend_StyleSheet($this->base_url . '/assets/css/html5-galleries.css');
    }

    # Enqueue Template Stylesheets
    ForEach ($this->Get_Template_Files() AS $template_name => $template_properties){
      If ($stylesheet_uri = $template_properties['stylesheet_uri']){
        $this->Enqueue_Frontend_StyleSheet($stylesheet_uri);
      }
    }

    # Enqueue the Script
    WP_Enqueue_Script('fancy-gallery', $this->base_url . '/assets/js/fancy-gallery.js', Array('jquery'), $this->version, $this->options->Get('script_position') != 'header');
    $arr_options = $this->options->Get();
    Unset($arr_options['disable_update_notification'], $arr_options['update_username'], $arr_options['update_password']);
    $arr_options['stylesheets'] = $this->arr_stylesheets;
    $arr_options['javascripts'] = $this->arr_javascripts;
    WP_Localize_Script('fancy-gallery', 'FANCYGALLERY', $arr_options);
  }

  public function Filter_Attachment_Query($query){
    If (Is_Admin() && $query->Get('post_type') == 'attachment' && $query->Get('orderby') == 'menu_order ASC, ID' && $query->Get('order') == 'DESC')
      $query->Set('order', 'ASC');
  }

  public function Filter_Excerpt($excerpt){
    If ($GLOBALS['post']->post_type == $this->gallery_post_type->name && $this->gallery_post_type->Get_Meta('excerpt_type') == 'images' && !Post_Password_Required()){
      return $this->ShortCode_Gallery(Array(
        'number'          => $this->gallery_post_type->Get_Meta('excerpt_image_number'),
        'orderby'         => 'rand',
        'thumb_width'     => $this->gallery_post_type->Get_Meta('excerpt_thumb_width'),
        'thumb_height'    => $this->gallery_post_type->Get_Meta('excerpt_thumb_height'),
        'thumb_grayscale' => $this->gallery_post_type->Get_Meta('excerpt_thumb_grayscale'),
        'thumb_negate'    => $this->gallery_post_type->Get_Meta('excerpt_thumb_negate'),
        'template'        => $this->gallery_post_type->Get_Meta('excerpt_template')
      ));
    }
    Else {
      return $excerpt;
    }
  }

  public function Filter_Content($content){
    If ($GLOBALS['post']->post_type == $this->gallery_post_type->name && StrPos($content, '[gallery]') === False && StrPos($content, '[gallery ') === False && !Post_Password_Required()){
      return $content . $this->ShortCode_Gallery();
    }
    Else {
      return $content;
    }
  }

  public function Filter_Feed_Content($content){
    return $this->Filter_Excerpt($content);
  }

  public function Filter_Post_Class($arr_class){
    $arr_class[] = 'fancy-gallery-content-unit';
    return $arr_class;
  }

  public function Get_Template_Files(){
    $arr_template = Array_Unique(Array_Merge (
      (Array) Glob (self::$plugin_folder . '/templates/*.php' ),
      (Array) Glob (self::$plugin_folder . '/templates/*/*.php' ),

      (Array) Glob ( Get_StyleSheet_Directory() . '/*.php' ),
      (Array) Glob ( Get_StyleSheet_Directory() . '/*/*.php' ),

      Is_Child_Theme() ? (Array) Glob ( Get_Template_Directory() . '/*.php' ) : Array(),
      Is_Child_Theme() ? (Array) Glob ( Get_Template_Directory() . '/*/*.php' ) : Array(),

      (Array) Glob ( $this->template_dir . '/*.php' ),
      (Array) Glob ( $this->template_dir . '/*/*.php' )
    ));

    # Filter to add template files - you can use this filter to add template files to the user interface
    $arr_template = Apply_Filters('fancy_gallery_template_files', $arr_template);

    # Check if there template files
    If (Empty($arr_template)) return False;

    $arr_result = Array();
    $arr_sort = Array();
    ForEach ($arr_template AS $index => $template_file){
      # Read meta data from the template
      If ($arr_properties = $this->Get_Template_Properties($template_file)){
        $stylesheet_file = SubStr($template_file, 0, -4) . '.css';
        $stylesheet_url = Is_File($stylesheet_file);

        If ($stylesheet_url){
          If (StrPos($stylesheet_file, self::$plugin_folder) === 0){ # the template is inside the plugin folder
            $stylesheet_url = $this->base_url . SubStr($stylesheet_file, StrLen(self::$plugin_folder));
          }
          ElseIf (StrPos($stylesheet_file, ABSPATH) === 0){ # the template is inside the wordpress folder
            $stylesheet_url = Get_Bloginfo('wpurl').'/'.SubStr($stylesheet_file, Strlen(ABSPATH));
          }
          Else
            $stylesheet_url = $stylesheet_file = False;
        }

        $arr_result[$arr_properties['name']] = Array_Merge ($arr_properties, Array(
          'file' => $template_file,
          'stylesheet_file' => Is_File($stylesheet_file) ? $stylesheet_file : False,
          'stylesheet_uri' => $stylesheet_url
        ));
        $arr_sort[$arr_properties['name']] = StrToLower($arr_properties['name']);
      }
      Else Continue;
    }
    Array_MultiSort($arr_sort, SORT_STRING, SORT_ASC, $arr_result);

    return $arr_result;
  }

  public function Get_Template_Properties($template_file){
    # Check if this is a file
    If (!($template_file && Is_File ($template_file) && Is_Readable($template_file))) return False;

    # Read meta data from the template
    $arr_properties = Array_Merge(Get_File_Data ($template_file, Array(
      'name' => 'Fancy Gallery Template',
      'description' => 'Description',
      'author' => 'Author',
      'author_uri' => 'Author URI',
      'author_email' => 'Author E-Mail',
      'version' => 'Version'
    )), Array('file' => $template_file));

    # Check if there is a name for this template
    If (Empty($arr_properties['name']))
      return False;
    Else
      return $arr_properties;
  }

  public function Get_Default_Template(){
    # Which file set the user as default?
    $template_file = $this->options->Get('default_template_file');
    If (Is_File($template_file)) return $template_file;

    # Else:
    return self::$plugin_folder . '/templates/default/default.php';
  }

  public function Get_Default_Feed_Template(){
    return self::$plugin_folder . '/templates/default/default.php';
  }

  public function Generate_Gallery_Attributes($attributes){
    Global $post;

    $attributes = Is_Array($attributes) ? $attributes : Array();
    $gallery_meta = Array();

    # Get the Gallery Meta settings
    If (IsSet($attributes['id']) && !Empty($attributes['id']))
      $gallery_meta = $this->gallery_post_type->Get_Meta(Null, False, $attributes['id']);
    Else {
      $attributes['id'] = $post->ID;
      If ($post->post_type == $this->gallery_post_type->name){
        $gallery_meta = $this->gallery_post_type->Get_Meta(Null, False, $attributes['id']);
      }
      Else {
        $gallery_meta = $this->gallery_post_type->Default_Meta();
      }
    }

    # Merge Attributes
    $attributes = Array_Merge(Array(
      'id'              => False,
      'post_status'     => 'inherit',
      'post_type'       => 'attachment',
      'post_mime_type'  => 'image',
      'order'           => 'ASC',
      'orderby'         => IsSet($attributes['ids']) ? 'post__in' : 'menu_order',
      'columns'         => 4,
      'number'          => -1,
      'ids'             => False,
      'include'         => False,
      'exclude'         => False,
      'size'            => 'thumbnail',
      'link'            => 'file', # nothing else make sense
      'link_class'      => False,
      'thumb_width'     => False,
      'thumb_height'    => False,
      'thumb_grayscale' => False,
      'thumb_negate'    => False,
      'template'        => False
    ), $gallery_meta, $attributes);

    # Rename some keys
    $attributes = Array_Merge($attributes, Array(
      'post_parent' => $attributes['id'],
      'posts_per_page' => $attributes['number'],
      'include' => $attributes['include'].$attributes['ids']
    ));

    If (!Empty($attributes['include'])) $attributes['post_parent'] = Null;

    ForEach (Array('id', 'number', 'ids') AS $field) Unset($attributes[$field]);

    return $attributes;
  }

  public function Build_Gallery($arr_images, $attributes){
    # Prepare Gallery Array
    $this->gallery = (Object) Array(
      'attributes' => (Object) $attributes,
      'images' => Array()
    );

    # Generate Gallery HTML ID
    If (!Empty($attributes['post_parent'])){ # this gallery uses the post attachments
      $this->gallery->id = $attributes['post_parent'];
    }
    Else { # this gallery only includes images
      $this->gallery->id = Sanitize_Title($attributes['include']);
    }

  	# Build the Gallery object
    ForEach ($arr_images AS $id => &$image){
      # Thumb URL, width, height
      List($src, $width, $height) = WP_Get_Attachment_Image_Src($image->ID, $attributes['size']);

      $image->width = $width;
      $image->height = $height;
      $image->src = $src;
      $image->title = $image->post_title;
      $image->caption = $image->post_excerpt;
      $image->description = $image->post_content;
      $image->class = 'attachment-' . $attributes['size'];

      # Image Link
      If (Empty($image->href)) $image->href = WP_Get_Attachment_URL($image->ID);

      # Run filter
      $image->attributes = Apply_Filters('wp_get_attachment_image_attributes', Array(
        'src'    => $image->src,
        'width'  => $image->width,
        'height' => $image->height,
        'class'  => $image->class,
        'alt'    => $image->title,
        'title'  => $image->title
      ), $image, $attributes['size'] );

      # Write in Object
      $this->gallery->images[] = $image;
    }
  }

  public function Render_Gallery ($template_file){
    # Uses template filter
    $template_file = Apply_Filters('fancy_gallery_template', $template_file);

    # If there is no valid template file we bail out
    If (!Is_File($template_file)) $template_file = $this->Get_Default_Template();

    # Load template
    Ob_Start();
    Include $template_file;
    $code = Ob_Get_Clean();

    # Strip Whitespaces
    $code = PReg_Replace('/\s+/', ' ', $code);
    $code = Str_Replace('> <', '><', $code);
    $code = Trim($code);

    # Return
    return $code;
  }

  public function ShortCode_Gallery ($attributes = Array()){
    $attributes = $this->Generate_Gallery_Attributes($attributes);

  	# Get Images
    $arr_gallery = Get_Posts($attributes);

  	# There are no attachments
  	If (Empty($arr_gallery)) return False;

  	# Build the Gallery object
  	$this->Build_Gallery($arr_gallery, $attributes);

    # Load Template
    return $this->Render_Gallery($attributes['template']);
  }

  function Clear_Plugin_Update_Cache(){
    Update_Option('_site_transient_update_plugins', Array());
  }

  function Filter_Get_The_Categories($arr_categories){
    Global $post;
    
    If (!Is_Admin()){
      $gallery_taxonomy = 'gallery_category';
      $taxonomy_exists = Taxonomy_Exists($gallery_taxonomy);
      $is_gallery = $post->post_type == $this->gallery_post_type->name;
      $uses_post_categories = Is_Object_in_Taxonomy($post->post_type, 'category');
      $uses_gallery_categories = Is_Object_in_Taxonomy($post->post_type, $gallery_taxonomy);
      
      If ($taxonomy_exists && $is_gallery && !$uses_post_categories && $uses_gallery_categories){
        $arr_categories = Get_The_Terms($post->ID, $gallery_taxonomy);
        If (Is_Array($arr_categories)){
          ForEach ($arr_categories As &$category){
            _Make_Cat_Compat($category); # Compat mode for very very very old and deprecated themes...
          }
        }
      }
    }
    
    return $arr_categories;
  }

  function Filter_The_Category($str_category_list, $separator = Null, $parents = Null){
    Global $post;
    
    If (!Is_Admin()){
      $gallery_taxonomy = 'gallery_category';
      $taxonomy_exists = Taxonomy_Exists($gallery_taxonomy);
      $is_gallery = $post->post_type == $this->gallery_post_type->name;
      $uses_post_categories = Is_Object_in_Taxonomy($post->post_type, 'category');
      $uses_gallery_categories = Is_Object_in_Taxonomy($post->post_type, $gallery_taxonomy);
      
      If ($taxonomy_exists && $is_gallery && !$uses_post_categories && $uses_gallery_categories){
        $str_category_list = Get_The_Term_List($post->ID, $gallery_taxonomy, Null, $separator, Null);
        If (Empty($str_category_list)) $str_category_list = __('Uncategorized');
      }
    }
    
    return $str_category_list;
  }
  
  function Filter_Get_The_Tags($arr_tags){
    Global $post;
    
    If (!Is_Admin()){
      $gallery_taxonomy = 'gallery_tag';
      $taxonomy_exists = Taxonomy_Exists($gallery_taxonomy);
      $is_gallery = $post->post_type == $this->gallery_post_type->name;
      $uses_post_tags = Is_Object_in_Taxonomy($post->post_type, 'post_tag');
      $uses_gallery_tags = Is_Object_in_Taxonomy($post->post_type, $gallery_taxonomy);
      
      If ($taxonomy_exists && $is_gallery && !$uses_post_tags && $uses_gallery_tags){
        $arr_tags = Get_The_Terms($post->ID, $gallery_taxonomy);
      }
    }
    
    return $arr_tags;
  }

  function Filter_The_Tags($str_tag_list, $before, $separator, $after, $post_id){
    $post = Get_Post($post_id);

    If (!Is_Admin()){
      $gallery_taxonomy = 'gallery_tag';
      $taxonomy_exists = Taxonomy_Exists($gallery_taxonomy);
      $is_gallery = $post->post_type == $this->gallery_post_type->name;
      $uses_post_tags = Is_Object_in_Taxonomy($post->post_type, 'post_tag');
      $uses_gallery_tags = Is_Object_in_Taxonomy($post->post_type, $gallery_taxonomy);
      
      If ($taxonomy_exists && $is_gallery && !$uses_post_tags && $uses_gallery_tags){
        $str_tag_list = Get_The_Term_List($post_id, $gallery_taxonomy, $before, $separator, $after);
      }
    }
    
    return $str_tag_list;
  }

}