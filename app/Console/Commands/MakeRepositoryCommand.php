<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class MakeRepositoryCommand extends GeneratesContractedClass
{
    protected $name = 'make:repository';

    protected $description = 'Crea un repositorio con su interfaz y registra el binding';

    protected $type = 'Repository';

    protected function suffix(): string
    {
        return 'Repository';
    }

    protected function contractsNamespace(): string
    {
        return 'Contracts\Repositories';
    }

    protected function providerPath(): string
    {
        return 'Providers/RepositoryServiceProvider.php';
    }

    protected function providerProperty(): string
    {
        return 'repositories';
    }

    protected function getStub(): string
    {
        return $this->resolveStubPath('stubs/repository.stub');
    }

    protected function contractStub(): string
    {
        return $this->resolveStubPath('stubs/repository.interface.stub');
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Repositories';
    }

    protected function replacements(string $baseName): array
    {
        $model = $this->option('model') ?: $baseName;
        $model = Str::startsWith($model, 'App\\') ? $model : 'App\Models\\'.str_replace('/', '\\', $model);

        if (! class_exists($model)) {
            $this->components->warn("El modelo [$model] no existe todavía. Créelo o use --model.");
        }

        return [
            '{{ model }}' => $model,
            '{{ modelName }}' => class_basename($model),
        ];
    }

    protected function getOptions(): array
    {
        return array_merge(parent::getOptions(), [
            ['model', 'm', InputOption::VALUE_REQUIRED, 'Modelo del repositorio (por defecto, el mismo nombre en App\Models)'],
        ]);
    }
}
