<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\tareas;
use App\Models\logtarea;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

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
       // $schedule->command('inspire')
       //Log::debug(debug_print_backtrace());
        //          ->hourly();
        //////////////////////////////////////////
        ///// TAREAS PROGRAMADAS CUCU 360/////////
        //////////////////////////////////////////
        Log::debug(implode('|',$commands));
        $tasks = tareas::where('mca_activa','S')->get();
        $directorio = storage_path().'/tareas/';
        if(!File::exists($directorio)) {
            File::makeDirectory($directorio);
        }
        // Go through each task to dynamically set them up.
        Log::info(Carbon::now()->toDateTimeString().' Inicio de scheduler');
        foreach ($tasks as $task) {
        // Use the scheduler to add the task at its desired frequency
        //log_tarea('Creada tarea '.$task->nom_tarea,$task->cod_tarea);
            
            $comando=$task->signature." ".$task->cod_tarea;  //." --queue=".$task->queue
            $sch=$schedule->command($comando)->runInBackground()->withoutOverlapping(30);  // $sch=$schedule->call($comando); -->Esto es para cuando se llama a una funcion de un controller
        
            $frequency = $task->val_intervalo;
            //Opciones para el intervalo
            switch ($frequency) {
                case 'hourlyAt':
                    $sch->hourlyAt($task->det_minuto);
                    break;
                case 'dailyAt':
                    $sch->dailyAt("'".Carbon::parse($task->det_horaminuto)->format('H:i')."'");
                    break;
                case 'weeklyOn':
                    $sch->weeklyOn($task->det_diasemana,"'".Carbon::parse($task->det_horaminuto)->format('H:i')."'");
                    break;
                case 'monthlyOn':
                    $sch->monthlyOn($task->det_diames,"'".Carbon::parse($task->det_horaminuto)->format('H:i')."'");
                    break;
                case 'lastDayOfMonth':
                    $sch->dailyAt("'".Carbon::parse($task->det_horaminuto)->format('H:i')."'")->when(function () {
                        return \Carbon\Carbon::now()->endOfMonth()->isToday();
                    });
                    break;
                                                                           
                default:
                    $sch->$frequency();
                    break;
            }

            if(isset($task->dias_semana)&&strlen($task->dias_semana)>0){
                $constraints=explode(",",$task->dias_semana);
                foreach($constraints as $cns){
                    $sch->$cns();
                }
            }
               
            $sch->before(function() use ($task) {
                // Task is about to start...
                log_tarea("Inicio de la tarea [".$task->cod_tarea."] ".$task->des_tarea,$task->cod_tarea);
            });

            $sch->after(function() use ($task) {
                // Task is complete...
                //Incorporamos la salida de la tarea en la BDD
                $salida=File::get(storage_path()."/tareas/".$task->cod_tarea.'.txt');
                log_tarea($salida,$task->cod_tarea);
                log_tarea("Fin de la tarea [".$task->cod_tarea."] ".$task->des_tarea,$task->cod_tarea);
                DB::table('cug_tareas_programadas')
                ->where('cod_tarea',$task->cod_tarea)
                ->update([
                    "fec_ult_ejecucion"=>Carbon::now(),
                    "txt_resultado"=>$salida
                ]);
            });
            $sch->sendOutputTo(storage_path()."/tareas/".$task->cod_tarea.'.txt');
            //Log::info('Creada tarea '.$task->des_tarea);
        }
        $colas_informes=['InformesL','InformesM','InformesS'];
         //Procesadores de colas
        $schedule->command('queue:restart')->hourly();
        $schedule->command('queue:work --sleep=3 --timeout=1800 --tries=3 --daemon --queue=high,default,low')->runInBackground()->withoutOverlapping()->everyMinute();
        //Cola generica de informs
        $schedule->command('queue:work --sleep=3 --timeout=1800 --tries=3 --daemon --queue=Informes0')->runInBackground()->withoutOverlapping()->everyMinute();
         foreach($colas_informes as $cola){
            $schedule->command('queue:work --sleep=3 --timeout=1800 --tries=3 --daemon --queue='.$cola)->runInBackground()->withoutOverlapping()->everyMinute();
         }

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        //$this->load(__DIR__.'/Commands');

        require_once base_path('routes/console.php');
    }
}
