<section class="container">
	<div class="row">
		<?php
		$db = Craigslist\Database::getInstance();
		$jobs = $db->getJobs();

		foreach( $jobs as $job ) {
		?>
			<article>
				<h2><?php echo sprintf('<a href="%s">%s</a> %s',
					$job->link,
					$job->title,
					'<span class="badge badge-default">' . $job->name. '</span>'
				); ?></h2>
				<p><?php echo $job->description; ?></p>
			</article>
		<?php } ?>
	</div>
</section>
