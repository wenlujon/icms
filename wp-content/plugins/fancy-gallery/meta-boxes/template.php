<?php Namespace WordPress\Plugin\Fancy_Gallery ?>
<p><?php Echo I18n::t('Please choose a template to display this gallery.') ?></p>
<?php ForEach ( $this->core->Get_Template_Files() AS $name => $properties ) : ?>
<p>
  <input type="radio" name="<?php Echo $this->Field_Name('template') ?>" id="<?php Echo Sanitize_Title($properties['file']) ?>" value="<?php Echo HTMLSpecialChars($properties['file']) ?>"
    <?php Checked($this->Get_Meta('template'), $properties['file']) ?>
    <?php Checked(!$this->Get_Meta('template') && $properties['file'] == $this->core->Get_Default_Template()) ?> >
  <label for="<?php Echo sanitize_title($properties['file']) ?>">
  <?php If (Empty($properties['name'])) : ?>
    <em><?php Echo $properties['file'] ?></em>
  <?php Else : ?>
    <strong><?php Echo $properties['name'] ?></strong>
  <?php EndIf; ?>
  </label>
  <?php If ($properties['version']) : ?> (<?php Echo $properties['version'] ?>)<?php Endif; ?>
  <?php If ($properties['author'] && !$properties['author_uri'] ) : ?>
    <?php Echo I18n::t('by') ?> <?php Echo $properties['author'] ?>
  <?php ElseIf ($properties['author'] && $properties['author_uri'] ) : ?>
    <?php Echo I18n::t('by') ?> <a href="<?php Echo $properties['author_uri'] ?>" target="_blank"><?php Echo $properties['author'] ?></a>
  <?php Endif; ?>
  <?php If ($properties['description']) : ?><br><?php Echo $properties['description']; Endif ?>
</p>
<?php EndForEach;