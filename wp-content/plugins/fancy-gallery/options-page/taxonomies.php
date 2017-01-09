<?php Namespace WordPress\Plugin\Fancy_Gallery ?>

<p>
  <?php Echo I18n::t('Please select the taxonomies you need to classify your galleries.') ?>
</p>

<?php
$active_taxonomies = (Array) $this->Get('gallery_taxonomies');
ForEach ($this->core->gallery_post_type->arr_taxonomies AS $taxonomy => $tax_args) : ?>
<p>
  <input type="checkbox" name="gallery_taxonomies[<?php Echo $taxonomy ?>][name]" id="gallery_taxonomies_<?php Echo $taxonomy ?>" value="<?php Echo $taxonomy ?>" <?php Checked(IsSet($active_taxonomies[$taxonomy])) ?> >
  <label for="gallery_taxonomies_<?php Echo $taxonomy ?>"><?php Echo $tax_args['labels']['name'] ?></label>
  (<input type="checkbox" name="gallery_taxonomies[<?php Echo $taxonomy ?>][hierarchical]" id="gallery_taxonomies_<?php Echo $taxonomy ?>_hierarchical" <?php Checked(IsSet($active_taxonomies[$taxonomy]['hierarchical'])) ?>>
  <label for="gallery_taxonomies_<?php Echo $taxonomy ?>_hierarchical"><?php Echo I18n::t('hierarchical') ?></label>)
</p>
<?php EndForEach ?>

<?php ForEach (Array(I18n::t('Events'), I18n::t('Places'), I18n::t('Dates'), I18n::t('Persons'), I18n::t('Photographers')) AS $tax): ?>
<p>
  <input type="checkbox" <?php Disabled(True) ?> ><label><?php Echo $tax ?></label>
  (<input type="checkbox" <?php Disabled(True) ?> ><label><?php Echo I18n::t('hierarchical') ?></label>)<?php $this->core->mocking_bird->Pro_Notice('unlock') ?>
</p>
<?php EndForEach ?>

<p><?php $this->core->mocking_bird->Pro_Notice('custom_tax') ?></p>