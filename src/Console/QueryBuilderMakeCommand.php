<?php

namespace Sergyjar\QueryBuilder\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use ReflectionClass;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Finder\Finder;
use function Laravel\Prompts\error;

class QueryBuilderMakeCommand extends GeneratorCommand
{
	protected $name = 'query-builder:make';

	protected $description = 'Create a new QueryBuilder class';

	protected array $replace = [];

	protected function getStub(): string
	{
		return __DIR__ . '/stubs/query-builder.stub';
	}

	protected function qualifyClass($name): string
	{
		$this->replace = $this->buildModelReplacements();

		return $this->getQueryBuilderFileName($name);
	}

	protected function buildClass($name): string
	{
		return str_replace(array_keys($this->replace), array_values($this->replace), parent::buildClass($name));
	}

	protected function getDefaultNamespace($rootNamespace): string
	{
		$customPath = $this->option('path');

		if ($customPath) {
			return $rootNamespace . '\\' . str_replace('/', '\\', ltrim($customPath, '\\/'));
		}

		return $rootNamespace . '\QueryBuilders';
	}

	protected function getOptions(): array
	{
		return [
			['path', 'p', InputOption::VALUE_OPTIONAL, 'Указать путь для создания файла'],
		];
	}

	protected function buildModelReplacements(): array
	{
		$modelClass = $this->getNameInput();
		$modelFullName = $this->getModelFullName();

		if (!class_exists($modelClass) && !class_exists($modelFullName)) {
			error($modelClass . ' модель не обнаружена.');
			exit;
		}

		return [
			'{{ model }}' => class_basename($modelClass),
			'{{ modelFullName }}' => $modelFullName,
		];
	}

	private function getModelFullName(): ?string
	{
		$files = Finder::create()
			->in('app')
			->files()
			->name($this->getNameInput() . '.php')
			->filter(fn($file) => $this->filterModelFile($file));

		$file = Arr::first(iterator_to_array($files));

		return $file ? $this->getModelFileNamespace($file->getRelativePath()) : null;
	}

	private function filterModelFile($file): bool
	{
		$class = $this->getModelClassByFilePath($file->getRelativePathName());

		if (!class_exists($class)) {
			return false;
		}

		$reflection = new ReflectionClass($class);

		return $reflection->isSubclassOf(Model::class) && !$reflection->isAbstract();
	}

	private function getModelClassByFilePath(string $path): string
	{
		return sprintf(
			'\%s%s',
			Container::getInstance()->getNamespace(),
			str_replace('/', '\\', substr($path, 0, strrpos($path, '.')))
		);
	}

	private function getModelFileNamespace(string $fileRelativePath): string
	{
		return str_replace('/', '\\',
			$this->rootNamespace() . $fileRelativePath . '/' . $this->getNameInput()
		);
	}

	private function getQueryBuilderFileName(string $name): string
	{
		$name = str_replace('/', '\\', ltrim($name, '\\/'));

		return $this->getDefaultNamespace(trim($this->rootNamespace(), '\\')) . '\\' . $name . 'QueryBuilder';
	}
}

