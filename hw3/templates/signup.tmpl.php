<?php if (!defined('TMPL_DIR')) return; ?>

<form id="inputArea" action="index.php?action=signup" method="post">
	<div>
        <label>Email</label>
        <input type="text" name="email" value="<?php echo $HTML['email'];?>" />
        <span><?php echo $HTML['email_error'];?></span>

        <label>Password</label>
        <input type="password" name="password" value="<?php echo $HTML['hidden_password'];?>" />

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" value="<?php echo $HTML['hidden_password'];?>" />
		<span><?php echo $HTML['confirm_password_error'];?></span>

        <label>City</label>
        <input type="text" name="city" value="<?php echo $HTML['city'];?>" />
        <span><?php echo $HTML['city_error'];?></span>

        <label>Country</label>	
		<select name="countryID"><?php echo $HTML['country_options_escape'];?></select>
        <span><?php echo $HTML['countryID_error'];?></span>

		<span><?php echo $HTML['signup_error'];?></span>

		<input class="submit" type="submit" value="Sign Up" />
		<input type="hidden" name="submitted" value="yes" />
		<input type="hidden" name="password_encrypted" value="<?php echo $HTML['password'];?>" />
		
		<div class="user">
			Registered User?  <a href="index.php?action=login" rel="external">Login here</a>
		</div>
	</div>
</form>
