<?php
$time_start = microtime(true);
ob_implicit_flush(true);
ob_start();


$search = 'wordpress|php|html|css|javascript -"on site" -onsite -"full time" -intern';
$exclude = '12-1-2-1-7-1-2-1-1-19-1-1-1-2-2-1-2-2-2-14-25-25-1-1-1-1-1-1';

$args = array(
	'category' => 'jjj',
	'search' => $search,
	'exclude' => $exclude,
	'postedToday' => false,
	'sleep' => 2, # array( 10, 120 ),
	'debug' => true
);

// parse jobs

$parser = new Craigslist\Parser( $args );

#$cityCodes = Craigslist\Database::getInstance()->getCityCodesByCountry( 'US' );
$cityCodes = Craigslist\Database::getInstance()->getCityCodesByState( 'CO' );

$parser->parseByCodes( $cityCodes );



// parse gigs
$args['category'] = 'cpg';
unset( $args['exclude']);

$parser->editConfig( $args );
$parser->parseByCodes( $cityCodes );






ob_end_flush();
$numPosts = Craigslist\Database::getInstance()->total_query_count;
$time = microtime(true) - $time_start;
echo sprintf( 'Execution time: <b>%s</b> seconds | <b>%s</b> %s found<br />',
	$time,
	$numPosts,
	$numPosts === 1 ? 'post' : 'posts'
);
