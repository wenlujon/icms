<?php Namespace WordPress\Plugin\Fancy_Gallery ?>
<p><?php Echo I18n::t('To display this gallery in posts or pages easily you can use the <em>[gallery]</em> Shortcode:') ?></p>
<p><input type="text" class="gallery-code" value="[gallery id=&quot;<?php Echo $GLOBALS['post']->ID ?>&quot;]" readonly="readonly"></p>
<p><small>(<?php Echo I18n::t('Copy this code to all places where this gallery should appear.') ?>)</small></p>
