<section class="container">
	<div class="row">
		<?php
		$db = Craigslist\Database::getInstance();
		$jobs = $db->getJobs();

        $i = 0;
		foreach( $jobs as $job ) {
		    $i++;
		?>
			<article class="col-md-12 <?= $i % 2 ? 'odd' : 'even'; ?>">
				<h2>
                    <a href="api/save/<?= $job->id; ?>" class="badge badge-default"><i class="fa fa-heart" aria-hidden="true"></i></a>
                    <a href="api/delete/<?= $job->id; ?>" class="badge badge-default"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    <a href="<?= $job->link; ?>" target="_blank"><?= $job->title; ?></a>
				</h2>
				<p><?php echo $job->description; ?></p>
                <hr />
			</article>
		<?php } ?>
	</div>
</section>
