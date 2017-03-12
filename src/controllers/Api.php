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

		$api = str_replace('/api/', '', $_SERVER['REQUEST_URI'] );
		$params = explode( '/', $api );
		#$this->api_call( $apiParams[0], $apiParams[1] );

		header('Content-type: application/json');

		switch( $params[0] ) {
			case 'get':
				if( !isset($params[1]) )
					return json_encode([]);

				if( $params[1] == 'jobs' ) {
					$jobs = self::getInstance()->get(
						'jobs',
						'*',
						array( 'hide' => 0 ),
						array( 'hide' => '=' ),
						isset( $params[2] ) ? $params[2] : null
					);

					$jobs = self::getInstance()->getJobs(
						array('hide' => 0),
						array( 'hide' => '=' ),
						5
					);

					$jobs = array_map('\Craigslist\WebUI::filterContent', $jobs );

					echo json_encode( $jobs );

				} else {
					return json_encode([]);
				}


				break;

			case 'save':
				$response = self::getInstance()->update( 'jobs', array( 'saved' => 1), array( 'id' => $params[1] ) );
				if( $response )
					echo json_encode( array( 'status' => true ) );
				else
					echo json_encode( array( 'status' => false ) );
				break;

			case 'hide':
				self::getInstance()->update( 'jobs', array( 'hide' => 1), array( 'id' => $params[1] ) );
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