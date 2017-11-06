<?php
namespace Gt\Database\Test;

//require_once(__DIR__ . "/../../vendor/autoload.php");

class Helper {

const COUNT_PATH_PROVIDER = 10;

const QUERY_CREATE_TABLE_PERSON = <<<SQL
create table person
(
    id int primary key auto_increment,
    first_name varchar(32) not null,
    last_name varchar(32) not null,
    dob date not null
);
SQL;

const QUERY_CREATE_TABLE_ADDRESS = <<<SQL
create table address
(
    id int primary key auto_increment,
    building varchar(32) not null,
    street varchar(32),
    district varchar(32),
    town varchar(32),
    county varchar(32),
    postcode varchar(8) not null
);
SQL;

const QUERY_CREATE_TABLE_PERSON_HAS_ADDRESS = <<<SQL
create table person_has_address
(
    id_person int,
    id_address int,
    constraint person_has_address__composite primary key (id_person, id_address)
);
SQL;


public static function getTmpDir() {
	return implode("/", [
		sys_get_temp_dir(),
		"phpgt",
		"database",
		uniqid()
	]);
}

public static function deleteDir(string $dir) {
	exec("rm -rf $dir");
}

public static function queryPathExistsProvider() {
	return self::queryPathProvider(true);
}

public static function queryPathNotExistsProvider() {
	return self::queryPathProvider(false);
}

public static function queryPathExtensionNotValidProvider() {
	return self::queryPathProvider(true, null);
}

private static function queryPathProvider(bool $exists, $extension = "sql") {
	$data = [];

	foreach(self::queryCollectionPathProvider(true) as $qcName => $qcData) {
		$queryCollectionPath = $qcData[1];

		if(is_null($extension)) {
			$extension = uniqid();
		}

		$queryName = uniqid("query");
		$filename = "$queryName.$extension";
		$filePath = implode(DIRECTORY_SEPARATOR, [
			$queryCollectionPath,
			$filename,
		]);

		if($exists) {
			touch($filePath);
		}

		$data []= [
			$queryName,
			$queryCollectionPath,
			$filePath,
		];
	}

	return $data;
}

public static function queryCollectionPathExistsProvider() {
	return self::queryCollectionPathProvider(true);
}

public static function queryCollectionPathNotExistsProvider() {
	return self::queryCollectionPathProvider(false);
}

private static function queryCollectionPathProvider(bool $exists) {
	$data = [];

	for($i = 0; $i < self::COUNT_PATH_PROVIDER; ++$i) {
		$name = uniqid();
		$path = self::getTmpDir() . "/query/" . $name;

		if($exists) {
			mkdir($path, 0775, true);
		}

		$data []= [
			$name,
			$path
		];
	}

	return $data;
}

}#