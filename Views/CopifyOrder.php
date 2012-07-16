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
	
	
	<?php
	// If we don't have our category or budget arrays it means we can't place an order, 
	// and that the API key is wrong so don't show the form
	if(is_array($budgetList) && is_array($categoryList)) :
	?>

	<form id="CopifyOrderForm" class="CopifyForm CopifyWell">
		<fieldset>
			
			<legend>Place a new order through Copify</legend>
			
			<table>
			<tr>
				<td>
					<label for="name">Enter a title for your job</label>
					<div class="input">
						<input name="name" class="required" placeholder="This will be the title of your new post" value="<?php echo $name; ?>"  type="text" minLength="5" maxLength="100" />
					</div>
				</td>
				<td>
					<?php 
					if(is_array($categoryList)) : 
						echo '<label for="job_category_id">Choose a category that best describes your topic</label>';
						echo '<div class="input">';
						echo '<select name="job_category_id">';
						foreach($categoryList as $category_id => $category_name) :
							$selected = '';
							if($category_id == $job_category_id) { // Retain users selection if error
								$selected = 'selected="selected"';
							}
							echo "<option value=\"$category_id\" $selected >$category_name</option>\n";
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
						<textarea name="brief" class="required" rows="10" cols="90" minLength="10" placeholder="Enter your brief here. In exact detail, describe what you require from the job and provide the writer with as much information as possible in order to meet your requirements." /><?php echo $brief; ?></textarea>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<label for="words">How many words do you require?</label>
					<div class="input">
						<?php
						// default words
						$words = '300';
						?>
						<input name="words" class="CopifyTiny required" placeholder="300" value="<?php echo $words; ?>" type="text" maxLength="6" minlength="3" />
					</div>
				</td>
				<td>
					<span class="CopifyOrderCost"></span>
					<span class="CopifyOrderDeliveryTime"></span>
				</td>	
			</tr>	
			<tr>
				<td>			
					<label for="job_budget_id">Choose writer standard</label>
					<div class="input">
						<?php
							if(empty($job_budget_id)) {
								$default = array_shift(array_keys($budgetList));
							}
							foreach($budgetList as $budget_id => $budget_name) {
								$lowerCaseName = strtolower($budget_name);
								$checked = '';
								if($job_budget_id == $budget_id || $default == $budget_id) {
									$checked = 'checked="checked"';
								} 
								$radio = '<input name="job_budget_id" type="radio" value="%d" %s /><span class="CopifyRadioSpan budget %s">%s</span>';
								echo sprintf($radio , $budget_id, $checked, $lowerCaseName , $budget_name);
							}
						?>
					</div>
				</td>
				<td>
					<input type="hidden" value="4" name="job_type_id" />
					<input type="submit" id="CopifySaveJobButton" class="CopifyButton CopifyGreen CopifyFloatRight" value="Place order" />
				</td>	
			</tr>
			</table>
		
		</fieldset>
	</form>	
	
	<?php endif; ?>

</div>


<script type="text/javascript">
	jQuery(document).ready(function() {
		
				
		/*********************************
		* Validate & post new job
		*********************************/
		jQuery("#CopifyOrderForm").validate({
			rules : {
				name: 'required',
				brief: 'required',
				words: 'required'
			},
			messages: {
				name: 'Please enter a title',
				brief: 'Enter what you require',
				words: 'Min. 100 words'
			},
			submitHandler : function(form) {
				
				jQuery('.CopifyOrderCost').addClass('loading');
				jQuery('#CopifySaveJobButton').removeClass('CopifyGreen');
				jQuery('#CopifySaveJobButton').attr('disabled' , 'disabled');
				jQuery('#CopifySaveJobButton').attr('value' , 'Saving...');
		
				var newJob = {
					job: jQuery('#CopifyOrderForm').serialize(),
					action: 'CopifyAjaxOrder'
				};
		
				jQuery.ajax(ajaxurl, {
					type: 'post',
					data: newJob,
					dataType : 'json',
					success: function(data) {
						if(data.status == 'success') {
							// OK
							jQuery('.CopifyOrderCost').removeClass('loading');
							var newJobId = data.response.id;
							window.location.href = '?page=CopifyDashboard&flashMessage=You+have+successfully+added+job+'+newJobId;
						} else {
							jQuery('.CopifyOrderCost').removeClass('loading');
							jQuery('#CopifySaveJobButton').addClass('CopifyGreen');
							jQuery('#CopifySaveJobButton').removeAttr('disabled');
							jQuery('#CopifySaveJobButton').attr('value' , 'Place order');
							alert(data.message);
						}
					}, 
					error: function(jqXHR, textStatus, errorThrown) {
						alert(errorThrown);
					},
					timeout: 30000,
					cache: false
				});
			}
		});
		
		
		
		
		/*********************************
		* Method to update cost
		*********************************/
		
		function getQuote() {
			
			jQuery('.CopifyOrderCost').addClass('loading');
			
			var job_budget_id = jQuery('input[name=job_budget_id]:checked').val();
			var words = jQuery('input[name=words]').val();
			
			var budget = {
				job_budget_id: job_budget_id,
				words: words,
				action: 'CopifyQuoteWords'
			};
		
			jQuery.ajax(ajaxurl, {
				type: 'post',
				data: budget,
				dataType : 'json',
				success: function(data) {
					if(data.status == 'success') {
						// OK
						jQuery('.CopifyOrderCost').html(data.response.cost);
						jQuery('.CopifyOrderDeliveryTime').html(data.response.client_deadline);
						jQuery('.CopifyOrderCost').removeClass('loading');
						jQuery('.CopifyOrderCost').css('color' , '#999');
					} else {
						jQuery('.CopifyOrderCost').html(data.message);
						jQuery('.CopifyOrderCost').removeClass('loading');
						jQuery('.CopifyOrderCost').css('color' , 'red');
					}
				}, 
				error: function(jqXHR, textStatus, errorThrown) {
					alert(errorThrown);
				},
				timeout: 30000,
				cache: false
			});
			
		}
		

		// Get cost when budget changed
		jQuery('input[name=job_budget_id]').click(function() {
			getQuote();
		});
		
		// Get cost when change word count
		jQuery("input[name=words]").keyup(function() {
			getQuote();
		});
		
		// Re-quote on page load
		getQuote();
		
	});
</script>