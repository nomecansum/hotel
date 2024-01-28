<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DispatchJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:dispatch {job} {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch job';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //PAra probar este job desde consola se utiliza    php artisan job:dispatch GeneraInforme <ID_INFORME>
        $class = '\\App\\Jobs\\' . $this->argument('job');
        $class::dispatchNow($this->argument('id'));
    }
}
