<?php

namespace Sergyjar\QueryBuilder\Traits;

trait FiltersQuery
{
	protected array $filterCallbacks = [];

	protected function setQueryFilters(): void
	{
		foreach ($this->filterCallbacks as $callback) {
			if ($this->isValidFilterCallback($callback)) {
				[$this, $callback]($this->params[$callback]);
			}
		}
	}

	private function isValidFilterCallback(string $callback): bool
	{
		return isset($this->params[$callback]) && method_exists($this, $callback);
	}
}
