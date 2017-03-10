<?php
spl_autoload_register(function ($class) {
	$ignore = array( 'jobs', 'cities' );
	$dir = '/controllers/';
	$className = str_replace( 'Craigslist\\', '', $class );
	if( !in_array( $className, $ignore ) )
		include dirname(__FILE__) . $dir . $className . '.php';
});