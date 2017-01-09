<?php Namespace WordPress\Plugin\Fancy_Gallery;

/*
Fancy Gallery Template: Full Size Images
Description: This template displays the full size images.
Version: 1.0
Author: Dennis Hoppe
Author URI: http://DennisHoppe.de
*/

?>
<div class="gallery fancy-gallery <?php Echo BaseName(__FILE__, '.php') ?> gallery-columns-<?php Echo Core::$gallery->attributes->columns ?>" id="gallery_<?php Echo Core::$gallery->id ?>">
  <?php ForEach(Core::$gallery->images AS $image): ?>
  <img src="<?php Echo Esc_Url($image->href) ?>" title="<?php Echo Esc_Attr($image->title) ?>" class="aligncenter <?php Echo Esc_Attr(Core::$gallery->attributes->link_class) ?>"  data-caption="<?php Echo Esc_Attr($image->caption) ?>" data-description="<?php Echo Esc_Attr($image->description) ?>">
  <?php EndForEach ?>
</div>