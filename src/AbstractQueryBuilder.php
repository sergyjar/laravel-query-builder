<?php

namespace Sergyjar\QueryBuilder;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Sergyjar\QueryBuilder\Contracts\QueryBuilderInterface;
use Sergyjar\QueryBuilder\Exceptions\PaginationIsNotDefinedException;
use Sergyjar\QueryBuilder\Exceptions\QueryBuilderModelNotFoundException;
use Sergyjar\QueryBuilder\Traits\FiltersQuery;
use Sergyjar\QueryBuilder\Traits\PaginationQuery;
use Sergyjar\QueryBuilder\Traits\SortQuery;

abstract class AbstractQueryBuilder implements QueryBuilderInterface
{
	use FiltersQuery, SortQuery, PaginationQuery;

	protected Builder $query;
	protected array $params = [];

	/**
	 * @throws QueryBuilderModelNotFoundException
	 */
	public function __construct($params)
	{
		$this->params = $params;
		$this->query = $this->getQuery();
	}

	abstract protected function getModelClass(): string;

	/**
	 * Sets request filters
	 *
	 * @return $this
	 */
	public function setFilters(): static
	{
		$this->setQueryFilters();

		return $this;
	}

	/**
	 * Sets the query sorting
	 *
	 * @return $this
	 */
	public function setSort(): static
	{
		$this->setQuerySort();

		return $this;
	}

	/**
	 * Adds a request modifier to get a list, including soft deletes
	 *
	 * @return $this
	 */
	public function withTrashed(): static
	{
		$this->query->withTrashed();

		return $this;
	}

	/**
	 * Adds pagination to the request
	 *
	 * @return $this
	 */
	public function setPagination(): static
	{
		$this->setQueryPagination();

		return $this;
	}

	/**
	 * Adding a field restriction to the request
	 *
	 * @return $this
	 */
	public function setSelect(array $select = ['*']): static
	{
		$this->query->select($select);

		return $this;
	}

	/**
	 * Collecting the query result
	 *
	 * @return Collection
	 */
	public function getCollection(): Collection
	{
		return $this->query->get();
	}

	/**
	 * Getting pagination
	 *
	 * @return array
	 * @throws PaginationIsNotDefinedException
	 */
	public function getPagination(): array
	{
		return $this->getQueryPagination();
	}

	/**
	 * Returns the query builder object for the bound model
	 *
	 * @throws QueryBuilderModelNotFoundException
	 */
	private function getQuery(): Builder
	{
		/** @var class-string<Model> $modelClass */
		$modelClass = $this->getBuilderModelClass();

		return $modelClass::query();
	}

	/**
	 * Returns the name of the model class with which the builder is associated
	 *
	 * @return class-string<Model>
	 * @throws QueryBuilderModelNotFoundException
	 */
	private function getBuilderModelClass(): string
	{
		if (!method_exists(static::class, 'getModelClass')
			|| !is_subclass_of($this->getModelClass(), Model::class)
		) {
			throw new QueryBuilderModelNotFoundException();
		}

		return $this->getModelClass();
	}
}
