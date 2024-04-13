<?php

namespace Sergyjar\QueryBuilder\Traits;

use Sergyjar\QueryBuilder\Enums\SortDirection;

trait SortQuery
{
    protected array $sortMap = [];
    protected string $sortKey;
    protected string $orderKey;
    protected string $sortDefault;
    protected string $orderDefault;

    protected function setQuerySort(): void
    {
        $this->setSortKeys();

        $this->query->orderBy($this->getSortField(), $this->getOrder());
    }

    private function setSortKeys(): void
    {
        $this->sortKey = $this->sortKey ?? config('query-builder.sort_key', 'sort');
        $this->orderKey = $this->orderKey ?? config('query-builder.order_key', 'order');
    }

    private function getSortField()
    {
        if ($this->isValidSortParamField()) {
            return $this->sortMap[$this->params[$this->sortKey]];
        }

        return $this->sortDefault ?? config('query-builder.sort_default', 'id');
    }

    private function getOrder()
    {
        if ($this->isValidOrderParam()) {
            return $this->params[$this->orderKey];
        }

        return $this->orderDefault ?? config('query-builder.order_default', SortDirection::ASC);
    }

    private function isValidSortParamField(): bool
    {
        return isset($this->params[$this->sortKey]) && $this->isAllowedSortField();
    }

    private function isValidOrderParam(): bool
    {
        return isset($this->params[$this->orderKey]) && $this->isOrderCorrectFormat();
    }

    private function isAllowedSortField(): bool
    {
        return in_array($this->params[$this->sortKey], $this->sortMap, true);
    }

    private function isOrderCorrectFormat(): bool
    {
        return in_array($this->params[$this->orderKey], [SortDirection::ASC, SortDirection::DESC], true);
    }
}
