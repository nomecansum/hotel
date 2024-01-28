<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\tareas;
use App\Models\logtarea;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\cucoWEB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Stringable;
use App\Console\Commands\liberarReserva;

class Kernel extends ConsoleKernel
{

    // protected function log_tarea($mensaje){
    //     $log=new logtarea();
    //     $log->txt_log=$mensaje;
    //     $log->fec_log=Carbon::now();
    //     $log->save;

    // }

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected function lista_comandos(){
        $files = File::allFiles(app_path() . '/Console/Commands/');
        $comandos=[];
        foreach($files as $f){
            $comandos[]='App\\Console\\Commands\\'.str_replace(".php","",basename($f));
        }
    }

       //'App\Console\Commands\TaskEjemplo'
    //    'App\Console\Commands\TaskEjemplo',
    protected $commands = [
         //Commands\InformesProgramados::class,
         //Commands\DispatchJob::class,
        // Commands\limpiar_bitacora::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /////////////////////////////////////
        //TAREAS PROGRAMADAS         /////////
        ////////////////////////////////////
        try{
            $tasks = tareas::where('mca_activa', 'S')->get();
            foreach ($tasks as $task)
            {
                // Use the scheduler to add the task at its desired frequency
                //log_tarea('Creada tarea '.$task->nom_tarea,$task->cod_tarea);
                $comando = $task->signature." ".$task->cod_tarea;  //." --queue=".$task->queue
                $sch = $schedule->command($comando)->runInBackground()->withoutOverlapping(60);  // $sch=$schedule->call($comando); -->Esto es para cuando se llama a una funcion de un controller
                //$sch=$schedule->command($comando);
                $frequency = $task->val_intervalo;
                //Opciones para el intervalo
                switch ($frequency) {
                    case 'hourlyAt':
                        $sch->hourlyAt($task->det_minuto);
                        break;
                    case 'dailyAt':
                        $sch->dailyAt(Carbon::parse($task->det_horaminuto)->format('H:i'));
                        break;
                    case 'weeklyOn':
                        $sch->weeklyOn($task->det_diasemana, Carbon::parse($task->det_horaminuto)->format('H:i') );
                        break;
                    case 'monthlyOn':
                        $sch->monthlyOn($task->det_diames, Carbon::parse($task->det_horaminuto)->format('H:i') );
                        break;
                    case 'lastDayOfMonth':
                        $sch->dailyAt( Carbon::parse($task->det_horaminuto)->format('H:i') )->when(function () {
                            return \Carbon\Carbon::now()->endOfMonth()->isToday();
                        });
                        break;
                    default:
                        $sch->$frequency();
                        break;
                }
                //Vemos si tiene algun dia de la semana o todos
                if(isset($task->dias_semana) && ($task->dias_semana != "alldays") && ($task->dias_semana != ""))
                    $sch->{$task->dias_semana}();
                
                $sch
                    ->before(function() use ($task) {
                        log::debug("Inicio de la tarea [".$task->cod_tarea."] " . $task->des_tarea);
                    })
                    ->between(Carbon::parse($task->hora_inicio)->format('H:i'), Carbon::parse($task->hora_fin)->format('H:i'))
                    ->after(function() use ($task) {
                        log::debug("Fin de la tarea php [".$task->cod_tarea."] " . $task->des_tarea);
                    })
                    ->onSuccess(function (Stringable $output) use ($task) {
                        log::debug("Tarea [".$task->cod_tarea."] " . $task->des_tarea . " se ha ejecutado correctamente. Resp: ");
                    })
                    ->onFailure(function (Stringable $output) use ($task) {
                        log_tarea("Se ha producido un error al ejecutar la tarea [".$task->cod_tarea."] " . $task->des_tarea . " Error: " . json_encode($output), $task->cod_tarea);
                    })
                    ->sendOutputTo(storage_path()."/tareas/".$task->cod_tarea.'.txt')
                    ->timezone('Europe/Madrid');
            }
        } catch(\Exception $e){
            log::debug("Error en la tarea programada: " . $e->getMessage());
        }
        

        
        $colas_informes = ['InformesL', 'InformesM', 'InformesS'];
        //Procesadores de colas
        $schedule->command('queue:restart')->hourly();
        $schedule->command('queue:work --sleep=3 --timeout=1800 --tries=3 --daemon --queue=high,default,low')->runInBackground()->withoutOverlapping()->everyMinute();
        //Cola generica de informs
    	$schedule->command('queue:work --sleep=3 --timeout=1800 --tries=3 --daemon --queue=Informes0')->runInBackground()->withoutOverlapping()->everyMinute();
        $schedule->command('queue:work --sleep=3 --timeout=1800 --tries=3 --daemon --queue=salas')->runInBackground()->withoutOverlapping()->everyMinute();
	    foreach($colas_informes as $cola){
	    	$schedule->command('queue:work --sleep=3 --timeout=1800 --tries=3 --daemon --queue='.$cola)->runInBackground()->withoutOverlapping()->everyMinute();
	    }
        //Para purgar los token de acceso caducados
        $schedule->command('passport:purge')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
