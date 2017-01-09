<?php Namespace WordPress\Plugin\Fancy_Gallery ?>

<p>
  <?php Echo I18n::t('Usually posts have optional hand-crafted summaries of their content. But galleries do not.') ?>
  <?php Echo I18n::t('Gallery excerpts are randomly chosen images from a gallery.') ?>
  <i><?php Echo I18n::t('These settings may be overridden for individual galleries.') ?></i>
</p>

<p>
  <input type="checkbox" name="disable_excerpts" id="disable_excerpts" value="1" <?php Checked($this->Get('disable_excerpts')) ?> >
  <label for="disable_excerpts"><?php Echo I18n::t('Do not generate excerpts out of random gallery images.') ?></label>
</p>

<table>
<tr>
  <td><label><?php Echo I18n::t('Images per excerpt') ?></label></td>
  <td nowrap="nowrap">
    <input type="number" value="<?php Echo Esc_Attr($this->Get('excerpt_image_number')) ?>" <?php Disabled(True) ?>><?php $this->core->mocking_bird->Pro_Notice('unlock') ?>
  </td>
</tr>

<tr>
  <td><label><?php Echo I18n::t('Thumbnail width') ?></label></td>
  <td nowrap="nowrap">
    <input type="number" value="<?php Echo Esc_Attr($this->Get('excerpt_thumb_width')) ?>" <?php Disabled(True) ?>>px<?php $this->core->mocking_bird->Pro_Notice('unlock') ?>
  </td>
</tr>

<tr>
  <td><label><?php Echo I18n::t('Thumbnail height') ?></label></td>
  <td nowrap="nowrap">
    <input type="number" value="<?php Echo Esc_Attr($this->Get('excerpt_thumb_height')) ?>" <?php Disabled(True) ?>>px<?php $this->core->mocking_bird->Pro_Notice('unlock') ?>
  </td>
</tr>
</table>