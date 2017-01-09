<?php Namespace WordPress\Plugin\Fancy_Gallery;

/*
Fancy Gallery Template: Thumbnails only
Description: This template displays the thumbnail images without any links.
Version: 1.0.1
Author: Dennis Hoppe
Author URI: http://DennisHoppe.de
*/

?>
<div class="gallery fancy-gallery <?php Echo BaseName(__FILE__, '.php') ?> gallery-columns-<?php Echo Core::$gallery->attributes->columns ?>" id="gallery_<?php Echo Core::$gallery->id ?>">
<?php ForEach(Core::$gallery->images AS $image): ?>
  <figure class="gallery-item">

    <div class="gallery-icon">
      <img <?php ForEach ($image->attributes AS $attribute => $value) PrintF('%s="%s" ', $attribute, Esc_Attr($value)) ?> >
    </div>

    <?php If (!Empty($image->caption)): ?>
    <figcaption class="wp-caption-text gallery-caption"><?php Echo WPTexturize($image->caption) ?></figcaption>
    <?php EndIf ?>

  </figure>
<?php EndForEach ?>
</div>