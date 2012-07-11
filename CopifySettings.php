<div class="wrap CopifySettings CopifyPage">

	<div class="icon32" id="icon-copify">
		<br>
	</div>
	
	<h2>Settings</h2>

	<?php if(isset($error)) : ?>
		<div class="message error">
			<?php echo $error; ?>
		</div>
			
	<?php endif; ?>	
	
	<?php if(isset($success)) : ?>
		<div class="message success">
			<?php echo $success; ?>
		</div>
	<?php endif; ?>	

	<div class="message">
		Enter your Copify <strong>Email</strong> and <strong>API Key</strong>. <a href="#">View instructions how to get these</a>
	</div>	

	<form method="POST" class="CopifyForm CopifyWell">
		<fieldset>
			<legend>Connect your Copify Account</legend>
			
			<label for="CopifyEmail">Email</label>
			<div class="input">
				<input name="CopifyEmail" value="<?php echo $CopifyEmail; ?>" class="CopifyEmail" type="text" maxLength="100" />
			</div>

			<label for="CopifyApiKey">API Key</label>
			<div class="input">
				<input name="CopifyApiKey" value="<?php echo $CopifyApiKey; ?>" class="CopifyApiKey" type="text" maxLength="40" />
			</div>

			<?php submit_button(); ?>
		</fieldset>			
	</form>

</div>
