<?php if (!defined('TMPL_DIR')) return; ?>

<form id="inputArea" action="index.php?action=login" method="post">
	<div>
        <label for="email">Email</label>
        <input id="email" type="text" name="email" value="<?php echo $HTML['email'];?>" />

        <label for="password">Password</label>
        <input id="password" type="password" name="password" value="<?php echo $HTML['password'];?>" />

        <span><?php echo $HTML['login_error'];?></span>

		<input class="submit" type="submit" value="Login" />
		<div class="user">
			New User?  <a href="index.php?action=signup" rel="external">Sign up here</a>
		</div>
		
		<input type="hidden" name="submitted" value="yes" />
	</div>
</form>
