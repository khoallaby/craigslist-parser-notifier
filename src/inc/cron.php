<?php
$timeStart = microtime(true);
ob_implicit_flush(true);
ob_start();


# city IDs that have over 10 listings
$citiesOver10 = explode( ',', '35,45,252,391,84,44,40,304,68,344,109,93,314,168,57,80,16,228,349,195,283,43,163,88,231,207,34,251,354,102,270,233,213,263,230,55' );


$search = 'wordpress|php|html|css|javascript -"on site" -onsite -"full time" -intern';
$exclude = '12-1-2-1-7-1-2-1-1-19-1-1-1-2-2-1-2-2-2-14-25-25-1-1-1-1-1-1';

$args = array(
	'category' => 'jjj',
	'search' => $search,
	'exclude' => $exclude,
	'postedToday' => false,
	'sleep' => array( 10, 60 ),
	'debug' => true,
	#'proxy' => true
);

// parse jobs

$parser = new Craigslist\Parser( $args );

#$cityCodes = Craigslist\Database::getInstance()->getCityCodesByCountry( 'US' );
#$cityCodes = Craigslist\Database::getInstance()->getCityCodesByState( 'CO' );

$cityCodes = Craigslist\Database::getInstance()->getCityCodesByIds( $citiesOver10 );

$parser->parseByCodes( $cityCodes );



// parse gigs
$args['category'] = 'cpg';
unset( $args['exclude']);

$parser->editConfig( $args );
$parser->parseByCodes( $cityCodes );






ob_end_flush();
$numPosts = Craigslist\Database::getInstance()->total_query_count;
$time = microtime(true) - $timeStart;
echo sprintf( 'Execution time: <b>%s</b> seconds | <b>%s</b> %s found<br />',
	$time,
	$numPosts,
	$numPosts === 1 ? 'post' : 'posts'
);
