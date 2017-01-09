<?php Namespace WordPress\Plugin\Fancy_Gallery;

class Gallery_Post_Type {
  var
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
      Add_Filter('media_upload_tabs', Array($this, 'Media_Upload_Tabs'));
      Add_Filter('media_upload_form_url', Array($this, 'Media_Upload_Form_URL'));
      Add_Action('media_upload_import_images', Array($this, 'Import_Images'));
    }

  }

  function t($text, $context = False){
    return $this->core->t($text, $context);
  }

  function Field_Name($option_name){
    # Generates field names for the meta box
    return SPrintF('%s[%s]', $this->meta_field, $option_name);
  }

  function Save_Meta_Box($post_id, $post){
    # If this is an autosave we dont care
    If ( Defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;

    # Check the PostType
    #If ($post->post_type != $this->name) return;

    # Check if this request came from the edit page section
    If (IsSet($_POST[$this->meta_field]) && Is_Array($_POST[$this->meta_field])){
      # Save Meta data
      Update_Post_Meta ($post_id, '_wp_plugin_fancy_gallery', $_POST[$this->meta_field]);
      Delete_Post_Meta ($post_id, '_wp_plugin_fancy_gallery_pro');
    }

  }

  function Get_Meta ($key = Null, $default = False, $post_id = Null){
    # Get the post id
    If ($post_id == Null && Is_Object($GLOBALS['post']))
      $post_id = $GLOBALS['post']->ID;
    ElseIf ($post_id == Null && !Is_Object($GLOBALS['post']))
      return False;

    # Read meta data
    $arr_meta = Array_Merge(
      (Array) Get_Post_Meta($post_id, '_wp_plugin_fancy_gallery_pro', True),
      (Array) Get_Post_Meta($post_id, '_wp_plugin_fancy_gallery', True)
    );
    If (Empty($arr_meta) || !Is_Array($arr_meta)) $arr_meta = Array();

    # Clean Meta data
    ForEach ($arr_meta AS $k => $v)
      If (!$v) Unset ($arr_meta[$k]);

    # Load default Meta data
    $arr_meta = Array_Merge ( $this->Default_Meta(), $arr_meta );

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
        'name' => $this->t('Galleries'),
        'singular_name' => $this->t('Gallery'),
        'add_new' => $this->t('Add Gallery'),
        'add_new_item' => $this->t('New Gallery'),
        'edit_item' => $this->t('Edit Gallery'),
        'view_item' => $this->t('View Gallery'),
        'search_items' => $this->t('Search Galleries'),
        'not_found' =>  $this->t('No Galleries found'),
        'not_found_in_trash' => $this->t('No Galleries found in Trash'),
        'parent_item_colon' => ''
        ),
      'public' => True,
      'show_ui' => True,
      'has_archive' => !$this->core->options->Get('deactivate_archive'),
      'capability_type' => Array('gallery', 'galleries'),
			'map_meta_cap' => True,
			'hierarchical' => False,
      'rewrite' => Array(
        'slug' => $this->t('galleries', 'URL slug'),
        'with_front' => False
      ),
      'supports' => Array('title', 'author', 'excerpt', 'thumbnail', 'comments', 'custom-fields'),
      'menu_position' => 10, # below Media
      'menu_icon' => 'dashicons-images-alt',
      'register_meta_box_cb' => Array($this, 'Add_Meta_Boxes')
    ));
  }

  function Updated_Messages($arr_message){
    return Array_Merge ($arr_message, Array($this->name => Array(
      1 => SPrintF ($this->t('Gallery updated. (<a href="%s">View Gallery</a>)'), Get_Permalink()),
      2 => __('Custom field updated.'),
      3 => __('Custom field deleted.'),
      4 => $this->t('Gallery updated.'),
      5 => IsSet($_GET['revision']) ? SPrintF($this->t('Gallery restored to revision from %s'), WP_Post_Revision_Title( (Int) $_GET['revision'], False ) ) : False,
      6 => SPrintF($this->t('Gallery published. (<a href="%s">View Gallery</a>)'), Get_Permalink()),
      7 => $this->t('Gallery saved.'),
      8 => $this->t('Gallery submitted.'),
      9 => SPrintF($this->t('Gallery scheduled. (<a target="_blank" href="%s">View Gallery</a>)'), Get_Permalink()),
      10 => SPrintF($this->t('Gallery draft updated. (<a target="_blank" href="%s">Preview Gallery</a>)'), Add_Query_Arg('preview', 'true', Get_Permalink()))
    )));
  }

  function Get_Taxonomies(){
    return Array(
      'gallery_category' => Array(
        'label' => $this->t('Gallery Categories'),
        'labels' => Array(
          'name' => $this->t('Categories' ),
          'singular_name' => $this->t('Category'),
          'all_items' => $this->t('All Categories'),
          'edit_item' => $this->t('Edit Category'),
          'view_item' => $this->t('View Category'),
          'update_item' => $this->t('Update Category'),
          'add_new_item' => $this->t('Add New Category'),
          'new_item_name' => $this->t('New Category'),
          'parent_item' => $this->t('Parent Category'),
          'parent_item_colon' => $this->t('Parent Category:'),
          'search_items' =>  $this->t('Search Categories'),
          'popular_items' => $this->t('Popular Categories'),
          'separate_items_with_commas' => $this->t('Separate Categories with commas'),
          'add_or_remove_items' => $this->t('Add or remove Categories'),
          'choose_from_most_used' => $this->t('Choose from the most used Categories'),
          'not_found' => $this->t('No Categories found.')
        ),
        'show_admin_column' => True,
        'hierarchical' => False,
        'show_ui' => True,
        'query_var' => True,
        'rewrite' => Array(
          'with_front' => False,
          'slug' => SPrintF($this->t('%s/category', 'URL slug'), $this->t('galleries', 'URL slug'))
        ),
        'capabilities' => Array (
          'manage_terms' => 'manage_gallery_categories',
          'edit_terms' => 'manage_gallery_categories',
          'delete_terms' => 'manage_gallery_categories',
          'assign_terms' => 'edit_galleries'
        )
      ),

      'gallery_tag' => Array(
        'label' => $this->t( 'Gallery Tags' ),
        'labels' => Array(
          'name' => $this->t('Tags'),
          'singular_name' => $this->t('Tag'),
          'all_items' => $this->t('All Tags'),
          'edit_item' => $this->t('Edit Tag'),
          'view_item' => $this->t('View Tag'),
          'update_item' => $this->t('Update Tag'),
          'add_new_item' => $this->t('Add New Tag'),
          'new_item_name' => $this->t('New Tag'),
          'parent_item' => $this->t('Parent Tag'),
          'parent_item_colon' => $this->t('Parent Tag:'),
          'search_items' =>  $this->t('Search Tags'),
          'popular_items' => $this->t('Popular Tags'),
          'separate_items_with_commas' => $this->t('Separate Tags with commas'),
          'add_or_remove_items' => $this->t('Add or remove Tags'),
          'choose_from_most_used' => $this->t('Choose from the most used Tags'),
          'not_found' => $this->t('No Tags found.')
        ),
        'show_admin_column' => True,
        'hierarchical' => False,
        'show_ui' => True,
        'query_var' => True,
        'rewrite' => Array(
          'with_front' => False,
          'slug' => SPrintF($this->t('%s/tag', 'URL slug'), $this->t('galleries', 'URL slug'))
        ),
        'capabilities' => Array (
          'manage_terms' => 'manage_gallery_tags',
          'edit_terms' => 'manage_gallery_tags',
          'delete_terms' => 'manage_gallery_tags',
          'assign_terms' => 'edit_galleries'
        )
      ),

      'gallery_event' => Array(
        'label' => $this->t( 'Gallery Events' ),
        'labels' => Array(
          'name' => $this->t('Events'),
          'singular_name' => $this->t('Event'),
          'all_items' => $this->t('All Events'),
          'edit_item' => $this->t('Edit Event'),
          'view_item' => $this->t('View Event'),
          'update_item' => $this->t('Update Event'),
          'add_new_item' => $this->t('Add New Event'),
          'new_item_name' => $this->t('New Event'),
          'parent_item' => $this->t('Parent Event'),
          'parent_item_colon' => $this->t('Parent Event:'),
          'search_items' =>  $this->t('Search Events'),
          'popular_items' => $this->t('Popular Events'),
          'separate_items_with_commas' => $this->t('Separate Events with commas'),
          'add_or_remove_items' => $this->t('Add or remove Events'),
          'choose_from_most_used' => $this->t('Choose from the most used Events'),
          'not_found' => $this->t('No Events found.')
        ),
        'show_admin_column' => True,
        'hierarchical' => False,
        'show_ui' => True,
        'query_var' => True,
        'rewrite' => Array(
          'with_front' => False,
          'slug' => SPrintF($this->t('%s/event', 'URL slug'), $this->t('galleries', 'URL slug'))
        ),
        'capabilities' => Array (
          'manage_terms' => 'manage_gallery_events',
          'edit_terms' => 'manage_gallery_events',
          'delete_terms' => 'manage_gallery_events',
          'assign_terms' => 'edit_galleries'
        )
      ),

      'gallery_place' => Array(
        'label' => $this->t( 'Gallery Places' ),
        'labels' => Array(
          'name' => $this->t('Places'),
          'singular_name' => $this->t('Place'),
          'all_items' => $this->t('All Places'),
          'edit_item' => $this->t('Edit Place'),
          'view_item' => $this->t('View Place'),
          'update_item' => $this->t('Update Place'),
          'add_new_item' => $this->t('Add New Place'),
          'new_item_name' => $this->t('New Place'),
          'parent_item' => $this->t('Parent Place'),
          'parent_item_colon' => $this->t('Parent Place:'),
          'search_items' =>  $this->t('Search Places'),
          'popular_items' => $this->t('Popular Places'),
          'separate_items_with_commas' => $this->t('Separate Places with commas'),
          'add_or_remove_items' => $this->t('Add or remove Places'),
          'choose_from_most_used' => $this->t('Choose from the most used Places'),
          'not_found' => $this->t('No Places found.')
        ),
        'show_admin_column' => True,
        'hierarchical' => False,
        'show_ui' => True,
        'query_var' => True,
        'rewrite' => Array(
          'with_front' => False,
          'slug' => SPrintF($this->t('%s/place', 'URL slug'), $this->t('galleries', 'URL slug'))
        ),
        'capabilities' => Array (
          'manage_terms' => 'manage_gallery_places',
          'edit_terms' => 'manage_gallery_places',
          'delete_terms' => 'manage_gallery_places',
          'assign_terms' => 'edit_galleries'
        )
      ),

      'gallery_date' => Array(
        'label' => $this->t('Gallery Dates'),
        'labels' => Array(
          'name' => $this->t('Dates'),
          'singular_name' => $this->t('Date'),
          'all_items' => $this->t('All Dates'),
          'edit_item' => $this->t('Edit Date'),
          'view_item' => $this->t('View Date'),
          'update_item' => $this->t('Update Date'),
          'add_new_item' => $this->t('Add New Date'),
          'new_item_name' => $this->t('New Date'),
          'parent_item' => $this->t('Parent Date'),
          'parent_item_colon' => $this->t('Parent Date:'),
          'search_items' =>  $this->t('Search Dates'),
          'popular_items' => $this->t('Popular Dates'),
          'separate_items_with_commas' => $this->t('Separate Dates with commas'),
          'add_or_remove_items' => $this->t('Add or remove Dates'),
          'choose_from_most_used' => $this->t('Choose from the most used Dates'),
          'not_found' => $this->t('No Dates found.')
        ),
        'show_admin_column' => True,
        'hierarchical' => False,
        'show_ui' => True,
        'query_var' => True,
        'rewrite' => Array(
          'with_front' => False,
          'slug' => SPrintF($this->t('%s/date', 'URL slug'), $this->t('galleries', 'URL slug'))
        ),
        'capabilities' => Array (
          'manage_terms' => 'manage_gallery_dates',
          'edit_terms' => 'manage_gallery_dates',
          'delete_terms' => 'manage_gallery_dates',
          'assign_terms' => 'edit_galleries'
        )
      ),

      'gallery_person' => Array(
        'label' => $this->t('Gallery Persons'),
        'labels' => Array(
          'name' => $this->t('Persons'),
          'singular_name' => $this->t('Person'),
          'all_items' => $this->t('All Persons'),
          'edit_item' => $this->t('Edit Person'),
          'view_item' => $this->t('View Person'),
          'update_item' => $this->t('Update Person'),
          'add_new_item' => $this->t('Add New Person'),
          'new_item_name' => $this->t('New Person'),
          'parent_item' => $this->t('Parent Person'),
          'parent_item_colon' => $this->t('Parent Person:'),
          'search_items' =>  $this->t('Search Persons'),
          'popular_items' => $this->t('Popular Persons'),
          'separate_items_with_commas' => $this->t('Separate Persons with commas'),
          'add_or_remove_items' => $this->t('Add or remove Persons'),
          'choose_from_most_used' => $this->t('Choose from the most used Persons'),
          'not_found' => $this->t('No Persons found.')
        ),
        'show_admin_column' => True,
        'hierarchical' => False,
        'show_ui' => True,
        'query_var' => True,
        'rewrite' => Array(
          'with_front' => False,
          'slug' => SPrintF($this->t('%s/person', 'URL slug'), $this->t('galleries', 'URL slug'))
        ),
        'capabilities' => Array (
          'manage_terms' => 'manage_gallery_persons',
          'edit_terms' => 'manage_gallery_persons',
          'delete_terms' => 'manage_gallery_persons',
          'assign_terms' => 'edit_galleries'
        )
      ),

      'gallery_photographer' => Array(
        'label' => $this->t('Gallery Photographers'),
        'labels' => Array(
          'name' => $this->t('Photographers'),
          'singular_name' => $this->t('Photographer'),
          'all_items' => $this->t('All Photographers'),
          'edit_item' => $this->t('Edit Photographer'),
          'view_item' => $this->t('View Photographer'),
          'update_item' => $this->t('Update Photographer'),
          'add_new_item' => $this->t('Add New Photographer'),
          'new_item_name' => $this->t('New Photographer'),
          'parent_item' => $this->t('Parent Photographer'),
          'parent_item_colon' => $this->t('Parent Photographer:'),
          'search_items' =>  $this->t('Search Photographers'),
          'popular_items' => $this->t('Popular Photographers'),
          'separate_items_with_commas' => $this->t('Separate Photographers with commas'),
          'add_or_remove_items' => $this->t('Add or remove Photographers'),
          'choose_from_most_used' => $this->t('Choose from the most used Photographers'),
          'not_found' => $this->t('No Photographers found.')
        ),
        'show_admin_column' => True,
        'hierarchical' => False,
        'show_ui' => True,
        'query_var' => True,
        'rewrite' => Array(
          'with_front' => False,
          'slug' => SPrintF($this->t('%s/photographer', 'URL slug'), $this->t('galleries', 'URL slug'))
        ),
        'capabilities' => Array (
          'manage_terms' => 'manage_gallery_photographers',
          'edit_terms' => 'manage_gallery_photographers',
          'delete_terms' => 'manage_gallery_photographers',
          'assign_terms' => 'edit_galleries'
        )
      )
    );
  }

  function Register_Taxonomies(){
    # Load Taxonomies
    $this->arr_taxonomies = $this->Get_Taxonomies();

    # Register Taxonomies
    ForEach ( (Array) $this->core->options->Get('gallery_taxonomies') As $taxonomie => $attributes ){
      If (!IsSet($this->arr_taxonomies[$taxonomie])) Continue;
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
      <th scope="row" valign="top"><?php Echo $this->t('Archive Url') ?></th>
      <td>
        <a href="<?php Echo $archive_url ?>" target="_blank"><?php Echo $archive_url ?></a><br>
        <span class="description"><?php PrintF($this->t('This is the URL to the archive of this %s.'), $taxonomy->labels->singular_name) ?></span>
      </td>
    </tr>
    <tr class="form-field">
      <th scope="row" valign="top"><?php Echo $this->t('Archive Feed') ?></th>
      <td>
        <a href="<?php Echo $archive_feed ?>" target="_blank"><?php Echo $archive_feed ?></a><br />
        <span class="description"><?php PrintF($this->t('This is the URL to the feed of the archive of this %s.'), $taxonomy->labels->singular_name) ?></span>
      </td>
    </tr>
    <?php
  }

  function Add_Media_Upload_Style(){
    WP_Enqueue_Style('fancy-gallery-media-upload', Core::$base_url . '/meta-boxes/media-upload.css');
  }

  function Media_Upload_Tabs($arr_tabs){
		return Array(
      'type' => $this->t('Upload Images'),
      'gallery' => $arr_tabs['gallery'],
      'import_images' => $this->t('Import from Library')
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
    WP_Enqueue_Script('fancy-gallery-meta-boxes', Core::$base_url . '/meta-boxes/meta-boxes.js', Array('jquery'), Core::$version, True);
    WP_Enqueue_Style('fancy-gallery-meta-boxes', Core::$base_url . '/meta-boxes/meta-boxes.css', False, Core::$version);

    # Remove Meta Boxes
    Remove_Meta_Box('authordiv', $this->name, 'normal');
    Remove_Meta_Box('postexcerpt', $this->name, 'normal');

    # Change some core texts
    #Add_Filter ( 'gettext', Array($this, 'Filter_GetText'), 10, 3 );

    # Register Meta Boxes
    $this->Add_Meta_Box( $this->t('Images'), DirName(__FILE__) . '/meta-boxes/images.php', 'normal', 'high' );

    If (!$this->core->options->Get('disable_excerpts'))
      $this->Add_Meta_Box( $this->t('Excerpt'), DirName(__FILE__) . '/meta-boxes/excerpt.php', 'normal', 'high' );

    $this->Add_Meta_Box( $this->t('Template'), DirName(__FILE__) . '/meta-boxes/template.php', 'normal', 'high' );

    If (Current_User_Can($post_type_object->cap->edit_others_posts))
      $this->Add_Meta_Box( $this->t('Owner'), DirName(__FILE__) . '/meta-boxes/owner.php' );

    $this->Add_Meta_Box( $this->t('Gallery ShortCode'), DirName(__FILE__) . '/meta-boxes/show-code.php', 'side', 'high' );
    $this->Add_Meta_Box( $this->t('Thumbnails'), DirName(__FILE__) . '/meta-boxes/thumbnails.php', 'side' );

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
		WP_Enqueue_Style('fancy-gallery-import-images', Core::$base_url.'/meta-boxes/import-images-form.css', Null, Core::$version);
		WP_Enqueue_Script('fancy-gallery-import-images', Core::$base_url.'/meta-boxes/import-images-form.js', Array('jquery'), Core::$version, True);

		# Check if an attachment should be moved
		$message = '';
		If (IsSet($_REQUEST['move_attachment']) && IsSet($_REQUEST['move_to'])){
			$attachment_id = IntVal($_REQUEST['move_attachment']);
			$dst_post_id = IntVal($_REQUEST['move_to']);
			WP_Update_Post(Array(
				'ID' => $attachment_id,
				'post_parent' => $dst_post_id
			));
			$message = $this->t('The Attachment was moved to your gallery.');
		}

		# Generate Output
		return WP_iFrame( Array($this, 'Print_Import_Images_Form'), $message );
	}

	function Print_Import_Images_Form($message = ''){
		Media_Upload_Header();
		Include DirName(__FILE__) . '/meta-boxes/import-images-form.php';
	}

  function Update_Post_Type_Name(){
    Global $wpdb;
    $wpdb->Update($wpdb->posts, Array('post_type' => $this->name), Array('post_type' => 'fancy_gallery'));
	}

}