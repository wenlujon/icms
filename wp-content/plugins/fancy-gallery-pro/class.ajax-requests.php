<?php Namespace WordPress\Plugin\Fancy_Gallery;

abstract class AJAX_Requests {
  
  static function Init(){
    Add_Action('wp_ajax_get_gallery', Array(__CLASS__, 'getGallery'));
    Add_Action('wp_ajax_nopriv_get_gallery', Array(__CLASS__, 'getGallery'));
  }

  static function getGallery(){
    $gallery_id = Trim($_REQUEST['gallery_id']);

    # Generate WP_Query attributes
    $attributes = Core::Generate_Gallery_Attributes(Array(
      'id' => $gallery_id
    ));

  	# Get Images
    $arr_gallery = Get_Posts($attributes);

  	# There are no attachments
  	If (Empty($arr_gallery)) return False;

  	# Build the Gallery object
  	Core::Build_Gallery($arr_gallery, $attributes);
    
    # Clean up the image details
    ForEach (Core::$gallery->images As &$image){
      $image = (Object) Array(
        'title' => IsSet($image->title) ? $image->title : False,
        'description' => IsSet($image->description) ? $image->description : False,
        'href' => IsSet($image->href) ? $image->href : False,
        'thumbnail' => IsSet($image->attributes['src']) ? $image->attributes['src'] : False
      );
    }
    
    # return the images
    Header('Content-Type: application/json');
    Echo JSON_Encode(Core::$gallery->images);
    Exit;
  }
  
}

AJAX_Requests::Init();