<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\usuarios;
use App\Models\salas;
use App\Models\puestos;
use Illuminate\Support\Facades\Log;

class getEstructuraIncidenciasSalas implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $fecha;
    public $cliente_salas;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($fecha,$cliente_salas)
    {
        $this->fecha=$fecha;
        $this->cliente_salas=$cliente_salas;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $fecha=Carbon::parse($this->fecha)->format('Y-m-d').'T'.Carbon::parse($this->fecha)->format('H:i:s');
        $cliente_salas=$this->cliente_salas;
        //Codigo para la resincronizacion de incidencias
       $url="get_estructura_incidencias_empresa_desde_fecha/".$fecha;
       $respuesta=enviar_request_salas("GET",$url,"","",$cliente_salas);
       log::debug($respuesta);
       $respuesta=json_decode($respuesta['body']);

       //Sincronizamos las salas
       $salas_spotlinker=json_decode($respuesta->a_salas);
       $cuenta=0;
       foreach($salas_spotlinker as $sala){
           $esta=puestos::where('salas.id_cliente',$cliente_salas)
                   ->join('salas','salas.id_puesto','puestos.id_puesto')
                   ->where('des_puesto',$sala->nombre)
                   ->wherein('puestos.id_tipo_puesto',config('app.tipo_puesto_sala'))
                   ->first();
           if(isset($esta)){
               $sala_qrclean=salas::where('id_puesto',$esta->id_puesto)->first();
               $sala_qrclean->id_externo_salas=$sala->id;
               $sala_qrclean->save();
               $cuenta++;
           }
       }
    }
}
