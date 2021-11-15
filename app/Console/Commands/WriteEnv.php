<?php

namespace App\Console\Commands;

use App\Models\Environment;
use App\Models\EnvironmentVariable;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class WriteEnv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:write {for} {--stdout}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Write a .env file for an environment';

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
        $for = $this->argument('for');
        $environmentQuery = Environment::where('name', '=', $for);
        if (!$environmentQuery->exists()) {
            $this->line("$for environment does not exist.");
            return Command::FAILURE;
        }
        $environment = $environmentQuery->first();
        $globalEnvs = EnvironmentVariable::whereNull('environment_id')->get();
        $envVars = EnvironmentVariable::where('environment_id', '=', $environment->id)->get();
        $combined = collect();
        $globalEnvs->each(function (EnvironmentVariable $var) use ($combined) {
            $combined->put($var->name, $var->value);
        });
        $envVars->each(function (EnvironmentVariable $var) use ($combined) {
            // assume that local vars should override global vars
            $combined->put($var->name, $var->value);
        });
        $envFile = $this->buildEnv($combined)->values()->implode("\n") . "\n";
        if ($this->option('stdout')) {
            echo $envFile;
        } else {
            File::put(storage_path(sprintf('app/files/%s.env', $for)), $envFile);
        }
        return Command::SUCCESS;
    }

    private function buildEnv(Collection $variables): Collection
    {
        return $variables->mapWithKeys(fn(string $v, string $k) => [$k => $this->getLine($k, $v)]);
    }

    private function getLine(string $name, string $value): string
    {
        return sprintf("%s=%s", $name, $value);
    }
}
