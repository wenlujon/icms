<h4><?php Echo $this->core->t('Your update subscription account') ?></h4>
<table>
<tr>
  <td><label for="update_username"><?php _e('Username') ?></label></td>
  <td><input type="text" name="update_username" id="update_username" value="<?php Echo Esc_Attr($this->Get('update_username')) ?>"></td>
</tr>

<tr>
  <td><label for="update_password"><?php _e('Password') ?></label></td>
  <td><input type="password" name="update_password" id="update_password" value="<?php Echo Esc_Attr($this->Get('update_password')) ?>"></td>
</tr>
</table>