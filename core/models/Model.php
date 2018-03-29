<?php

namespace Acceptic\Models;

use \PDO;

class Model {

	public $dbh;

	function __construct($dbconfig) {

		$host = $dbconfig['host'];
		$dbname = $dbconfig['dbname'];
		$user = $dbconfig['user'];
		$pass = $dbconfig['pass'];
		$char = $dbconfig['char'];
		$this->dbh = new PDO("mysql:host={$host};dbname={$dbname};charset={$char}", $user, $pass);

	}

}

