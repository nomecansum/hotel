<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class informes_programados extends Model
{
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'informes_programados';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'cod_informe_programado';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'des_informe_programado',
                  'url_informe',
                  'fec_inicio',
                  'val_periodo',
                  'list_usuarios',
                  'cod_usuario',
                  'fec_creacion',
                  'val_parametros',
                  'fec_ult_ejecucion',
                  'val_intervalo',
                  'txt_resultado',
                  'fec_prox_ejecucion',
                  'dia_desde',
                  'dia_hasta',
                  'controller',
                  'cod_cliente'
              ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [];
    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];
    
    /**
     * Get the User for this model.
     *
     * @return App\Models\User
     */
    public function User()
    {
        return $this->belongsTo('App\Models\User','cod_usuario','id');
    }

    /**
     * Get the Cliente for this model.
     *
     * @return App\Models\Cliente
     */
    public function Cliente()
    {
        return $this->belongsTo('App\Models\Cliente','cod_cliente','id_cliente');
    }



}
