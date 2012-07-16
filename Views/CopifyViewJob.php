<style type="text/css">
	/* Twitter bootstrap bits */
	.modal-backdrop {
	    background-color: #000000;
	    bottom: 0;
	    left: 0;
	    position: fixed;
	    right: 0;
	    top: 0;
	    z-index: 1040;
	}
	.modal-backdrop.fade {
	    opacity: 0;
	}
	.modal-backdrop, .modal-backdrop.fade.in {
	    opacity: 0.8;
	}
	.modal {
	    background-clip: padding-box;
	    background-color: #FFFFFF;
	    border: 1px solid rgba(0, 0, 0, 0.3);
	    border-radius: 6px 6px 6px 6px;
	    box-shadow: 0 3px 7px rgba(0, 0, 0, 0.3);
	    left: 50%;
	    margin: -250px 0 0 -280px;
	    overflow: auto;
	    position: fixed;
	    top: 50%;
	    width: 560px;
	    z-index: 1050;
	}
	.modal.fade {
	    -moz-transition: opacity 0.3s linear 0s, top 0.3s ease-out 0s;
	    top: -25%;
	}
	.modal.fade.in {
	    top: 50%;
	}
	.modal-header {
	    border-bottom: 1px solid #EEEEEE;
	    padding: 9px 15px;
	}
	.modal-header .close {
	    margin-top: 2px;
	}
	.modal-body {
	    max-height: 400px;
	    overflow-y: auto;
	    padding: 0 15px 15px 15px;
	}
	.modal-form {
	    margin-bottom: 0;
	}
	.modal-footer {
	    background-color: #F5F5F5;
	    border-radius: 0 0 6px 6px;
	    border-top: 1px solid #DDDDDD;
	    box-shadow: 0 1px 0 #FFFFFF inset;
	    margin-bottom: 0;
	    padding: 14px 15px 15px;
	    text-align: right;
	}
	.modal-footer:before, .modal-footer:after {
	    content: "";
	    display: table;
	}
	.modal-footer:after {
	    clear: both;
	}
	.modal-footer .btn + .btn {
	    margin-bottom: 0;
	    margin-left: 5px;
	}
	.modal-footer .btn-group .btn + .btn {
	    margin-left: -1px;
	}

	.close {
	    color: #000000;
	    float: right;
	    font-size: 20px;
	    font-weight: bold;
	    line-height: 18px;
	    opacity: 0.2;
	    text-shadow: 0 1px 0 #FFFFFF;
	}
	.close:hover {
	    color: #000000;
	    cursor: pointer;
	    opacity: 0.4;
	    text-decoration: none;
	}
	button.close {
	    background: none repeat scroll 0 0 transparent;
	    border: 0 none;
	    cursor: pointer;
	    padding: 0;
	}
	
</style>

<div class="wrap CopifyView CopifyPage">
	
	<?php if(isset($job) && !empty($job)) : ?>
	
		<div class="icon32" id="icon-copify">
			<br>
		</div>
	
		<h2>
			<?php echo sprintf('View Job #%s' , $job['id']); ?>
			<?php if(!empty($job['copy']) && $job['job_status_id'] == 3) : ?>
					<a class="add-new-h2 CopifyApproveAndDraft" id="" href="#">Approve & Move to Drafts</a>
			<?php else: ?>	
					<a class="add-new-h2" id="" href="?page=CopifyDashboard">« Back to all jobs</a>
			<?php endif; ?>	
		</h2>
	
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
	
		<div class="CopifyWell CopifyViewJob">
		
			
			<h1><?php echo $job['name']; ?></h1>
		
			
			<!-- Meta -->
			<span class="CopifyMeta">Date created : <?php echo date('jS F Y' , strtotime($job['created'])); ?></span>
			<span class="CopifyMeta"> 
				<?php
				if(array_key_exists($job['job_status_id'] , $statusList)) { // Job status
					$statusName = $statusList[$job['job_status_id']];
					$statusNameClass = str_replace(' ' , '_', strtolower($statusName));
				}
				?>
				<span class="<?php echo $statusNameClass; ?>">
					<?php echo $statusName; ?>
				</span> &nbsp; | &nbsp;
				<?php 
				$budgetName = '' ;
				
				if(array_key_exists($job['job_budget_id'] , $budgetList)) { // Budget name
					$budgetName = $budgetList[$job['job_budget_id']];
				}
				?>
				<span class="budget <?php echo strtolower($budgetName); ?>">
					<?php echo $budgetName; ?>
				</span> &nbsp;	| &nbsp;
				<?php 
				if(array_key_exists($job['job_category_id'] , $categoryList)) { // Category name
					echo $categoryList[$job['job_category_id']];
				}	
				?>
			</span>
			
			
			<!-- Show original brief -->
			<br>
			<span class="CopifyButton CopifyShowBriefClick">Show original brief</span>
			
			
			<!-- Move to drafts if already approved and not a post already -->
			<?php if(!empty($job['copy']) && $job['job_status_id'] == 4 && !$CopifyJobIsPostAlready) : ?>
				<span class="CopifyButton CopifyGreen CopifyMoveToDrafts">Move to Wordpress</span>
				<form style="display:none;">
					<input type="hidden" id="CopifyApproveJobIdHidden" value="<?php echo $job['id']; ?>" name="job_id">
				</form>	
				<span class="CopifyConfirmSaving" id="CopifyConfirmSaving" style="display:none;">&nbsp;</span>
			<?php endif; ?>
			
			
			<!-- This job is already in wordpress.... -->
			<?php if(!empty($job['copy']) && $job['job_status_id'] == 4 && $CopifyJobIsPostAlready) : 
				$urlText = 'Edit in Wordpress';
				$buttonClass = 'CopifyButton CopifyGreen';
				$linkToPost = sprintf('<a class="%s" href="post.php?post=%s&action=edit">%s</a>' ,$buttonClass, $CopifyJobIsPostAlready, $urlText); 
				echo $linkToPost;
				?>
			<?php endif; ?>
			
		
			<!-- The brief -->
			<div class="CopifyViewJobBriefDiv" style="display:none;">
				<div class="CopifyViewJobBrief message">
					<?php echo $this->CopifyFormatBrief($job['brief']); ?>
				</div>
			</div>
			
			
			<!-- Status info -->
			<?php if(in_array($job['job_status_id'], array(1,2,6,7))) : // Job status info ?>
				<div class="CopifyJobStatusInfo message">
					<h3><?php echo sprintf('Your job is <span class="%s">%s</span>. What happens next?' , $statusNameClass, $statusName); ?></h3>
					<p>Your order has been placed with an approved Copify writer, for now sit back and relax!</p>
					<p>When your job is complete, you will be notified by email and you will have chance to review the content before you publish it</p>
					<ul>
						<li><span class="open">Open</span> - Your job is in the queue for the next availalbe writer</li>
						<li><span class="in_progress">In progress</span> -  A writer is working on your content, you will receive an email once complete</li>
						<li><span class="completed">Completed</span> - Your content is ready for you to approve</li>
						<li><span class="approved">Approved</span> -  The content has been approved and is ready to publish</li>
					</ul>
				</div>	
			<?php endif; ?>
			
		
			<!-- The finished copy -->
			<?php if(!empty($job['copy']) && in_array($job['job_status_id'], array(3,4))) : ?>
				
				<h3 class="CopifyViewFinishedCopyHeading">The Finished Copy</h3>
				<div class="CopifyViewFinishedCopy">
					<?php echo $this->CopifyFormatCopy($job['copy']); ?>
				</div>
			
			<?php endif; ?>
			
			
			<!-- Modal for feedback -->
			<?php if(isset($CopifyWriter) && !empty($CopifyWriter) && $job['job_status_id'] == 3) : ?>
			
				<!-- Approve btn -->
				<span class="CopifyButton CopifyGreen CopifyApproveAndDraft">Approve & Move to Drafts</span>
			
				<!-- Modal -->
				<div id="CopifyFeedBackModal" class="modal" style="display:none;">
		
					<div class="modal-header">
						<button data-dismiss="modal" class="close" type="button">×</button>
				      	<h3>Approve & Move to Drafts</h3>
				    </div>
	    
					<div class="modal-body">

						<p>We try to ensure that all of the writers on Copify are of a great standard. Help us by leaving some honest feedback for <?php echo $CopifyWriter['first_name']; ?>... </p>

						<div class="CopifyFeedback">
	            
							<form id="CopifyFeedbackForm">
					
								<input type="hidden" id="CopifyApproveJobIdHidden" value="<?php echo $job['id']; ?>" name="job_id">
								<input type="hidden" id="CopifyApproveJobNameHidden" value="<?php echo $job['name']; ?>" name="name">
								<input type="hidden" id="CopifyApproveJobCopyHidden" value="<?php echo $job['copy']; ?>" name="copy">
				
									
								<label>
									<img alt="<?php echo $CopifyWriter['first_name']; ?>" src="<?php echo $CopifyWriter['avatar']; ?>">		            
								</label> 
		            
					            <div class="CopifyStarsDiv">
					            	<ul>
						                <li>
						                  <label>
						                    <div class="CopifyRating">
						                    	<input type="radio" checked="checked" name="CopifyRating" value="5">
						                    	<span class="CopifyStars CopifyStars5">5 stars</span>
						                    	<span class="CopifyFeedbackComment">Excellent, will use again!</span>
						                    </div>
						                  </label>
						                </li>
						                <li>
						                  <label>
						                    <div class="CopifyRating">
						                    	<input type="radio" name="CopifyRating" value="4">
						                    	<span class="CopifyStars CopifyStars4">4 stars</span>
						                    	<span class="CopifyFeedbackComment">Great work!</span>
						                    </div>
						                  </label>
						                </li>
						                <li>
						                  <label>
						                    <div class="CopifyRating">
						                    	<input type="radio" name="CopifyRating" value="3">
						                    	<span class="CopifyStars CopifyStars3">3 stars</span>
						                    	<span class="CopifyFeedbackComment">Just what the doctor ordered</span>
						                    </div>
						                  </label>
						                </li>
						                <li>
						                  <label>
						                    <div class="CopifyRating">
						                    	<input type="radio" name="CopifyRating" value="2">
						                    	<span class="CopifyStars CopifyStars2">2 stars</span>
						                    	<span class="CopifyFeedbackComment">Acceptable. Just.</span>
						                    </div>
						                  </label>
						                </li>
						                <li>
						                  <label>
						                    <div class="CopifyRating">
						                    	<input type="radio" name="CopifyRating" value="1">
						                    	<span class="CopifyStars CopifyStars1">1 stars</span>
						                    	<span class="CopifyFeedbackComment">Poor standard</span>
						                    </div>
						                  </label>
						                </li>
						         	</ul>
								</div>	
					
							</form>
				
			      		</div>

				    </div>
		
				    <div class="modal-footer">
						<span data-dismiss="modal" class="CopifyButton" >Cancel</span>
				      	<span class="CopifyButton CopifyGreen" id="CopifyConfirmApprove">Approve & Move to Drafts</span>
						<span class="CopifyConfirmSaving" id="CopifyConfirmSaving" style="display:none;">&nbsp;</span>
				    </div>
			
				</div>

			<?php endif; ?>
			
		</div>
	
	<?php else : ?>
		<div class="message error">
			Invalid job
		</div>
	<?php endif; ?>	

</div>

<script type="text/javascript">
jQuery(document).ready(function() {
	
	// Show brief. Chips chips chips chips chips
	jQuery('.CopifyShowBriefClick').click(function() {
		if(jQuery(this).html() == 'Show original brief') {
			jQuery(this).html('Hide original brief');
		} else {
			jQuery(this).html('Show original brief');
		}
		jQuery('.CopifyViewJobBriefDiv').toggle();
		
	});
	
	// Show approve modal
	jQuery('.CopifyApproveAndDraft').click(function() {
		jQuery('#CopifyFeedBackModal').modal({
			show: true
		});
	});
	
	// Post feedback via ajax, show indicator, check success and redirect...
	jQuery('#CopifyConfirmApprove').click(function() {
		
		// Hide the approve button and show the spinny thing
		jQuery('#CopifyFeedBackModal .CopifyButton').hide();
		jQuery('#CopifyConfirmSaving').show();
		
		// Get the variables we need from la form...
		var job_id = jQuery('#CopifyApproveJobIdHidden').val();
		var name = jQuery('#CopifyApproveJobNameHidden').val();
		var copy = jQuery('#CopifyApproveJobCopyHidden').val();
		var comment = jQuery('.CopifyStarsDiv').find('input:checked').parent('.CopifyRating').find('.CopifyFeedbackComment').html();
		var rating = jQuery('.CopifyStarsDiv').find('input:checked').val();
		
		// Our feedback ob:
		var feedback = {
			action: 'CopifyPostFeedback',
			job_id: job_id,
			name: name,
			copy: copy,
			comment: comment,
			rating: rating
		};

		// Make ajax request. Fun this isn't it!?
		jQuery.ajax(ajaxurl, {
			type: 'post',
			data: feedback,
			dataType : 'json',
			success: function(data) {
				if(data.status == 'success') {
					// OK
					jQuery('#CopifyConfirmApprove').hide();
					jQuery('#CopifyConfirmSaving').hide();
					window.location.href = window.location.href + '&flashMessage=Job+approved+and+moved+to+Drafts';
				} else {
					alert(data.message);
				}
			}, 
			error: function(jqXHR, textStatus, errorThrown) {
				alert(errorThrown);
			},
			timeout: 30000,
			cache: false
		});

		
	});
	
	
	// Move an already approved job to drafts
	jQuery('.CopifyMoveToDrafts').click(function() {
		
		jQuery(this).hide();
		jQuery('#CopifyConfirmSaving').show();
		
		var job_id = jQuery('#CopifyApproveJobIdHidden').val();
		
		var job = {
			job_id: job_id,
			action: 'CopifyMoveToDrafts'
		};
		
		jQuery.ajax(ajaxurl, {
			type: 'post',
			data: job,
			dataType : 'json',
			success: function(data) {
				if(data.status == 'success') {
					// OK
					window.location.href = window.location.href + '&flashMessage=Job+moved+to+Drafts';
				} else {
					alert(data.message);
				}
			}, 
			error: function(jqXHR, textStatus, errorThrown) {
				alert(errorThrown);
			},
			timeout: 30000,
			cache: false
		});
		
	});
	
	<?php if($job['job_status_id'] != 4) : ?>
	
	//Prevent copy paste from copy area
	jQuery('.CopifyViewFinishedCopy').bind('cut copy paste', function(e) {
		e.preventDefault();
		alert('Please approve before copying! Thanks!');
	});
	
	<?php endif; ?>
	
});
</script>