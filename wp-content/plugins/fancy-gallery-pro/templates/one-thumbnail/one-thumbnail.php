<?php Namespace WordPress\Plugin\Fancy_Gallery;

/*
Fancy Gallery Template: First thumbnail only
Description: This template displays the first gallery thumbnail in full size and enables the user to navigate through all images of the gallery.
Version: 1.1
Author: Dennis Hoppe
Author URI: http://DennisHoppe.de
*/

$gallery_thumb_id = False;
$gallery_thumb = False;  

If ($gallery_thumb_id = Get_Post_Thumbnail_ID(Core::$gallery->id)){
  # Try to find this image in the gallery
  ForEach (Core::$gallery->images AS $index => $image){
    If ($gallery_thumb_id == $image->ID){
      $gallery_thumb = $image;
      Unset(Core::$gallery->images[$index]);
      Break;
    }
  }
}

# The thumbnail is not an image from the gallery
If (Empty($gallery_thumb)){
  $gallery_thumb = Array_Shift(Core::$gallery->images);
}

$gallery_thumb->attributes = Array_Merge($gallery_thumb->attributes, Array(
  'src' => $gallery_thumb->href,
  'height' => Null,
  'width' => Null
));

?>
<div class="gallery fancy-gallery <?php Echo BaseName(__FILE__, '.php') ?> gallery-columns-1" id="gallery_<?php Echo Core::$gallery->id ?>">
  <figure class="gallery-item">
    <div class="gallery-icon">
      <a href="<?php Echo Esc_Url($gallery_thumb->href) ?>" title="<?php Echo Esc_Attr($gallery_thumb->title) ?>" class="<?php Echo Core::$gallery->attributes->link_class ?>" data-caption="<?php Echo Esc_Attr($gallery_thumb->caption) ?>" data-description="<?php Echo Esc_Attr($gallery_thumb->description) ?>">
         <img <?php ForEach ($gallery_thumb->attributes AS $attribute => $value) PrintF('%s="%s" ', $attribute, Esc_Attr($value)) ?> >
      </a>
    </div>
    <?php If (!Empty($gallery_thumb->caption)): ?>
    <figcaption class="wp-caption-text gallery-caption"><?php Echo WPTexturize($gallery_thumb->caption) ?></figcaption>
    <?php EndIf ?>
  </figure>

  <?php ForEach(Core::$gallery->images AS $image) : ?>
    <a href="<?php Echo Esc_Url($image->href) ?>" title="<?php Echo Esc_Attr($image->title) ?>" class="hidden <?php Echo Esc_Attr(Core::$gallery->attributes->link_class) ?>" data-caption="<?php Echo Esc_Attr($image->caption) ?>" data-description="<?php Echo Esc_Attr($image->description) ?>">
      <img <?php ForEach ($image->attributes AS $attribute => $value) PrintF('%s="%s" ', $attribute, Esc_Attr($value)) ?> >
    </a>
  <?php EndForEach ?>
</div>