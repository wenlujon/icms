<iframe
  id="fancy-gallery-library-window"
  src="<?php Echo Add_Query_Arg(Array('post_id' => $GLOBALS['post']->ID, 'type' => 'image', 'tab' => 'gallery', 'strip_tabs' => 'true'), Admin_url('media-upload.php')) ?>"
  name="Fancy_Gallery_Images_Frame"
  width="100%"
  height="250"
  scrolling="no"
  marginheight="0"
  marginwidth="0"
  frameborder="0"></iframe>