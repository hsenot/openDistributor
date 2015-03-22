<?php
	/**
	 * Database Include
	 * Handles all database functions required by the REST web services.
	 */

    // Load credentials for database connection
    include 'credentials.php';

	/**
	 * Return postgres data connection
	 * @return 		object		- adodb data connection
	 */
	function pgConnection() {
		global $db_host,$db_name,$db_port,$db_username,$db_password;
		try {
			// Connect to the database passed in the config variable
			$conn = new PDO ("pgsql:host=".$db_host.";dbname=".$db_name.";port=".$db_port,$db_username,$db_password, array(PDO::ATTR_PERSISTENT => true));
		    return $conn;
		}
		catch (Exception $e) {
			trigger_error("Caught Exception: " . $e->getMessage(), E_USER_ERROR);
		}
	}
?>
