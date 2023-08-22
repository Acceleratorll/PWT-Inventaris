<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeService extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Create a new service';

    public function handle()
    {
        $name = $this->argument('name');
        $serviceClassName = $name;

        $servicePath = app_path("Services/{$serviceClassName}.php");

        // Create service file
        $this->createService($servicePath, $serviceClassName);

        $this->info("Service $serviceClassName created successfully!");
    }

    protected function createService($path, $name)
    {
        if (!File::exists($path)) {
            $content = $this->getServiceStub($name);
            $content = str_replace('{{ $name }}', $name, $content);

            File::put($path, $content);
        }
    }


    protected function getServiceStub($name)
    {
        return File::get(__DIR__ . '/stubs/service.stub');
    }
}
