<?php
namespace Gt\Database\Query;

use Gt\Database\Connection\DefaultSettings;
use Gt\Database\Connection\Settings;
use Gt\Database\Migration\Migrator;
use PHPUnit\Framework\TestCase;

class MigratorTest extends TestCase {
	protected $baseDir;

	protected $settings;
	protected $path;
	protected $tableName = "_migration";

	public function setUp() {
		$this->baseDir = sys_get_temp_dir() . "/Gt/Database/Test/" . uniqid();
		$this->settings = new Settings(
			$this->baseDir,
			Settings::DRIVER_SQLITE,
			Settings::DATABASE_IN_MEMORY
		);

		$this->path = $this->baseDir . "/src/query/_migration";
		mkdir($this->path, 0775, true);
	}

	public function tearDown() {
		exec("rm -rf " . $this->baseDir);
	}

	public function testGetMigrationCount() {
		$migrator = new Migrator(
			$this->settings,
			$this->path,
			$this->tableName
		);
		var_dump($migrator);die();
	}
}