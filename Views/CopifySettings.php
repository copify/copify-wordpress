<div class="wrap CopifySettings CopifyPage">

	<div class="icon32" id="icon-copify">
		<br>
	</div>
	
	<h2>Settings</h2>
	
	<?php if(!function_exists('curl_init')) : ?>
		<div class="message error">
			This Plugin requires cURL
		</div>
	<?php endif; ?>

	<?php if(isset($error)) : ?>
		<div class="message error">
			<?php echo $error;  ?>
		</div>
			
	<?php endif; ?>	
	
	<?php if(isset($success)) : ?>
		<div class="message success">
			<?php echo $success; ?>
		</div>
	<?php endif; ?>	

	<div class="message">
		Enter your Copify <strong>Email</strong> and <strong>API Key</strong>. <a target="blank" href="https://www.copify.com/users/settings">Get your API key from Copify</a>
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
				<input name="CopifyApiKey" style="width:300px;" value="<?php echo $CopifyApiKey; ?>" class="CopifyApiKey" type="text" maxLength="40" />
			</div>
			
			<label for="CopifyApiKey">Select country</label>
			<div class="input">
				<select name="CopifyLocale">
					<?php
					foreach($CopifyAvailableLocales as $loc => $name) {
						$selected = '';
						if($CopifyLocale == $loc) {
							$selected = 'selected="selected"';
						}
						echo sprintf('<option value="%s" %s>%s</option>' , $loc , $selected, $name); 
					}
					?>
				</select>	
			</div>
			<?php submit_button(); ?>
		</fieldset>			
	</form>

</div>
