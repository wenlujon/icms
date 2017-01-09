<?php
$dir = __DIR__ . '/../uploads';

$uri = get_bloginfo('siteurl') .'/wp-content/uploads';

define( 'BP_AVATAR_UPLOAD_PATH', $dir );

define( 'BP_AVATAR_URL', $uri );
?>
