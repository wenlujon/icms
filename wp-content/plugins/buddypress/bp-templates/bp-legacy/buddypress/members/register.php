<div id="buddypress">

	<?php do_action( 'bp_before_register_page' ); ?>

	<div class="reg-page2" id="register-page">

		<form action="" name="signup_form" id="signup_form" class="register-form" method="post" enctype="multipart/form-data">

		<?php if ( 'registration-disabled' == bp_get_current_signup_step() ) : ?>
			<?php do_action( 'template_notices' ); ?>
			<?php do_action( 'bp_before_registration_disabled' ); ?>

				<p><?php _e( 'User registration is currently not allowed.', 'buddypress' ); ?></p>

			<?php do_action( 'bp_after_registration_disabled' ); ?>
		<?php endif; // registration-disabled signup step ?>

		<?php if ( 'request-details' == bp_get_current_signup_step() ) : ?>

			<?php do_action( 'template_notices' ); ?>

			<?php do_action( 'bp_before_account_details_fields' ); ?>

			<div class="register-section" id="basic-details-section">

				<?php /***** Basic Account Details ******/ ?>

				<script type="text/javascript">
		function checkWord(len,evt){
//len为英文字符的个数，中文自动为其一般数量
//evt是欲检测的对象
   var str = evt.value;
    var myLen = 0;
    for(i=0; i<str.length&&myLen<=len; i++){
        if(str.charCodeAt(i)>0&&str.charCodeAt(i)<128)
            myLen++;
        else
            myLen+=2;
        }
    if(myLen>len){
        alert("您输入超过限定长度");
        evt.value = str.substring(0,i-1);
    }
} 
function check() { 
var regC = /[^ -~]+/g; 
var regE = /\D+/g; 
var str = t1.value; 
 
if (regC.test(str)){ 
t1.value = t1.value.substr(0,10); 
} 
 
if(regE.test(str)){ 
t1.value = t1.value.substr(0,20); 
} 
} 
				</script>


			<p>
				<span><label for="signup_username">用户名 </label></span>
				<span><input type="text" name="signup_username" id="signup_username" value="<?php bp_signup_username_value(); ?>" <?php bp_form_field_attributes( 'username' ); ?> placeholder="英文字母或者数字" maxlength="20"/></span>
				<?php do_action( 'bp_signup_username_errors' ); ?>
			</p>


			<?php if ( bp_is_active( 'xprofile' ) ) : if ( bp_has_profile( array( 'profile_group_id' => 1, 'fetch_field_data' => false ) ) ) : while ( bp_profile_groups() ) : bp_the_profile_group(); ?>
				<p>
				<span><label for="field_1"><?php _e( '昵称', 'buddypress' ); ?> </label> </span>

				<span><input type="text" id="field_1" name="field_1" value="<?php bp_signup_displayname_value(); ?>" aria-required="true" placeholder="可以使用中文名"  maxlength="20"/></span>
				<script type="text/javascript" src="../../../jquery-1.4.3.min.js" ></script>
				<script type="text/javascript">
				$(document).ready(function(){
					$("#signup_username").blur(function(){
						$('#field_1').val($('#signup_username').val());
					});

				});
				</script>
				<?php do_action( 'bp_field_1_errors' ); ?>
				</p>


				<input type="hidden" name="signup_profile_field_ids" id="signup_profile_field_ids" value="<?php bp_the_profile_field_ids(); ?>" />
		<?php endwhile; endif; endif; ?>

				<p>
				<span><label for="signup_email"><?php _e( '邮箱', 'buddypress' ); ?> </label></span>
				<span><input type="email" name="signup_email" id="signup_email" value="<?php bp_signup_email_value(); ?>" <?php bp_form_field_attributes( 'email' ); ?>  placeholder="Gmail/新浪/QQ/163" maxlength="50"/></span>
				<?php do_action( 'bp_signup_email_errors' ); ?>
				</p>

				<p>
				<label for="signup_password"><?php _e( '密码', 'buddypress' ); ?> </label>
				<span><input type="password" name="signup_password" id="signup_password" value="" class="password-entry" <?php bp_form_field_attributes( 'password' ); ?>  maxlength="50"/></span>
				<?php do_action( 'bp_signup_password_errors' ); ?>
				</p>

				<div id="pass-strength-result"></div>

				<p>
				<label for="signup_password_confirm"><?php _e( 'Confirm Password', 'buddypress' ); ?> </label>
				<span><input type="password" name="signup_password_confirm" id="signup_password_confirm" value="" class="password-entry-confirm" <?php bp_form_field_attributes( 'password' ); ?>  maxlength="50"/></span>
				<?php do_action( 'bp_signup_password_confirm_errors' ); ?>
				</p>
				<?php do_action( 'bp_account_details_fields' ); ?>

			</div><!-- #basic-details-section -->

			<?php do_action( 'bp_after_account_details_fields' ); ?>


				<?php do_action( 'bp_after_signup_profile_fields' ); ?>



			<?php do_action( 'bp_before_registration_submit_buttons' ); ?>

			<div class="reg-button" style="margin-left: 120px; padding: 8px 12px; ">
				<input type="submit" name="signup_submit" id="signup_submit" value="<?php esc_attr_e( '   注册   ', 'buddypress' ); ?>" />
			</div>

			<?php do_action( 'bp_after_registration_submit_buttons' ); ?>

			<?php wp_nonce_field( 'bp_new_signup' ); ?>

		<?php endif; // request-details signup step ?>

		<?php if ( 'completed-confirmation' == bp_get_current_signup_step() ) : ?>

			<?php do_action( 'template_notices' ); ?>
			<?php do_action( 'bp_before_registration_confirmed' ); ?>

			<?php if ( bp_registration_needs_activation() ) : ?>
				<p><?php _e( 'You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.', 'buddypress' ); ?></p>
			<?php else : ?>
				<p><?php _e( 'You have successfully created your account! Please log in using the username and password you have just created.', 'buddypress' ); ?></p>
			<?php endif; ?>

			<?php do_action( 'bp_after_registration_confirmed' ); ?>

		<?php endif; // completed-confirmation signup step ?>

		<?php do_action( 'bp_custom_signup_steps' ); ?>

		</form>

	</div>

	<?php do_action( 'bp_after_register_page' ); ?>

</div><!-- #buddypress -->
