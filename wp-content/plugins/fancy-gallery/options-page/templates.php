<?php Namespace WordPress\Plugin\Fancy_Gallery ?>

<script type="text/javascript">
var $delete_confirm_message = "<?php Echo I18n::t('Are you sure you want to delete this template?') ?>";
</script>

<ol>
  <?php ForEach ( $this->core->Get_Template_Files() AS $template_name => $properties ) : ?>
  <li>
    <?php If (Empty($properties['name'])) : ?>
      <em><?php Echo $properties['file'] ?></em>
    <?php Else : ?>
      <strong><?php Echo $properties['name'] ?></strong>
    <?php EndIf ?>
    <?php If ($properties['version']) : ?> <small>(<?php Echo $properties['version'] ?>)</small><?php Endif; ?>
    <?php If ($properties['author'] && !$properties['author_uri'] ) : ?>
      <?php Echo I18n::t('by') ?> <?php Echo $properties['author'] ?>
    <?php ElseIf ($properties['author'] && $properties['author_uri'] ) : ?>
      <?php Echo I18n::t('by') ?> <a href="<?php Echo $properties['author_uri'] ?>" target="_blank"><?php Echo $properties['author'] ?></a>
    <?php Endif ?>
    (<small><a href="<?php Echo $this->Get_Options_Page_Url(Array('delete' => $properties['file'])) ?>" title="<?php Echo I18n::t('Delete this template') ?>" class="delete-link"><?php Echo I18n::t('Delete') ?></a></small>)
    <?php If ($properties['description']) : ?><br><?php Echo $properties['description']; Endif ?><br>
    <small><?php PrintF(I18n::t('Found in <em>%s</em>.'), SubStr($properties['file'], StrLen(ABSPATH))) ?></small>
  </li>
  <?php EndForEach ?>
</ol>


<h4><?php Echo I18n::t('Install a template in ZIP format') ?><?php $this->core->mocking_bird->Pro_Notice('unlock') ?></h4>
<p><?php Echo I18n::t('If you have a template as a .zip archive, you may install it by uploading it here.') ?></p>
<p><label for="template_zip"><?php Echo I18n::t('Template as ZIP File') ?></label> <input type="file" id="template_zip" <?php Disabled(True) ?> ></p>

<h4><?php Echo I18n::t('Install a template in PHP format') ?><?php $this->core->mocking_bird->Pro_Notice('unlock') ?></h4>
<p><?php Echo I18n::t('If you have a template as a .php file, you may install it by uploading it here.') ?></p>
<p><label for="template_php"><?php Echo I18n::t('Templates PHP File') ?></label> <input type="file" id="template_php" <?php Disabled(True) ?> ></p>

<p><input type="button" value="<?php Echo I18n::t('Install template and save all options') ?>" class="button-primary" <?php Disabled(True) ?> ></p>