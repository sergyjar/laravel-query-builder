# sergyjar/laravel-query-builder
[Package on Github](https://github.com/sergyjar/laravel-query-builder) |
[Package on Packagist](https://packagist.org/packages/sergyjar/laravel-query-builder)

A simple way to obtain a model collection taking into account filtering, sorting and pagination.
> **Note**: This package requires **PHP 8.3+** and **Laravel/Eloquent 10+**.

## Table of Contents
<details><summary>Click to expand</summary><p>

- [Installation](#installation)
- [Usage](#usage)
  - [Command](#command)
  - [ModelQueryBuilder](#modelquerybuilder)
    - [Filtration](#1-filtration)
    - [Sorting](#2-sorting)
  - [Practical use](#practical-use)
- [Defaults](#defaults)
  - [Default Sort](#1-default-sort)
  - [Default pagination](#2-default-pagination)
- [Additional features](#additional-features)
</p></details>

## Installation

You can add this package as a local, per-project dependency to your project using:

```bash
composer require sergyjar/laravel-query-builder
```

After installing the package into your project, you need to publish the configuration 
and register a command that will help you further. To do this, run this command:

```bash
php artisan vendor:publish --provider="Sergyjar\QueryBuilder\Providers\QueryBuilderServiceProvider"
```

This completes the installation phase. Right now you can take advantage of the opportunities.

## Usage

### Command
There are only a couple of steps left to use the builder's capabilities. 
First of all, you need to create a builder for your specific model. 
You can do this yourself, but the easiest way to do it automatically is to run the command:

```
php artisan query-builder:make ModelName
```

This command will automatically find the model with the passed name and create a file that is inherited 
from the base builder.

The file will be created in the standard path: *App/QueryBuilders* with name **ModelNameQueryBuilder**

You can also specify the file creation path by specifying an optional parameter to the command.

```
php artisan query-builder:make ModelName --path Path/For/File
```
or
```
php artisan query-builder:make ModelName -p Path/For/File
```
In both cases, a file will be created in *App/Path/For/File*.

### ModelQueryBuilder

If you used the command, it will create a class like this.
You can also create a similar class yourself.
```php

namespace App\Path\For\File;

use App\Models\ModelName;
use Sergyjar\QueryBuilder\AbstractQueryBuilder;

class ModelNameQueryBuilder extends AbstractQueryBuilder
{
    protected array $filterableFields = [
        //
    ];
    
    protected array $sortMap = [
        //
    ];

    protected function getModelClass(): string
    {
        return ModelName::class;
    }
}
```

#### **1. Filtration**

You can enable filtering by adding fields to the array **$filterableFields**.
Field names can be passed to both camelCase and snake_case, 
the main thing is that the field names match the request fields. 
The builder itself will convert the fields to camelCase and try to call 
a scope like **whereFieldName**, where FieldName is an array element **$filterableFields**.
```php
protected array $filterableFields = [
 'fieldName'
];
```
or
```php
protected array $filterableFields = [
    'field_name'
];
```

If you want to use your custom filter that doesn't match model fields or for some other reason, 
you can define a **method** that will be used instead of trying to call scope. 
In this method, you can define any filtering conditions.

*Example:*
```php
protected array $filterableFields = [
    'status'
];

public function status($value): void
{
    if ($value == 'pending') {
        $this->query->whereStatus($value)
    }
}
```
This completes the filtering setup, and now you can use the filter according to your rules.

#### **2. Sorting**

This builder allows you to define a map of sorting fields, which you need to define in the **$sortMap** array.
```php
    protected array $sortMap = [
        //
    ];
```

Thanks to this approach, you can determine which query fields for sorting will determine the model fields for sorting.
*Example:*
```php
    protected array $sortMap = [
        'status' => 'status_id'
    ];
```
In this example, in the parameters passed to the builder, the **status** field is specified in the sorting key.
Thanks to the map, the builder understands that in this case he should use sorting
by the **status_id** field of the model.

### Practical use

In order to get a filtered, sorted, paginated collection, you only need to create an instance of the desired builder, 
passing it an array with parameters, and then apply the methods we need.

*Example:*
```php
$query = new ModelQueryBuilder($params);

$result = $query
    ->setFilters()
    ->setSort()
    ->setPagination()
    ->getCollection();
```

You can also use the builder interface to create variants in your services.
```php
$query = new ModelQueryBuilder($params);
$collection = $service->getModels($query);
```

*Service:*
```php
use Sergyjar\QueryBuilder\Contracts\QueryBuilderInterface;

public function getModels(QueryBuilderInterface $queryBuilder): Collection
{
    return = $queryBuilder
        ->setFilters()
        ->setSort()
        ->setPagination()
        ->getCollection();
}
```

## Defaults

#### **1. Default Sort**

By default, when adding a sorting request (**->setSort()**), the builder searches the array of parameters for the fields
**'sort'** to get the sort field and **'order'** to get the direction.

If elements with these keys are not found in the parameters array, then the default values are used.
These default values, as well as the field and sort direction keys, can be found and overridden in 
***/config/query-builder.php***

```php
'sort_key' => 'sort',
'order_key' => 'order',

'sort_default' => 'id',
'order_default' => 'asc',
```

Also, if you need to ***override*** sort field keys or default values, you can override them in your *ModelQueryBuilder*. 
The builder will primarily focus on your class, and only then on the configuration.

> **Note:** For the correct sorting direction value, you can use the built-in enum of possible sorts **SortDirection**.

*Example:*
```php
namespace App\Path\For\File;

use App\Models\Model;
use Sergyjar\QueryBuilder\AbstractQueryBuilder;
use Sergyjar\QueryBuilder\Enums\SortDirection

class ModelQueryBuilder extends AbstractQueryBuilder
{
	protected string $sortKey = 'customSortKey';
	protected string $orderKey = 'customOrderKey';
	protected string $sortDefault = 'custom_sort_model_field';
	protected string $orderDefault = SortDirection::DESC;
...
```

#### **2. Default pagination**

By default, when adding a request for pagination (**->setPagination()**), the constructor looks for fields
in the array of parameters **'page'** to get the number of the requested page, and 
**'per_page'** to get the required number of records per page.

If elements with these keys are not found in the parameters array, then the default values are used.
These default values, as well as the page number and number of elements per page keys, can be found and overridden in
***/config/query-builder.php***

```php
'page' => 'page',
'per_page' => 'perPage',

'page_default' => 1,
'per_page_default' => 20,
```

Additionally, if you need to ***override*** pagination field keys or default values, 
you can override them in the *ModelQueryBuilder*. The designer will focus on your class first, 
and only then on the configuration.

*Example:*
```php
namespace App\Path\For\File;

use App\Models\Model;
use Sergyjar\QueryBuilder\AbstractQueryBuilder;

class ModelQueryBuilder extends AbstractQueryBuilder
{
	protected string $pageKey = 'customPageKey';
	protected string $perPageKey = 'customPerPageKey';
	protected int $pageDefault = 2;
	protected int $perPageDefault = 3;
...
```

## Additional features

In addition to the basic methods of working with the builder, there are also several 
additional features that can be useful in building your queries.

+ **With Trashed**

By default, the builder does not take into account soft deleted records, but if you want to receive such records too, 
you can add the **withTrashed** modifier to your request.

```php
$query->withTrashed()
```

> **Important:** If you use *pagination* and *withTrashed* in your request, 
> then the *withTrashed* modifier must come **before** the *pagination*!
> Else you will get *incorrect* pagination values.

+ **Select**

You can limit the model record fields that the query will return. To do this, simply add the *->setSelect()* 
modifier to your request, passing as an argument an array of keys to be retrieved.

```php
$query->setSelect($select)
```

+ **Get Pagination**

Often you need to add a pagination array to the response to further work with the list of records. 
This is easy to do by calling the query modifier *->getPagination();* this method will return an
**array** of pagination field values.

```php
$pagination = $query->getPagination();
```

*Will return example:*

```php
[
    'page' => 20,
    'perPage' => 1,
    'total' => 12,
    'totalPages' => 15,
]
```

