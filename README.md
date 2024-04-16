# sergyjar/laravel-query-builder
[Package on Github](https://github.com/sergyjar/laravel-query-builder) |
[Package on Packagist](https://packagist.org/packages/sergyjar/laravel-query-builder)

A simple way to obtain a model collection taking into account filtering, sorting and pagination.
> **Note**: This package requires **PHP 8.3+** and **Laravel/Eloquent 10+**.
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

**1. Filtration**

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

**2. Sorting**

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