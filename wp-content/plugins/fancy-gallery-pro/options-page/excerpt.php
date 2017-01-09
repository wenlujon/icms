<p>
  <?php Echo $this->t('Usually posts have optional hand-crafted summaries of their content. But galleries do not.') ?>
  <?php Echo $this->t('Gallery excerpts are randomly chosen images from a gallery.') ?>
  <em><?php Echo $this->t('These settings may be overridden for individual galleries.') ?></em>
</p>

<p>
  <input type="checkbox" name="disable_excerpts" id="disable_excerpts" value="yes" <?php Checked ($this->Get('disable_excerpts'), 'yes') ?> >
  <label for="disable_excerpts"><?php Echo $this->t('Do not generate excerpts out of random gallery images.') ?></label>
</p>

<table>
<tr>
  <td><label for="excerpt_image_number"><?php Echo $this->t('Images per excerpt') ?></label></td>
  <td><input type="number" name="excerpt_image_number" id="excerpt_image_number" value="<?php Echo Esc_Attr($this->Get('excerpt_image_number')) ?>"></td>
  </tr>
</tr>
<tr>
  <td><label for="excerpt_thumb_width"><?php Echo $this->t('Thumbnail width') ?></label></td>
  <td><input type="number" name="excerpt_thumb_width" id="excerpt_thumb_width" value="<?php Echo Esc_Attr($this->Get('excerpt_thumb_width')) ?>"> px</td>
</tr>
<tr>
  <td><label for="excerpt_thumb_height"><?php Echo $this->t('Thumbnail height') ?></label></td>
  <td><input type="number" name="excerpt_thumb_height" id="excerpt_thumb_height" value="<?php Echo Esc_Attr($this->Get('excerpt_thumb_height')) ?>"> px</td>
</tr>
</table>