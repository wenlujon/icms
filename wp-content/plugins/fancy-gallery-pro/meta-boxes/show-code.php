<p><?php Echo $this->t('To display this gallery in posts or pages easily you can use the <em>[gallery]</em> Shortcode:') ?></p>
<p><input type="text" class="gallery-code" value="[gallery id=&quot;<?php Echo $GLOBALS['post']->ID ?>&quot;]" readonly="readonly"></p>
<p><small>(<?php Echo $this->t('Copy this code to all places where this gallery should appear.') ?>)</small></p>

<p><?php Echo $this->t('Or you could link to this hash:') ?></p>
<p><input type="text" class="gallery-code" value="#gallery-<?php Echo $GLOBALS['post']->ID ?>" readonly="readonly"></p>
<p><small>(<?php Echo $this->t('Just use this hash as link target.') ?>)</small></p>
