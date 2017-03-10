<?php


$db = Craigslist\Database::getInstance();
$cityCodes = Craigslist\Database::getInstance()->getCityCodesByState( 'CO' );
