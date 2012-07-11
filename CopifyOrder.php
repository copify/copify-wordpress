<div class="wrap CopifyOrder CopifyPage">
	
	<div class="icon32" id="icon-copify">
		<br>
	</div>
	
	<h2>Order Content</h2>

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
		
	<?php if(isset($message)) : ?>
		<div class="message">
			<?php echo $message; ?>
		</div>
	<?php endif; ?>

	<form method="POST" class="CopifyForm CopifyWell">
		<fieldset>
			
			<legend>Place a new order through Copify</legend>
			
			<table>
			<tr>
				<td>
					<label for="name">Enter a title for your job</label>
					<div class="input">
						<input name="name" placeholder="This will be the title of your new post"  type="text" maxLength="100" />
					</div>
				</td>
				<td>
					<?php 
					if(is_array($categoryList)) : 
						echo '<label for="job_category_id">Choose a category that best describes your topic</label>';
						echo '<div class="input">';
						echo '<select name="job_category_id">';
						foreach($categoryList as $category_id => $category_name) :
							echo "<option value=\"$category_id\">$category_name</option>\n";
						endforeach; 
						echo '</select>';
						echo '</div>';
					endif; 
					?>
				</td>	
			</tr>
			<tr>
				<td colspan="2">			
					<label for="brief">Your requirements</label>
					<div class="input">
						<textarea name="brief" rows="10" cols="90" placeholder="Enter your brief here. In exact detail, describe what you require from the job and provide the writer with as much information as possible in order to meet your requirements." /></textarea>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<label for="words">How many words do you require?</label>
					<div class="input">
						<input name="words" class="CopifyTiny" placeholder="300" value="300" type="text" maxLength="6" />
					</div>
				</td>
			</tr>	
			<tr>
				<td>			
					<label for="job_budget_id">Choose writer standard</label>
					<div class="input">
						<input name="job_budget_id" type="radio" value="1" checked="checked" /><span class="CopifyRadioSpan budget standard">Standard</span>
						<input name="job_budget_id" type="radio" value="2" /><span class="CopifyRadioSpan budget professional">Professional</span>
					</div>
				</td>
				<td>
					<input type="hidden" value="4" name="job_type_id" />
					<input type="submit" class="CopifyButton CopifyGreen CopifyFloatRight" value="Place order" />
				</td>	
			</tr>
			</table>
		
		</fieldset>
	</form>	

</div>

<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('form.CopifyForm:first *:input[type!=hidden]:first').focus();
	});
</script>