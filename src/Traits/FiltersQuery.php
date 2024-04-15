<?php

namespace Sergyjar\QueryBuilder\Traits;

use Illuminate\Support\Str;

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

	private function addFilter(string $field): void
	{
		$field = Str::camel($field);

		if ($this->hasFilterCallback($field)) {
			[$this, $field]($this->params[$field]);
		} else {
			$this->query->{"where" . $field}($this->params[$field]);
		}
	}

	private function hasFilterCallback(string $field): bool
	{
		return method_exists($this, $field);
	}
}
