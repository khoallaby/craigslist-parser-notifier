<?php

namespace Craigslist;


class Api extends Database {

	protected $dbInstance;
	protected static $params, $method, $id;


	public function __construct( ) {
		parent::__construct();
	}


	public static function init() {

		#var_dump(self::getInstance()->db);

		$params = str_replace('/api/', '', $_SERVER['REQUEST_URI'] );
		list( $method, $id ) = explode( '/', $params );
		#$this->api_call( $apiParams[0], $apiParams[1] );

		switch( $method ) {
			case 'save':
				self::getInstance()->update( 'jobs', array( 'saved' => 1), array( 'id' => $id) );
				break;

			case 'hide':
				self::getInstance()->update( 'jobs', array( 'hide' => 1), array( 'id' => $id) );
				break;

			case 'search':


				exit;

			default:
				break;



		}
	}


	public static function save() {

	}



}