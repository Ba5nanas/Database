<?php

namespace Gt\Database\Result;

class EmptyResultSetException extends \Gt\Database\Exception
{
    public function __construct()
    {
        parent::__construct("Attempted to access row when there were no results");
    }
}
