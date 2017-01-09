<?php Namespace WordPress\Plugin\Fancy_Gallery;

If (!$this->Get('deactivate_archive')) : ?>
<p>
  <?php PrintF(I18n::t('The Archive link for your Galleries is: <div><a href="%1$s" target="_blank">%1$s</a></div>'), Get_Post_Type_Archive_Link($this->core->gallery_post_type->name)) ?>
</p>
<p>
  <?php PrintF(I18n::t('The Archive Feed for your Galleries is: <div><a href="%1$s" target="_blank">%1$s</a></div>'), Get_Post_Type_Archive_Feed_Link($this->core->gallery_post_type->name)) ?>
</p>
<?php EndIf ?>

<h4><?php Echo I18n::t('Deactivate Archive') ?></h4>
<p>
  <input type="checkbox" name="deactivate_archive" id="deactivate_archive" value="1" <?php Checked ($this->Get('deactivate_archive')) ?>>
  <label for="deactivate_archive"><?php Echo I18n::t('Deactivate the archive feature.') ?></label><br>
  <small><?php Echo I18n::t('If you tick this box visitors will not be able to navigate through the gallery archive.') ?></small>
</p>