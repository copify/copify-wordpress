<div class="wrap CopifyDashboard CopifyPage">

	<div class="icon32" id="icon-copify">
		<br>
	</div>
	
	<h2>Copify
		<a class="add-new-h2" id="CopifyNewOrder" href="?page=CopifyOrder">Order content</a>
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
	
	
	<?php 
		
	// Toggle sort option
	$toggle = 'asc';
	if($direction == 'asc') {
		$toggle = 'desc';
	}
		
	if(isset($CopifyJobs['jobs']) && !empty($CopifyJobs['jobs'])) : ?>
	
	<div class="tablenav bottom">
		<div class="tablenav-pages">
			<span class="displaying-num"><?php echo $total; ?> items</span>
			<span class="pagination-links">
				<a href="<?php echo "?page=$page&pageNumber=1&sort=$sort&direction=$direction"; ?>" title="Go to the first page" class="first-page">«</a>
				<a href="<?php echo "?page=$page&pageNumber=$prevPage&sort=$sort&direction=$direction"; ?>" title="Go to the previous page" class="prev-page">‹</a>
				<span class="paging-input">
					<?php echo $paginateNumber; ?> of <span class="total-pages"><?php echo $totalPages; ?></span>
				</span>
				<a href="<?php echo "?page=$page&pageNumber=$nextPage&sort=$sort&direction=$direction"; ?>" title="Go to the next page" class="next-page">›</a>
				<a href="<?php echo "?page=$page&pageNumber=$totalPages&sort=$sort&direction=$direction"; ?>" title="Go to the last page" class="last-page">»</a>
			</span>
		</div>
	</div>
	
	<table class="wp-list-table widefat">
		<tr>
			<th>
				<a class="<?php if($sort == 'id') echo 'on '; echo $toggle; ?>" href="<?php echo "?page=$page&pageNumber=$pageNumber&sort=id&direction=$toggle"; ?>">#</a>
			</th>
			<th>
				<a class="<?php if($sort == 'name') echo 'on '; echo $toggle; ?>" href="<?php echo "?page=$page&pageNumber=$pageNumber&sort=name&direction=$toggle"; ?>">Job name</a>
			</th>
			<th>
				<a class="<?php if($sort == 'job_category_id') echo 'on '; echo $toggle; ?>" href="<?php echo "?page=$page&pageNumber=$pageNumber&sort=job_category_id&direction=$toggle"; ?>">Category</a>
			</th>
			<th>Quality Level</th>
			<th>
				<a class="<?php if($sort == 'job_status_id') echo 'on '; echo $toggle; ?>" href="<?php echo "?page=$page&pageNumber=$pageNumber&sort=job_status_id&direction=$toggle"; ?>">Status</a>
			</th>
		</tr>
		<?php foreach($CopifyJobs['jobs'] as $k => $job) : ?>
		<tr>
			<td><?php echo $job['id']; ?></td>
			<td class="">
				<?php $JobId = $job['id']; ?>
				<a href="<?php echo "?page=CopifyViewJob&id=$JobId"; ?>"><?php echo $job['name']; ?></a>
			</td>
			<td>
				<?php if(array_key_exists($job['job_category_id'] , $categoryList)) echo $categoryList[$job['job_category_id']]; ?>
			</td>
			<td>
				<?php 
				$budgetName = '' ;
				if(array_key_exists($job['job_budget_id'] , $budgetList)) {
					$budgetName = $budgetList[$job['job_budget_id']];
				}
				?>
				<span class="budget <?php echo strtolower($budgetName); ?>">
					<?php echo $budgetName; ?>
				</span>	
			</td>
			<?php 
			$statusName = '' ;
			if(array_key_exists($job['job_status_id'] , $statusList)) {
				$statusName = $statusList[$job['job_status_id']];
			}
				
			// Is this in wordpress as a post?
			$linkClass = 'statusName';
			if(in_array($job['id'] , $CopifyPostIds)) {
				$linkClass .= ' savedInWordpress';
			}
				
			?>
			<td class="<?php echo $linkClass; ?>">
				<span class="<?php echo str_replace(' ' , '_', strtolower($statusName)); ?>">
					<?php echo $statusName; ?>
				</span>		
			</td>
		</tr>
		<?php endforeach; ?>	
	</table>
	
	<br/>
	<a class="CopifyButton CopifyGreen" href="?page=CopifyOrder">Order content</a>
	
	<div class="tablenav bottom">
		<div class="tablenav-pages">
			<span class="displaying-num"><?php echo $total; ?> items</span>
			<span class="pagination-links">
				<a href="<?php echo "?page=$page&pageNumber=1&sort=$sort&direction=$direction"; ?>" title="Go to the first page" class="first-page">«</a>
				<a href="<?php echo "?page=$page&pageNumber=$prevPage&sort=$sort&direction=$direction"; ?>" title="Go to the previous page" class="prev-page">‹</a>
				<span class="paging-input">
					<?php echo $paginateNumber; ?> of <span class="total-pages"><?php echo $totalPages; ?></span>
				</span>
				<a href="<?php echo "?page=$page&pageNumber=$nextPage&sort=$sort&direction=$direction"; ?>" title="Go to the next page" class="next-page">›</a>
				<a href="<?php echo "?page=$page&pageNumber=$totalPages&sort=$sort&direction=$direction"; ?>" title="Go to the last page" class="last-page">»</a>
			</span>
		</div>
	</div>
	
	<?php else : ?>
	
	
	<div class="message">
		It looks like you have no jobs with Copify yet. <a href="?page=CopifyOrder">Click here to place your first order</a>
	</div>
	
	<?php endif; ?>	

</div>
