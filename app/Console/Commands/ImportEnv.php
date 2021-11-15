<?php

namespace App\Console\Commands;

use App\Models\Environment;
use App\Models\EnvironmentVariable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportEnv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:import {file} {for?} {--global}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports a .env file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (!($this->option('global') xor $this->argument('for'))) {
            $this->line("Must specify --global or for");
            return Command::FAILURE;
        }
        $for = $this->option('global') ? null : $this->argument('for');
        $file = $this->argument('file');
        if ($for) {
            $envQuery = Environment::where('name', '=', $for);
            if (!$envQuery->exists()) {
                $environment = Environment::create(['name' => $for]);
            } else {
                $environment = $envQuery->first();
            }
        } else {
            $environment = null;
        }
        $fileContents = File::get($file);
        foreach (explode("\n", $fileContents) as $row) {
            if (!$row) {
                continue;
            }
            list($name, $value) = explode('=', $row);
            if ($environment) {
                $q = $environment->environmentVariables()->where('name', '=', $name);
                if ($q->exists()) {
                    $q->update(['value' => $value]);
                } else {
                    $environment->environmentVariables()->create(['name' => $name, 'value' => $value]);
                }
            } else {
                $q = EnvironmentVariable::whereNull('environment_id')
                    ->where('name', '=', $name);
                if ($q->exists()) {
                    $q->update(['value' => $value]);
                } else {
                    EnvironmentVariable::create(['name' => $name, 'value' => $value, 'environment_id' => null]);
                }
            }
        }
        return Command::SUCCESS;
    }
}
