<section class="content">
<?php
$db   = Craigslist\Database::getInstance();
$jobs = $db->getJobs(
    array('hide' => 0),
    array( 'hide' => '=' ),
    50
);

$i = 0;
foreach ( $jobs as $job ) {
	$i ++;
	$job = \Craigslist\WebUI::filterContent( $job );
	$articleClass = $i % 2 ? 'odd' : 'even';
	if ( $job->saved ) {
		$articleClass .= ' saved';
	}
	?>
    <section class="post <?= $articleClass; ?>">
        <div class="container">
            <div class="row">
                <article class="col-md-12">
                    <h2>
                        <a href="<?= $job->link; ?>" target="_blank"><?= $job->title; ?></a>
                    </h2>
                    <p>
                        <time datetime="<?= $job->date; ?>"><?= date( 'g:i a, M d, Y', strtotime( $job->date ) ); ?></time>
                        <span class="icons">
                            <a href="api/save/<?= $job->id; ?>" class="badge badge-default"><i class="fa fa-heart" aria-hidden="true"></i></a>
                            <a href="api/hide/<?= $job->id; ?>" class="badge badge-default"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </span>
                    </p>
                    <p class="description"><?= $job->description; ?></p>
                </article>
            </div>
        </div>
    </section>
    <!--<hr />-->
<?php } ?>
</section>
