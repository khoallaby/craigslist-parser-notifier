<?php
require_once dirname(__FILE__) . '/../vendor/autoload.php';
require_once dirname(__FILE__) . '/../src/Database.php';
require_once dirname(__FILE__) . '/../src/Parser.php';

function checkUserPass( $user, $pass ) {
	$config = parse_ini_file( dirname(__FILE__) . '/../db.ini', true );
	if( $user == $config['database']['user'] && $pass == $config['database']['password'] )
		return true;
	else
		return false;
}

function importSql( $sqlFile ) {
	$db = \Craigslist\Database::getInstance();
	$file = dirname( __FILE__ ) . '/../src/sql/' . $sqlFile;
	$contents = file_get_contents( $file );
	$query = str_replace( 'cl_', $db->prefix, $contents );
	$mq = mysqli_multi_query( $db->getMysqli(), $query );

	// clears results -- http://php.net/manual/en/mysqli.multi-query.php#110155
	while($db->getMysqli()->more_results() && $db->getMysqli()->next_result()) {
		$extraResult = $db->getMysqli()->use_result();
		if($extraResult instanceof mysqli_result)
			$extraResult->free();
	}
	return $mq;
}


if( isset($_POST['user'], $_POST['pass']) ) {
	if( checkUserPass( $_POST['user'], $_POST['pass'] ) ) {
		$db = new \Craigslist\Database();

		if( isset($_POST['truncate']) ) {
			$db->getMysqliDb()->rawQuery( "TRUNCATE TABLE {$db->prefix}jobs" );
		} elseif( isset($_POST['install']) ) {
			set_time_limit(60*10);
			$db->getMysqliDb()->rawQuery( "DROP TABLE {$db->prefix}jobs" );
			$db->getMysqliDb()->rawQuery( "DROP TABLE {$db->prefix}regions" );
			$db->getMysqliDb()->rawQuery( "DROP TABLE {$db->prefix}states" );
			$db->getMysqliDb()->rawQuery( "DROP TABLE {$db->prefix}cities" );
			if( importSql( 'schema.sql' ) ) {
				while ($db->getMysqli()->more_results()) {;}
				importSql( 'bootstrap.sql' );
			}
		}
		echo $db->getMysqliDb()->getLastQuery();
	}
}

?>

<form action="" method="post">
	<label>User: <input type="text" name="user" value="<?php echo isset( $_POST['user'] ) ? $_POST['user'] : ''; ?>" /> </label><br />
	<label>Pass: <input type="password" name="pass" value="<?php echo isset( $_POST['pass'] ) ? $_POST['pass'] : ''; ?>" /> </label><br />
	<input type="submit" name="truncate" value="Truncate Jobs" />
	<input type="submit" name="install" value="Install Tables" />
</form>

