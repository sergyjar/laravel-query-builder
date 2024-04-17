<?php

namespace Sergyjar\QueryBuilder\Exceptions;

use Exception;

class FilterFieldNotFoundForModelException extends Exception
{
	public function __construct()
	{
		parent::__construct('Filter field not found for model');
	}
}
