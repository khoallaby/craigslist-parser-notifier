<?php

namespace Craigslist;


class Api extends Database {

	protected $dbInstance;
	public static$limit = 100;
	protected static $params, $method, $id;


	public function __construct( ) {
		parent::__construct();
	}


	public static function init() {

		$api = str_replace('/api/', '', $_SERVER['REQUEST_URI'] );
		$params = explode( '/', $api );

		header('Content-type: application/json');

		switch( $params[0] ) {
			case 'get':
				$limit = isset( $params[1] ) && is_numeric($params[1])? (int)$params[1] : self::$limit;

				$jobs = self::getInstance()->getJobs(
                    array( 'hide' => 0 ),
					array( 'hide' => '=' ),
					$limit
				);

				$jobs = array_map('\Craigslist\WebUI::filterContent', $jobs );
				$json = array(
					'jobs' => $jobs,
					'status' => true
				);

				echo json_encode( $json );
				break;


            case 'favorites':
                $limit = isset( $params[1] ) && is_numeric($params[1])? (int)$params[1] : self::$limit;

                $jobs = self::getInstance()->getJobs(
                    array( 'hide' => 0, 'saved' => 1 ),
                    array( 'hide' => '=', 'saved' => '=' ),
                    $limit,
                    'AND'
                );

                $jobs = array_map('\Craigslist\WebUI::filterContent', $jobs );
                $json = array(
                    'jobs' => $jobs,
                    'status' => true
                );

                echo json_encode( $json );
                break;


            case 'save':
				$job = self::getInstance()->getOne( 'jobs', '*', array( 'id' => $params[1] ), array( 'id' => '=' )  );
				if( !$job )
					self::jsonError( 'Invalid job' );
				$job->saved = $job->saved == 1 ? 0 : 1;

				$response = self::getInstance()->update( 'jobs', array( 'saved' => $job->saved ), array( 'id' => $params[1] ) );
				echo json_encode( array( 'status' => $response, 'job' => $job ) );
				break;


			case 'hide':
				$response = self::getInstance()->update( 'jobs', array( 'hide' => 1), array( 'id' => $params[1] ) );
				echo json_encode( array( 'status' => $response, 'job' => array( 'hide' => 1 ) ) );
				break;


			case 'search':
			    $search = $params[1];
			    $searchQuery = '%' . str_replace(' ', '%', urldecode($search)) . '%';

                if( !isset($search) )
                    return json_encode([]);

                $limit = isset( $params[2] ) && is_numeric($params[2])? (int)$params[2] : self::$limit;

                $jobs = self::getInstance()->getJobs(
                    [
                        'title' => $searchQuery,
                        'description' => $searchQuery
                    ],
                    [
                        'title' => 'LIKE',
                        'description' => 'LIKE'
                    ],
                    $limit,
                    'OR'
                );

                $jobs = array_map('\Craigslist\WebUI::filterContent', $jobs );
                $json = array(
                    'jobs' => $jobs,
                    'status' => true
                );

                echo json_encode( $json );
                break;

			default:
				break;



		}
	}


	public static function save() {

	}



	private static function jsonError( $reason = '' ) {
		$return = array( 'status' => false );
		if( !empty($reason) )
			$return['message'] = $reason;
		die( json_encode( $return ) );
	}



}