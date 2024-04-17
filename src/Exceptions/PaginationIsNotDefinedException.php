<?php

namespace Sergyjar\QueryBuilder\Exceptions;

use Exception;

class PaginationIsNotDefinedException extends Exception
{
	public function __construct()
	{
		parent::__construct('Pagination is not defined in the query');
	}
}
