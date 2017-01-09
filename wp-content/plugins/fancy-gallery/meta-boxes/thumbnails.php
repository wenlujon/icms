<?php Namespace WordPress\Plugin\Fancy_Gallery ?>

<p>
  <?php Echo I18n::t('These settings affect the appearance of the thumbnail images of this gallery.') ?>
</p>

<table>
<tr>
  <td><label for="<?php Echo $this->Field_Name('thumb_width') ?>"><?php Echo I18n::t('Thumbnail width:') ?></label></td>
  <td nowrap="nowrap"><input type="number" name="<?php Echo $this->Field_Name('thumb_width') ?>" id="<?php Echo $this->Field_Name('thumb_width') ?>" value="<?php Echo IntVal($this->Get_Meta('thumb_width')) ?>" class="short" <?php Disabled(True) ?> >px<?php $this->core->mocking_bird->Pro_Notice('unlock') ?></td>
</tr>

<tr>
  <td><label for="<?php Echo $this->Field_Name('thumb_height') ?>"><?php Echo I18n::t('Thumbnail height:') ?></label></td>
  <td nowrap="nowrap"><input type="number" name="<?php Echo $this->Field_Name('thumb_height') ?>" id="<?php Echo $this->Field_Name('thumb_height') ?>" value="<?php Echo Esc_Attr($this->Get_Meta('thumb_height')) ?>" class="short" <?php Disabled(True) ?> >px<?php $this->core->mocking_bird->Pro_Notice('unlock') ?></td>
</tr>
</table>

<p>
  <input type="checkbox" name="<?php Echo $this->Field_Name('thumb_grayscale') ?>" id="<?php Echo $this->Field_Name('thumb_grayscale') ?>" value="yes" <?php Checked($this->Get_Meta('thumb_grayscale'), 'yes'); Disabled(True) ?> >
  <label for="<?php Echo $this->Field_Name('thumb_grayscale') ?>"><?php Echo I18n::t('Convert thumbnails to grayscale.') ?></label><?php $this->core->mocking_bird->Pro_Notice('unlock') ?>
</p>

<p>
  <input type="checkbox" name="<?php Echo $this->Field_Name('thumb_negate') ?>" id="<?php Echo $this->Field_Name('thumb_negate') ?>" value="yes" <?php Checked($this->Get_Meta('thumb_negate'), 'yes'); Disabled(True) ?> >
  <label for="<?php Echo $this->Field_Name('thumb_negate') ?>"><?php Echo I18n::t('Negate the thumbnails.') ?></label><?php $this->core->mocking_bird->Pro_Notice('unlock') ?>
</p>