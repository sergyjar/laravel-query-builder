<?php

namespace Sergyjar\QueryBuilder\Exceptions;

use Exception;

class QueryBuilderModelNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct('Query builder model not found');
    }
}
