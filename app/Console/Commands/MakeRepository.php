<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRepository extends Command
{
    protected $signature = 'make:repository {name}';
    protected $description = 'Create a new repository';

    public function handle()
    {
        $name = $this->argument('name');
        $repositoryName = $name;

        $repositoryPath = app_path("Repositories/{$repositoryName}.php");

        // Create repository file
        $this->createRepository($repositoryPath, $repositoryName);

        $this->info("Repository and Service $repositoryName created successfully!");
    }

    protected function createRepository($path, $name)
    {
        if (!File::exists($path)) {
            $content = $this->getRepositoryStub($name);
            $content = str_replace('{{ $name }}', $name, $content);

            File::put($path, $content);
        }
    }

    protected function getRepositoryStub($name)
    {
        // You can define your own stub or template for repositories
        return File::get(__DIR__ . '/stubs/repository.stub');
    }
}
