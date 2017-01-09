<?php
Namespace WordPress\Plugin\Fancy_Gallery;

class Gallery_Post_Type {
  public
    $core, # Pointer to the core object
    $name = 'fancy-gallery', # Name of the gallery post type
    $meta_field = 'fancy-gallery-meta', # Name of the meta field which is used in the post type meta boxes
    $arr_meta_box, # Meta boxes for the gallery post type
    $arr_taxonomies; # All buildIn Gallery Taxonomies - also the inactive ones.

  function __construct($core){
    $this->core = $core;
    $this->arr_meta_box = Array();

    Add_Action('init', Array($this, 'Register_Taxonomies'));
    Add_Action('init', Array($this, 'Register_Post_Type'));
    Add_Action('init', Array($this, 'Add_Taxonomy_Archive_Urls'), 99);
    Add_Filter('image_upload_iframe_src', Array($this, 'Image_Upload_Iframe_Src'));
    Add_Filter('post_updated_messages', Array($this, 'Updated_Messages'));
    Add_Action(SPrintF('save_post_%s', $this->name), Array($this, 'Save_Meta_Box'), 10, 2);

    If (IsSet($_REQUEST['strip_tabs'])){
      Add_Action('media_upload_gallery', Array($this, 'Add_Media_Upload_Style'));
      Add_Action('media_upload_image', Array($this, 'Add_Media_Upload_Style'));
      Add_Action('media_upload_import_images', Array($this, 'Add_Media_Upload_Style'));
      Add_Filter('media_upload_tabs', Array($this, 'Media_Upload_Tabs'));
      Add_Filter('media_upload_form_url', Array($this, 'Media_Upload_Form_URL'));
      Add_Action('media_upload_import_images', Array($this, 'Import_Images'));
    }

  }

  function Field_Name($option_name){
    # Generates field names for the meta box
    return SPrintF('%s[%s]', $this->meta_field, $option_name);
  }

  function Save_Meta_Box($post_id, $post){
    # If this is an autosave we dont care
    If (Defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    # Check the PostType
    #If ($post->post_type != $this->name) return;

    # Check if this request came from the edit page section
    If (IsSet($_POST[$this->meta_field]) && Is_Array($_POST[$this->meta_field])){
      # Save Meta data
      Update_Post_Meta ($post_id, '_wp_plugin_fancy_gallery', $_POST[$this->meta_field]);
    }
  }

  function Get_Meta ($key = Null, $default = False, $post_id = Null){
    # Get the post id
    If ($post_id == Null && Is_Object($GLOBALS['post']))
      $post_id = $GLOBALS['post']->ID;
    ElseIf ($post_id == Null && !Is_Object($GLOBALS['post']))
      return False;

    # Read meta data
    $arr_meta = Get_Post_Meta($post_id, '_wp_plugin_fancy_gallery', True);
    $arr_meta = Is_Array($arr_meta) ? $arr_meta : Array();

    # Clean Meta data
    $arr_meta = Array_Filter($arr_meta);

    # Load default Meta data
    $arr_meta = Array_Merge($this->Default_Meta(), $arr_meta);

    # Get the key value
    If ($key == Null)
      return $arr_meta;
    ElseIf (IsSet($arr_meta[$key]) && $arr_meta[$key])
      return $arr_meta[$key];
    Else
      return $default;
  }

  function Default_Meta(){
    return Array(
      'excerpt_type' => 'images',
      'thumb_width' => Get_Option('thumbnail_size_w'),
      'thumb_height' => Get_Option('thumbnail_size_h'),
      'excerpt_image_number' => $this->core->options->Get('excerpt_image_number'),
      'excerpt_thumb_width' => $this->core->options->Get('excerpt_thumb_width'),
      'excerpt_thumb_height' => $this->core->options->Get('excerpt_thumb_height')
    );
  }

  function Register_Post_Type(){
    # Register Post Type
    Register_Post_Type ($this->name, Array(
      'labels' => Array(
        'name' => I18n::t('Galleries'),
        'singular_name' => I18n::t('Gallery'),
        'add_new' => I18n::t('Add Gallery'),
        'add_new_item' => I18n::t('New Gallery'),
        'edit_item' => I18n::t('Edit Gallery'),
        'view_item' => I18n::t('View Gallery'),
        'search_items' => I18n::t('Search Galleries'),
        'not_found' =>  I18n::t('No Galleries found'),
        'not_found_in_trash' => I18n::t('No Galleries found in Trash'),
        'parent_item_colon' => ''
        ),
      'public' => True,
      'show_ui' => True,
      'has_archive' => !$this->core->options->Get('deactivate_archive'),
			'map_meta_cap' => True,
			'hierarchical' => False,
      'rewrite' => Array(
        'slug' => I18n::t('galleries', 'URL slug'),
        'with_front' => False
      ),
      'supports' => Array('title', 'author', 'excerpt', 'thumbnail', 'comments', 'custom-fields'),
      'menu_position' => 10, # below Media
      'menu_icon' => 'dashicons-images-alt',
      'register_meta_box_cb' => Array($this, 'Add_Meta_Boxes'),
      'show_in_admin_bar' => false
    ));
  }

  function Updated_Messages($arr_message){
    return Array_Merge ($arr_message, Array($this->name => Array(
      1 => SPrintF (I18n::t('Gallery updated. <a href="%s">View Gallery</a>'), Get_Permalink()),
      2 => __('Custom field updated.'),
      3 => __('Custom field deleted.'),
      4 => I18n::t('Gallery updated.'),
      5 => IsSet($_GET['revision']) ? SPrintF(I18n::t('Gallery restored to revision from %s'), WP_Post_Revision_Title( (Int) $_GET['revision'], False ) ) : False,
      6 => SPrintF(I18n::t('Gallery published. <a href="%s">View Gallery</a>'), Get_Permalink()),
      7 => I18n::t('Gallery saved.'),
      8 => I18n::t('Gallery submitted.'),
      9 => SPrintF(I18n::t('Gallery scheduled. <a target="_blank" href="%s">View Gallery</a>'), Get_Permalink()),
      10 => SPrintF(I18n::t('Gallery draft updated. <a target="_blank" href="%s">Preview Gallery</a>'), Add_Query_Arg('preview', 'true', Get_Permalink()))
    )));
  }

  function Get_Taxonomies(){
    return Array(
      'gallery_category' => Array(
        'label' => I18n::t('Gallery Categories'),
        'labels' => Array(
          'name' => I18n::t('Categories' ),
          'singular_name' => I18n::t('Category'),
          'all_items' => I18n::t('All Categories'),
          'edit_item' => I18n::t('Edit Category'),
          'view_item' => I18n::t('View Category'),
          'update_item' => I18n::t('Update Category'),
          'add_new_item' => I18n::t('Add New Category'),
          'new_item_name' => I18n::t('New Category'),
          'parent_item' => I18n::t('Parent Category'),
          'parent_item_colon' => I18n::t('Parent Category:'),
          'search_items' =>  I18n::t('Search Categories'),
          'popular_items' => I18n::t('Popular Categories'),
          'separate_items_with_commas' => I18n::t('Separate Categories with commas'),
          'add_or_remove_items' => I18n::t('Add or remove Categories'),
          'choose_from_most_used' => I18n::t('Choose from the most used Categories'),
          'not_found' => I18n::t('No Categories found.')
        ),
        'show_admin_column' => True,
        'hierarchical' => False,
        'show_ui' => True,
        'query_var' => True,
        'rewrite' => Array(
          'with_front' => False,
          'slug' => SPrintF(I18n::t('%s/category', 'URL slug'), I18n::t('galleries', 'URL slug'))
        )
      ),

      'gallery_tag' => Array(
        'label' => I18n::t( 'Gallery Tags' ),
        'labels' => Array(
          'name' => I18n::t('Tags'),
          'singular_name' => I18n::t('Tag'),
          'all_items' => I18n::t('All Tags'),
          'edit_item' => I18n::t('Edit Tag'),
          'view_item' => I18n::t('View Tag'),
          'update_item' => I18n::t('Update Tag'),
          'add_new_item' => I18n::t('Add New Tag'),
          'new_item_name' => I18n::t('New Tag'),
          'parent_item' => I18n::t('Parent Tag'),
          'parent_item_colon' => I18n::t('Parent Tag:'),
          'search_items' =>  I18n::t('Search Tags'),
          'popular_items' => I18n::t('Popular Tags'),
          'separate_items_with_commas' => I18n::t('Separate Tags with commas'),
          'add_or_remove_items' => I18n::t('Add or remove Tags'),
          'choose_from_most_used' => I18n::t('Choose from the most used Tags'),
          'not_found' => I18n::t('No Tags found.')
        ),
        'show_admin_column' => True,
        'hierarchical' => False,
        'show_ui' => True,
        'query_var' => True,
        'rewrite' => Array(
          'with_front' => False,
          'slug' => SPrintF(I18n::t('%s/tag', 'URL slug'), I18n::t('galleries', 'URL slug'))
        )
      )
    );
  }

  function Register_Taxonomies(){
    # Load Taxonomies
    $this->arr_taxonomies = $this->Get_Taxonomies();

    # Register Taxonomies
    $arr_taxonomies = $this->core->options->Get('gallery_taxonomies');
    If (!Is_Array($arr_taxonomies)) return False;

    ForEach ($arr_taxonomies As $taxonomie => $attributes ){
      If (!IsSet($this->arr_taxonomies[$taxonomie])) Continue;
      $this->arr_taxonomies[$taxonomie] = Is_Array($this->arr_taxonomies[$taxonomie]) ? $this->arr_taxonomies[$taxonomie] : Array();
      Register_Taxonomy ($taxonomie, $this->name, Array_Merge($this->arr_taxonomies[$taxonomie], $attributes));
    }
  }

  function Add_Taxonomy_Archive_Urls(){
    ForEach(Get_Object_Taxonomies($this->name) AS $taxonomy){ /*$taxonomy = Get_Taxonomy($taxonomy)*/
      Add_Action ($taxonomy.'_edit_form_fields', Array($this, 'Print_Taxonomy_Archive_Urls'), 10, 3);
    }
  }

  function Print_Taxonomy_Archive_Urls($tag, $taxonomy){
    $taxonomy = Get_Taxonomy($taxonomy);
    $archive_url = Get_Term_Link(get_term($tag->term_id, $taxonomy->name));
    $archive_feed = Get_Term_Feed_Link($tag->term_id, $taxonomy->name);
    ?>
    <tr class="form-field">
      <th scope="row" valign="top"><?php Echo I18n::t('Archive Url') ?></th>
      <td>
        <a href="<?php Echo $archive_url ?>" target="_blank"><?php Echo $archive_url ?></a><br>
        <span class="description"><?php PrintF(I18n::t('This is the URL to the archive of this %s.'), $taxonomy->labels->singular_name) ?></span>
      </td>
    </tr>
    <tr class="form-field">
      <th scope="row" valign="top"><?php Echo I18n::t('Archive Feed') ?></th>
      <td>
        <a href="<?php Echo $archive_feed ?>" target="_blank"><?php Echo $archive_feed ?></a><br>
        <span class="description"><?php PrintF(I18n::t('This is the URL to the feed of the archive of this %s.'), $taxonomy->labels->singular_name) ?></span>
      </td>
    </tr>
    <?php
  }

  function Add_Media_Upload_Style(){
    WP_Enqueue_Style('fancy-gallery-media-upload', $this->core->base_url . '/meta-boxes/media-upload.css');
  }

  function Media_Upload_Tabs($arr_tabs){
		return Array(
      'type' => I18n::t('Upload Images'),
      'gallery' => $arr_tabs['gallery'],
      'import_images' => I18n::t('Import from Library')
    );
  }

  function Media_Upload_Form_URL($url){
    return $url . '&strip_tabs=true';
  }

  function Image_Upload_Iframe_Src($url){
    If (IsSet($GLOBALS['post']) && $GLOBALS['post']->post_type == $this->name)
      return $url . '&strip_tabs=true';
    Else
      return $url;
  }

  function Add_Meta_Box($title, $include_file, $column = 'normal', $priority = 'default'){
    If (!$title) return False;
    If (!Is_File($include_file)) return False;
    If ($column != 'side') $column = 'normal';

    # Add to array
    $this->arr_meta_box[] = Array(
      'title' => $title,
      'include_file' => $include_file,
      'column' => $column,
      'priority' => $priority
    );
  }

  function Add_Meta_Boxes(){
    Global $post_type_object;

    # Enqueue Edit Gallery JavaScript/CSS
    WP_Enqueue_Script('fancy-gallery-meta-boxes', $this->core->base_url . '/meta-boxes/meta-boxes.js', Array('jquery'), $this->core->version, True);
    WP_Enqueue_Style('fancy-gallery-meta-boxes', $this->core->base_url . '/meta-boxes/meta-boxes.css', False, $this->core->version);

    # Remove Meta Boxes
    Remove_Meta_Box('authordiv', $this->name, 'normal');
    Remove_Meta_Box('postexcerpt', $this->name, 'normal');

    # Change some core texts
    #Add_Filter ( 'gettext', Array($this, 'Filter_GetText'), 10, 3 );

    # Register Meta Boxes
    $this->Add_Meta_Box(I18n::t('Images'), Core::$plugin_folder . '/meta-boxes/images.php', 'normal', 'high');

    If (!$this->core->options->Get('disable_excerpts'))
      $this->Add_Meta_Box(I18n::t('Excerpt'), Core::$plugin_folder . '/meta-boxes/excerpt.php', 'normal', 'high');

    $this->Add_Meta_Box(I18n::t('Template'), Core::$plugin_folder . '/meta-boxes/template.php', 'normal', 'high');

    If (Current_User_Can($post_type_object->cap->edit_others_posts))
      $this->Add_Meta_Box(I18n::t('Owner'), Core::$plugin_folder . '/meta-boxes/owner.php' );

    $this->Add_Meta_Box(I18n::t('Gallery ShortCode'), Core::$plugin_folder . '/meta-boxes/show-code.php', 'side', 'high');
    $this->Add_Meta_Box(I18n::t('Thumbnails'), Core::$plugin_folder . '/meta-boxes/thumbnails.php', 'side' );

    # Add Meta Boxes
    ForEach ($this->arr_meta_box AS $box_index => $meta_box){
      Add_Meta_Box(
        'meta-box-'.BaseName($meta_box['include_file'], '.php'),
        $meta_box['title'],
        Array($this, 'Print_Gallery_Meta_Box'),
        $this->name,
        $meta_box['column'],
        $meta_box['priority'],
        $box_index
      );
    }
  }

  function Print_Gallery_Meta_Box($post, $box){
    $include_file = $this->arr_meta_box[$box['args']]['include_file'];
    If (Is_File ($include_file))
      Include $include_file;
  }

  function Import_Images(){
		# Enqueue Scripts and Styles
		WP_Enqueue_Style('media');
		WP_Enqueue_Style('fancy-gallery-import-images', $this->core->base_url.'/meta-boxes/import-images-form.css', Null, $this->core->version);
		WP_Enqueue_Script('fancy-gallery-import-images', $this->core->base_url.'/meta-boxes/import-images-form.js', Array('jquery'), $this->core->version, True);

		# Check if an attachment should be moved
		$message = '';
		If (IsSet($_REQUEST['move_attachment']) && IsSet($_REQUEST['move_to'])){
			$attachment_id = IntVal($_REQUEST['move_attachment']);
			$dst_post_id = IntVal($_REQUEST['move_to']);
			WP_Update_Post(Array(
				'ID' => $attachment_id,
				'post_parent' => $dst_post_id
			));
			$message = I18n::t('The Attachment was moved to your gallery.');
		}

		# Generate Output
		return WP_iFrame( Array($this, 'Print_Import_Images_Form'), $message );
	}

	function Print_Import_Images_Form($message = ''){
		Media_Upload_Header();
		Include Core::$plugin_folder . '/meta-boxes/import-images-form.php';
	}

  function Update_Post_Type_Name(){
    Global $wpdb;
    $wpdb->Update($wpdb->posts, Array('post_type' => $this->name), Array('post_type' => 'fancy_gallery'));
	}

}
