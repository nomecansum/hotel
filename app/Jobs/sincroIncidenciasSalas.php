<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\incidencias;
use App\Models\salas;
use App\Models\clientes;
use App\Models\estados_incidencias;
use App\Models\incidencias_tipos;

class sincroIncidenciasSalas implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $data;
    public $cliente_salas;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data,$cliente_salas)
    {
        $this->data=$data;
        $this->cliente_salas=$cliente_salas;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ///Sincronizamos las salas
        $data=$this->data;
        $cliente_salas=$this->cliente_salas;
        $pendientes=$data->a_incidencias_pendientes;
        $reenviar=[];
        $errores=[];
        foreach($pendientes as $p){
            $obj=new \stdClass();
            $esta=incidencias::where('id_incidencia_salas',$p->incidencia_sala_id)->first();
            if(!isset($esta)){
                try{
                    $r->request->add(['id_puesto' => salas::where('id_cliente',$cliente_salas)->where('id_externo_salas',$p->sala_id)->first()->id_puesto]);
                    $r->request->add(['fec_apertura' => Carbon::parse($r->fecha)]);
                    $r->request->add(['id_usuario_apertura'=>config('app.id_usuario_spotlinker_salas')]);
                    $r->request->add(['id_estado' => estados_incidencias::where('id_cliente',$cliente_salas)->where('id_estado_salas',$p->estado)->first()->id_estado]);
                    $r->request->add(['id_tipo_incidencia' => incidencias_tipos::where('id_cliente',$cliente_salas)->where('id_tipo_salas',$p->tipo_incidencia_id)->first()->id_tipo_incidencia]);
                    $r->request->add(['procedencia' => "salas"]);
                    $r->request->add(['id_incidencia_salas' => $p->incidencia_sala_id]);
                    $r->request->add(['txt_incidencia' => $p->notas_admin]);
                    $r->request->add(['des_incidencia' => $p->descripcion_adicional]);
                    $respuesta=app('App\Http\Controllers\IncidenciasController')->save($r);
                    $obj->incidencia_sala_id=$p->incidencia_sala_id;
                    $obj->incidencia_puestos_id=$respuesta['id'];
                    $reenviar[]=$obj;
                } catch (\Throwable $e) {
                    //dd($e);
                    $obj->incidencia_sala_id=$p->incidencia_sala_id;
                    $obj->sala_id=$p->sala_id;
                    $obj->estado=$p->estado;
                    $obj->tipo_incidencia_id=$p->tipo_incidencia_id;
                    $obj->error=$e->getMessage();
                    $errores[]=$obj;
                }
            }
        }
        //Ahora mandamos la lista de parejas de ID para que las sincronice
        $url="add_incidencias_id_puestos_pendientes";
        $body=json_encode($reenviar);
        $respuesta=enviar_request_salas("POST",$url,"",$body,$cliente_salas);
        if ($respuesta['status'] != 200) {
            throw new \ErrorException($respuesta['error'].' | statusCode: '.$respuesta['status'], $respuesta['status']);
        }
    }
}
