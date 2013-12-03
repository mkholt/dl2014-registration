<?php
/**
 * Security check - make sure we were included by the main plugin file
 */
if (!defined('REGISTRATION_LOAD_VIEW')) exit();
?>
<p>
	Velkommen til forhåndstilmeldingen for Djurslejren 2014.<br/>
	Da du ikke er logget ind, bedes du logge ind med de login oplysninger, du har modtaget, herunder.
</p>
<?php if (!empty($error)) { ?>
	<div class="message error"><?= $error ?></div>
<?php } ?>
<form name="loginform" id="loginform" action="<?= site_url() ?>/<?= REGISTRATION_PAGE_CONTROLLER_NAME ?>/logon" method="post">
	<p class="login-username">
		<label for="user_login"><?= __("Username:") ?></label>
		<input type="text" name="log" id="user_login" class="input" value="" size="20" />
	</p>
	<p class="login-password">
		<label for="user_pass"><?= __("Password:") ?></label>
		<input type="password" name="pwd" id="user_pass" class="input" value="" size="20" />
	</p>
	<p class="login-remember"><label><input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?= __("Remember Me") ?></label></p>
	<p class="login-submit">
		<input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="<?= __("Log In") ?>" />
		<input type="hidden" name="redirect_to" value="<?= site_url() ?>/<?= REGISTRATION_PAGE_CONTROLLER_NAME ?>/logon/" />
	</p>
</form>