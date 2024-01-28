<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\informes_programados;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\usuarios;

class GeneraInforme implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $id_informe;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id_informe)
    {
        //
        $this->id_informe=$id_informe;
        //log::info($this->id_informe);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
        //Las posibilidaddes de log son:  error    warning  debug    info   critical   notice    alert
    //PAra probar este job desde consola se utiliza    php artisan job:dispatch GeneraInforme 6
    public function handle()
    {
        //Aumentamos el tiempo maximo de timeout, porque los informes pueden tardar
        ini_set('max_execution_time', 500);
        set_time_limit(500);

        //Y la memoria disponibie para ejecucion
        ini_set('memory_limit', '4095M');

        Log::warning('Iniciando proceso del informe ');
        $id=$this->id_informe;
        try{
            try{
                $informe=informes_programados::findorfail($id);
            } catch (\Exception $e){
                Log::error('No se ha encontrado el informe '.$id);
                exit();
            }

            Log::debug('Encontrado informe ['.$id.'] '.$informe->des_informe_programado." tipo ".$informe->url_informe);
            $param=json_decode($informe->val_parametros);
            //Montamos un request para poder llamar al controlador del informe tal cual lo haria la pagina
            $r = new Request();
            foreach($param as $key => $value){
                //$r->request->add([$key => $value]);
                $r->query->add([$key => $value]);
            }
            $r->query->add(['cod_usuario' => $informe->cod_usuario]);
            $r->query->add(['email_schedule' => 1]);

            //Ahora calculamos las fechas en funcion de la opcion de fechas seleccionada
            switch($informe->val_periodo){
                case 1://Ayer
                    $f1=Carbon::Now()->subDay();
                    $f2=Carbon::Now()->subDay();
                break;
                case 2://La semana pasada
                    $f1=Carbon::Now()->subWeek()->startOfWeek(Carbon::MONDAY);
                    $f2=Carbon::Now()->subWeek()->endOfWeek(Carbon::SUNDAY);
                break;
                case 3://L-V de la semana pasada
                    $f1=Carbon::Now()->subWeek()->startOfWeek(Carbon::MONDAY);
                    $f2=Carbon::Now()->subWeek()->endOfWeek(Carbon::FRIDAY);
                break;
                case 4://Los ultimos 10 dias
                    $f1=Carbon::Now()->subDays(11);
                    $f2=Carbon::Now()->subDay();
                break;
                case 5://La ultima quincena
                    $f1=Carbon::Now()->startOfMonth();
                    $f2=Carbon::Now()->startOfMonth()->addDays(14);
                break;
                case 6://El ultimo mes
                    $f1=Carbon::Now()->subMonth()->startOfMonth();
                    $f2=Carbon::Now()->subMonth()->endOfMonth();
                break;
                case 7://El ultimo trimestre
                    $f1=Carbon::Now()->subQuarter()->firstOfQuarter();
                    $f2=Carbon::Now()->subQuarter()->lastOfQuarter();
                break;
                case 8://El ultimo semestre
                    $f1=Carbon::Now()->subQuarter(2)->firstOfQuarter();
                    $f2=Carbon::Now()->subQuarter()->lastOfQuarter();
                break;
                case 9://El ultimo año
                    $f1=Carbon::Now()->subYear()->startOfYear();
                    $f2=Carbon::Now()->subYear()->endOfYear();
                break;
                case 10://Personalizar periodo
                    $f1=Carbon::Now()->subDays($informe->dia_desde);
                    $f2=Carbon::Now()->subDays($informe->dia_hasta);
                break;
                default:
                    $date = explode(" - ",$r->rango);
                    $f1 = adaptar_fecha($date[0]);
                    $f2 = adaptar_fecha($date[1]);
                break;
            }
            $rango=$f1->format('d/m/Y')." - ".$f2->format('d/m/Y');
            Log::notice('Periodo '.$informe->val_periodo." Rango: ".$rango);
            $r['rango'] = $rango;

            Log::debug('Construido request');
            //Por ultimo, obtenemos los email de los destinatarios del correo
            $usuarios=usuarios::find(explode(",",$informe->list_usuarios))->pluck('dir_email')->toArray();
            $r->query->add(['destinatarios' => $usuarios]);
            //Siguiente, calculamos la proxima vez que se ejecutara el informe en base al parametro de val_intervalo

            Log::debug('Intervalo '.$informe->val_intervalo);
            if(strpos($informe->val_intervalo,"Y")!==false){//Anual
                $fec_prox_ejecucion=Carbon::now()->addYear();
            } else if(strpos($informe->val_intervalo,"M")!==false){//Meses
                $intervalo=str_replace("M","",$informe->val_intervalo);
                $fec_prox_ejecucion=Carbon::now()->addMonths($intervalo);

            } else {//Dias
                $fec_prox_ejecucion=Carbon::now()->addDays($informe->val_intervalo);
            }
            try{
                Log::debug('Solicitada ejecucion el informe '.$informe->controller);
                $salida = app()->call(
                    [app(\App\Http\Controllers\ReportsController::class), $informe->controller],
                    ['r' => $r]
                );
                Log::info('Fin de ejecucion el informe '.$informe->controller);
            } catch(\Exception $e){
                Log::error('Ocurrion un error al llamar al controller del informe programado '.$e->getMessage().' '.$e->getTraceAsString());
            }


            $informe->fec_ult_ejecucion=Carbon::now();
            $informe->fec_prox_ejecucion=$fec_prox_ejecucion;
            $informe->save();
            Log::debug('Proxima ejecucion '.$informe->fec_prox_ejecucion->tostring());
            Log::info('Fin de ejecucion de informe programado');
            }
        catch (\Exception $e){
            Log::error('Error, en la ejecucion del informe '.$id." ".$informe->des_informe_programado." ".$e->getMessage()." ".$e->getTraceAsString());
        }

    }
}
