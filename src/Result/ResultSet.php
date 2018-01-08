<?php
namespace Gt\Database\Result;

use Countable;
use PDO;
use Iterator;
use PDOStatement;

/**
 * @property int $length Number of rows represented, synonym of
 * count and getLength
 */
class ResultSet implements Iterator, Countable {
	/** @var \PDOStatement */
	protected $statement;
	/** @var \Gt\Database\Result\Row */
	protected $current_row;
	protected $row_index = null;
	/** @var string */
	protected $insertId = null;
	protected $rowClassName = Row::class;

	public function __construct(PDOStatement $statement, string $insertId = null) {
		$this->statement = $statement;
		$this->insertId = $insertId;
	}

	public function setRowType(string $className):void {
		if(!class_exists($className, true)) {{
			throw new RowSubClassDoesNotExistException($className);
		}}

		$this->rowClassName = $className;
	}

	public function affectedRows():int {
		return $this->statement->rowCount();
	}

	public function lastInsertId():string {
		return $this->insertId;
	}

	public function fetch():?Row {
		$data = $this->statement->fetch(
			PDO::FETCH_ASSOC
		);

		if(is_null($this->row_index)) {
			$this->row_index = 0;
		}
		else {
			$this->row_index++;
		}

		if(empty($data)) {
			$this->current_row = null;
		}
		else {
			$this->setDataTypes($data, $this->rowClassName);
			$this->current_row = new $this->rowClassName($data);
		}

		return $this->current_row;
	}

	/**
	 * @return Row[]
	 */
	public function fetchAll():array {
		$data = [];

		while($row = $this->fetch()) {
			$data [] = $row;
		}

		return $data;
	}

	protected function fetchUpToIteratorIndex() {
		while(is_null($this->row_index)
			|| $this->row_index < $this->iterator_index) {
			$this->fetch();
		}
	}

// Iterator ////////////////////////////////////////////////////////////////////
	protected $iterator_index = 0;

	public function rewind():void {
		$this->statement->execute();
		$this->current_row = null;
		$this->row_index = null;
		$this->iterator_index = 0;
	}

	public function current():?Row {
		$this->fetchUpToIteratorIndex();

		return $this->current_row;
	}

	public function key():int {
		return $this->iterator_index;
	}

	public function next():void {
		$this->iterator_index++;
	}

	public function valid():bool {
		return !empty($this->current());
	}

	public function count():int {
		$this->rewind();

		return count($this->fetchAll());
	}
}
