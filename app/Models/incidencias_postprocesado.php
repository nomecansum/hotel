<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class incidencias_postprocesado extends Model
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
    protected $table = 'incidencias_postprocesado';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_proceso';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'id_estado',
                  'id_tipo_incidencia',
                  'mca_abriente',
                  'mca_api',
                  'mca_implicados',
                  'mca_responsable',
                  'mca_salas',
                  'mca_scan',
                  'mca_web',
                  'param_url',
                  'tip_metodo',
                  'txt_destinos',
                  'val_body',
                  'val_header',
                  'val_momento',
                  'val_respuesta',
                  'val_url'
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
     * Get the IncidenciasTipo for this model.
     *
     * @return App\Models\IncidenciasTipo
     */
    public function IncidenciasTipo()
    {
        return $this->belongsTo('App\Models\IncidenciasTipo','id_tipo_incidencia','id_tipo_incidencia');
    }



}
