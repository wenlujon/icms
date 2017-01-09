<?php Namespace WordPress\Plugin\Fancy_Gallery ?>

<p><?php Echo I18n::t('Want to unlock all settings and features? Upgrade to Fancy Gallery Pro!') ?></p>
<p>
  <a href="<?php $this->core->mocking_bird->Pro_Notice('upgrade_url') ?>" target="_blank">
    <img src="<?php Echo $this->core->base_url ?>/assets/img/fancy-gallery-pro.png" class="premium-banner" title="Fancy Gallery Pro">
  </a>
</p>
<a href="<?php $this->core->mocking_bird->Pro_Notice('upgrade_url') ?>" target="_blank" class="button button-primary button-block button-large"><?php $this->core->mocking_bird->Pro_Notice('upgrade') ?></a>