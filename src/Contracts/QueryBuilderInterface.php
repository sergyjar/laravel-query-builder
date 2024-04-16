<?php

namespace Sergyjar\QueryBuilder\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface QueryBuilderInterface
{
	public function setFilters(): static;

	public function setSort(): static;

	public function withTrashed(): static;

	public function setPagination(): static;

	public function setSelect(array $select = ['*']): static;

	public function getCollection(): Collection;

	public function getPagination(): array;
}
