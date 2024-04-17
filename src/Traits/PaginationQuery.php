<?php

namespace Sergyjar\QueryBuilder\Traits;

use Error;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Sergyjar\QueryBuilder\Exceptions\PaginationIsNotDefinedException;

trait PaginationQuery
{
	protected LengthAwarePaginator $paginator;
	protected string $pageKey;
	protected string $perPageKey;
	protected int $pageDefault;
	protected int $perPageDefault;

	protected function setQueryPagination(): void
	{
		$this->setPaginationKeys();

		$this->paginator = $this->query->paginate(
			$this->getPerPage(),
			['*'],
			$this->pageKey,
			$this->getPage()
		);
	}

	/**
	 * @throws PaginationIsNotDefinedException
	 */
	protected function getQueryPagination(): array
	{
		try {
			return [
				$this->pageKey => $this->getPage(),
				$this->perPageKey => $this->getPerPage(),
				'total' => $this->paginator->total(),
				'totalPages' => $this->getTotalPages(),
			];
		} catch (Error) {
			throw new PaginationIsNotDefinedException();
		}
	}

	private function getPage(): int
	{
		if (array_key_exists($this->pageKey, $this->params)) {
			return (int)$this->params[$this->pageKey];
		}

		return $this->pageDefault ?? config('query-builder.page_default', 1);
	}

	private function getPerPage(): int
	{
		if (array_key_exists($this->perPageKey, $this->params)) {
			return (int)$this->params[$this->perPageKey];
		}

		return $this->perPageDefault ?? config('query-builder.per_page_default', 20);
	}

	private function setPaginationKeys(): void
	{
		$this->pageKey = $this->pageKey ?? config('query-builder.page', 'page');
		$this->perPageKey = $this->perPageKey ?? config('query-builder.per_page', 'perPage');
	}

	private function getTotalPages(): int
	{
		return ceil($this->paginator->total() / $this->paginator->perPage());
	}
}
