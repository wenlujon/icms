<?php Namespace WordPress\Plugin\Fancy_Gallery ?>

<table class="form-table">

<tr valign="top">
  <th scope="row"><label><?php Echo I18n::t('Lightbox') ?></label></th>
  <td>
    <select <?php Disabled(True) ?>>
      <option <?php Selected(True) ?> ><?php Echo I18n::t('On') ?></option>
      <option <?php Disabled(True) ?>><?php Echo I18n::t('Off') ?></option>
    </select><?php $this->core->mocking_bird->Pro_Notice('unlock') ?>
    <p class="help"><?php Echo I18n::t('Turn this off if you do not want to use the included lightbox.') ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label><?php Echo I18n::t('Loop mode') ?></label></th>
  <td>
    <select <?php Disabled(True) ?>>
      <option <?php Disabled(True) ?>><?php Echo I18n::t('On') ?></option>
      <option <?php Selected(True) ?> ><?php Echo I18n::t('Off') ?></option>
    </select><?php $this->core->mocking_bird->Pro_Notice('unlock') ?>
    <p class="help"><?php Echo I18n::t('Enables the user to get from the last image to the first one with the "Next &raquo;" button.') ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label><?php Echo I18n::t('Title &amp; Description') ?></label></th>
  <td>
    <select <?php Disabled(True) ?>>
      <option <?php Selected(True) ?> ><?php Echo I18n::t('On') ?></option>
      <option <?php Disabled(True) ?> ><?php Echo I18n::t('Off') ?></option>
    </select><?php $this->core->mocking_bird->Pro_Notice('unlock') ?>
    <p class="help"><?php Echo I18n::t('Turn this off if you do not want to display the image title and description in your lightbox.') ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label><?php Echo I18n::t('Close button') ?></label></th>
  <td>
    <select <?php Disabled(True) ?>>
      <option <?php Selected(True) ?> ><?php Echo I18n::t('On') ?></option>
      <option <?php Disabled(True) ?> ><?php Echo I18n::t('Off') ?></option>
    </select><?php $this->core->mocking_bird->Pro_Notice('unlock') ?>
    <p class="help"><?php Echo I18n::t('Turn this off if you do not want to display a close button in your lightbox.') ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label><?php Echo I18n::t('Indicator thumbnails') ?></label></th>
  <td>
    <select <?php Disabled(True) ?>>
      <option <?php Selected(True) ?> ><?php Echo I18n::t('On') ?></option>
      <option <?php Disabled(True) ?> ><?php Echo I18n::t('Off') ?></option>
    </select><?php $this->core->mocking_bird->Pro_Notice('unlock') ?>
    <p class="help"><?php Echo I18n::t('Turn this off if you do not want to display small preview thumbnails below the lightbox image.') ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="slideshow_speed"><?php Echo I18n::t('Slideshow speed') ?></label></th>
  <td>
    <input type="number" name="slideshow_speed" id="slideshow_speed" value="<?php Echo IntVal($this->Get('slideshow_speed')) ?>">
    <?php Echo I18n::t('ms', 'Abbr. Milliseconds') ?>
    <p class="help"><?php Echo I18n::t('The delay between two images in the slideshow.') ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label><?php Echo I18n::t('Preload images') ?></label></th>
  <td>
    <input type="number" value="<?php Echo IntVal($this->Get('preload_images')) ?>" <?php Disabled(True) ?>><?php $this->core->mocking_bird->Pro_Notice('unlock') ?>
    <p class="help"><?php Echo I18n::t('The number of images which should be preloaded around the current one.') ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="animation_speed"><?php Echo I18n::t('Animation speed') ?></label></th>
  <td>
    <input type="number" name="animation_speed" id="animation_speed" value="<?php Echo IntVal($this->Get('animation_speed')) ?>">
    <?php Echo I18n::t('ms', 'Abbr. Milliseconds') ?>
    <p class="help"><?php Echo I18n::t('The speed of the image change animation.') ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="stretch_images"><?php Echo I18n::t('Stretch images') ?></label></th>
  <td>
    <select name="stretch_images" id="stretch_images">
      <option value="" <?php Selected ($this->Get('stretch_images'), '') ?> ><?php Echo I18n::t('No stretching') ?></option>
      <option value="contain" <?php Selected ($this->Get('stretch_images'), 'contain') ?> ><?php Echo I18n::t('Contain') ?></option>
      <option value="cover" <?php Selected ($this->Get('stretch_images'), 'cover') ?> ><?php Echo I18n::t('Cover') ?></option>
    </select>
    <p class="help"><?php Echo I18n::t('"Contain" means to scale the image to the largest size such that both its width and its height can fit the screen.') ?></p>
    <p class="help"><?php Echo I18n::t('"Cover" means to scale the image to be as large as possible so that the screen is completely covered by the image. Some parts of the image may be cropped and not in view.') ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="script_position"><?php Echo I18n::t('Script position') ?></label></th>
  <td>
    <select name="script_position" id="script_position">
      <option value="footer" <?php Selected ($this->Get('script_position'), 'footer') ?> ><?php Echo I18n::t('Footer of the website') ?></option>
      <option value="header" <?php Selected ($this->Get('script_position'), 'header') ?> ><?php Echo I18n::t('Header of the website') ?></option>
    </select>
    <p class="help"><?php Echo I18n::t('Please choose the position of the javascript. Footer is recommended. Use "Header" if you have trouble to make the lightbox work.') ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="asynchronous_loading"><?php Echo I18n::t('Asynchronous loading') ?></label></th>
  <td>
    <select name="asynchronous_loading" id="asynchronous_loading">
      <option value="all" <?php Selected ($this->Get('asynchronous_loading'), 'all') ?> ><?php Echo I18n::t('Load all components asynchronously') ?></option>
      <option value="none" <?php Selected ($this->Get('asynchronous_loading'), 'none') ?> ><?php Echo I18n::t('Do not load anything asynchronously') ?></option>
      <option value="css" <?php Selected ($this->Get('asynchronous_loading'), 'css') ?> ><?php Echo I18n::t('Load CSS asynchronously only') ?></option>
      <option value="js" <?php Selected ($this->Get('asynchronous_loading'), 'js') ?> ><?php Echo I18n::t('Load JavaScripts asynchronously only') ?></option>
    </select>
    <p class="help"><?php Echo I18n::t('If you are using a HTML/CSS/JS minifier you should disable the asynchronous loading.') ?></p>
  </td>
</tr>

</table>