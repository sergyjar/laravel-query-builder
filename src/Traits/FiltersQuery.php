<?php

namespace Sergyjar\QueryBuilder\Traits;

use Error;
use Illuminate\Support\Str;
use Sergyjar\QueryBuilder\Exceptions\FilterFieldNotFoundForModelException;

trait FiltersQuery
{
	protected array $filterableFields = [];

	protected function setQueryFilters(): void
	{
		foreach ($this->filterableFields as $field) {
			if (isset($this->params[$field])) {
				$this->addFilter($field);
			}
		}
	}

	/**
	 * @throws FilterFieldNotFoundForModelException
	 */
	private function addFilter(string $field): void
	{
		$requestFilter = $this->params[$field];
		$field = Str::camel($field);

		if ($this->hasFilterCallback($field)) {
			[$this, $field]($requestFilter);
		} else {
			try {
				$this->query->{"where" . ucfirst($field)}($requestFilter);
			} catch (Error) {
				throw new FilterFieldNotFoundForModelException();
			}
		}
	}

	private function hasFilterCallback(string $field): bool
	{
		return method_exists($this, $field);
	}
}
