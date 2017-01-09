<table class="form-table">

<tr valign="top">
  <th scope="row"><label for="lightbox"><?php Echo $this->t('Lightbox') ?></label></th>
  <td>
    <select name="lightbox" id="lightbox">
      <option value="on" <?php Selected($this->Get('lightbox'), 'on') ?> ><?php Echo $this->t('On') ?></option>
      <option value="off" <?php Selected($this->Get('lightbox'), 'off') ?> ><?php Echo $this->t('Off') ?></option>
    </select>
    <p class="help"><?php Echo $this->t('Turn this off if you do not want to use the included lightbox.') ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="continuous"><?php Echo $this->t('Loop mode') ?></label></th>
  <td>
    <select name="continuous" id="continuous">
      <option value="on" <?php Selected($this->Get('continuous'), 'on') ?> ><?php Echo $this->t('On') ?></option>
      <option value="off" <?php Selected($this->Get('continuous'), 'off') ?> ><?php Echo $this->t('Off') ?></option>
    </select>
    <p class="help"><?php Echo $this->t('Enables the user to get from the last image to the first one with the "Next &raquo;" button.') ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="title_description"><?php Echo $this->t('Title &amp; Description') ?></label></th>
  <td>
    <select name="title_description" id="title_description">
      <option value="on" <?php Selected($this->Get('title_description'), 'on') ?> ><?php Echo $this->t('On') ?></option>
      <option value="off" <?php Selected($this->Get('title_description'), 'off') ?> ><?php Echo $this->t('Off') ?></option>
    </select>
    <p class="help"><?php Echo $this->t('Turn this off if you do not want to display the image title and description in your lightbox.') ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="close_button"><?php Echo $this->t('Close button') ?></label></th>
  <td>
    <select name="close_button" id="close_button">
      <option value="on" <?php Selected($this->Get('close_button'), 'on') ?> ><?php Echo $this->t('On') ?></option>
      <option value="off" <?php Selected($this->Get('close_button'), 'off') ?> ><?php Echo $this->t('Off') ?></option>
    </select>
    <p class="help"><?php Echo $this->t('Turn this off if you do not want to display a close button in your lightbox.') ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="indicator_thumbnails"><?php Echo $this->t('Indicator thumbnails') ?></label></th>
  <td>
    <select name="indicator_thumbnails" id="indicator_thumbnails">
      <option value="on" <?php Selected($this->Get('indicator_thumbnails'), 'on') ?> ><?php Echo $this->t('On') ?></option>
      <option value="off" <?php Selected($this->Get('indicator_thumbnails'), 'off') ?> ><?php Echo $this->t('Off') ?></option>
    </select>
    <p class="help"><?php Echo $this->t('Turn this off if you do not want to display small preview thumbnails below the lightbox image.') ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="slideshow_speed"><?php Echo $this->t('Slideshow speed') ?></label></th>
  <td>
    <input type="number" name="slideshow_speed" id="slideshow_speed" value="<?php Echo IntVal($this->Get('slideshow_speed')) ?>">
    <?php Echo $this->t('ms', 'Abbr. Milliseconds') ?>
    <p class="help"><?php Echo $this->t('The delay between two images in the slideshow.') ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="preload_images"><?php Echo $this->t('Preload images') ?></label></th>
  <td>
    <input type="number" name="preload_images" id="preload_images" value="<?php Echo IntVal($this->Get('preload_images')) ?>">
    <p class="help"><?php Echo $this->t('The number of images which should be preloaded around the current one.') ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="animation_speed"><?php Echo $this->t('Animation speed') ?></label></th>
  <td>
    <input type="number" name="animation_speed" id="animation_speed" value="<?php Echo IntVal($this->Get('animation_speed')) ?>">
    <?php Echo $this->t('ms', 'Abbr. Milliseconds') ?>
    <p class="help"><?php Echo $this->t('The speed of the image change animation.') ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="stretch_images"><?php Echo $this->t('Stretch images') ?></label></th>
  <td>
    <select name="stretch_images" id="stretch_images">
      <option value="" <?php Selected ($this->Get('stretch_images'), '') ?> ><?php Echo $this->t('No stretching') ?></option>
      <option value="contain" <?php Selected ($this->Get('stretch_images'), 'contain') ?> ><?php Echo $this->t('Contain') ?></option>
      <option value="cover" <?php Selected ($this->Get('stretch_images'), 'cover') ?> ><?php Echo $this->t('Cover') ?></option>
    </select>
    <p class="help"><?php Echo $this->t('"Contain" means to scale the image to the largest size such that both its width and its height can fit the screen.') ?></p>
    <p class="help"><?php Echo $this->t('"Cover" means to scale the image to be as large as possible so that the screen is completely covered by the image. Some parts of the image may be cropped and not in view.') ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="script_position"><?php Echo $this->t('Script position') ?></label></th>
  <td>
    <select name="script_position" id="script_position">
      <option value="footer" <?php Selected ($this->Get('script_position'), 'footer') ?> ><?php Echo $this->t('Footer of the website') ?></option>
      <option value="header" <?php Selected ($this->Get('script_position'), 'header') ?> ><?php Echo $this->t('Header of the website') ?></option>
    </select>
    <p class="help"><?php Echo $this->t('Please choose the position of the javascript. Footer is recommended. Use "Header" if you have trouble to make the lightbox work.') ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="asynchronous_loading"><?php Echo $this->t('Asynchronous loading') ?></label></th>
  <td>
    <select name="asynchronous_loading" id="asynchronous_loading">
      <option value="all" <?php Selected ($this->Get('asynchronous_loading'), 'all') ?> ><?php Echo $this->t('Load all components asynchronously') ?></option>
      <option value="none" <?php Selected ($this->Get('asynchronous_loading'), 'none') ?> ><?php Echo $this->t('Do not load anything asynchronously') ?></option>
      <option value="css" <?php Selected ($this->Get('asynchronous_loading'), 'css') ?> ><?php Echo $this->t('Load CSS asynchronously only') ?></option>
      <option value="js" <?php Selected ($this->Get('asynchronous_loading'), 'js') ?> ><?php Echo $this->t('Load JavaScripts asynchronously only') ?></option>
    </select>
    <p class="help"><?php Echo $this->t('If you are using a HTML/CSS/JS minifier you should disable the asynchronous loading.') ?></p>
  </td>
</tr>

</table>