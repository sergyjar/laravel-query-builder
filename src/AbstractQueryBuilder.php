<?php

namespace Sergyjar\QueryBuilder;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Sergyjar\QueryBuilder\Contracts\QueryBuilderInterface;
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
     * Устанавливает фильтры запроса
     *
     * @return $this
     */
    public function setFilters(): static
    {
        $this->setQueryFilters();

        return $this;
    }

    /**
     * Устанавливает сортировку запроса
     *
     * @return $this
     */
    public function setSort(): static
    {
        $this->setQuerySort();

        return $this;
    }

    /**
     * Добавляет модификатор запроса на получение списка, включая мягко удалённые
     *
     * @return $this
     */
    public function withTrashed() : static
    {
        $this->query->withTrashed();

        return $this;
    }

    /**
     * Добавляет пагинацию к запросу
     *
     * @return $this
     */
    public function setPagination(): static
    {
        $this->setQueryPagination();

        return $this;
    }

    /**
     * Добавляем ограничение по полям к запросу
     *
     * @return $this
     */
    public function setSelect(array $select = ['*']): static
    {
        $this->query->select($select);

        return $this;
    }

    /**
     * Собираем результат запроса
     *
     * @return Collection
     */
    public function getQueryCollection(): Collection
    {
        return $this->query->get();
    }

    /**
     * Получаем пагинацию
     *
     * @return array
     */
    public function getPagination(): array
    {
        return $this->getQueryPagination();
    }

    /**
     * Возвращает объект билдера запроса для привязанной модели
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
     * Возвращает название класса модели, с которой связан билдер
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
