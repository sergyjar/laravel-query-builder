<?php

namespace Sergyjar\QueryBuilder\Providers;

use Illuminate\Support\ServiceProvider;
use Sergyjar\QueryBuilder\Console\QueryBuilderMakeCommand;

class QueryBuilderServiceProvider extends ServiceProvider
{
	public function boot()
	{
		if ($this->app->runningInConsole()) {
			$this->publishes([
				__DIR__ . '/../../config/query-builder.php' => config_path('query-builder.php'),
			]);

			$this->commands([
				QueryBuilderMakeCommand::class,
			]);
		}
	}
}
