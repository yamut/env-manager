<?php

namespace App\Console\Commands;

use App\Models\Environment;
use App\Models\EnvironmentVariable;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class Env extends Command
{
    protected $signature = 'env {name} {for?} {value?} {--delete} {--global}';
    protected $description = 'Manages env settings';

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
        if (!($this->argument('for') xor $this->option('global'))) {
            $this->line("Must specify --global or for");
            return Command::INVALID;
        }
        $value = $this->argument('value');
        $name = $this->argument('name');
        $for = $this->option('global') ? null : $this->argument('for');
        if (!$this->option('global') && !Environment::where('name', '=', $this->argument('for'))->exists()) {
            $environment = Environment::create(['name' => $this->argument('for')]);
        } elseif (!$this->option('global') && Environment::where('name', '=', $this->argument('for'))->exists()) {
            $environment = Environment::where('name', '=', $this->argument('for'))->first();
        } else {
            $environment = null;
        }

        if ($this->option('delete')) {
            $deleted = EnvironmentVariable::where('name', '=', $name)->delete();
            $this->line(
                sprintf(
                    "Deleted %s row(s).",
                    $deleted
                )
            );
            return Command::SUCCESS <=> $deleted;
        }
        $environmentVariable = EnvironmentVariable::where('name', '=', $name)
            ->when(!$for, function (Builder $q) {
                $q->whereNull('environment_id');
            })
            ->when($for, function (Builder $q) use ($environment) {
                $q->where('environment_id', '=', $environment->id);
            })
            ->first();
        if (!$environmentVariable) {
            EnvironmentVariable::create(
                [
                    'name' => $name,
                    'value' => $value,
                    'environment_id' => $environment->id,
                ]
            );
        } else {
            $environmentVariable->update(['value' => $value]);
        }
        return Command::SUCCESS;
    }
}
