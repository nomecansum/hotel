<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class incidencias extends Model
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
    protected $table = 'incidencias';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_incidencia';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'des_incidencia',
                  'txt_incidencia',
                  'id_usuario_apertura',
                  'id_usuario_cierre',
                  'fec_apertura',
                  'fec_cierre',
                  'id_tipo_incidencia',
                  'img_attach1',
                  'img_attach2',
                  'id_cliente',
                  'id_puesto',
                  'id_causa_cierre',
                  'comentario_cierre',
                  'id_estado',
                  'id_estado_vuelta_puesto',
                  'id_incidencia_externo',
                  'id_incidencia_salas',
                  'url_detalle_incidencia',
                  'val_procedencia',
                  'val_prioridad',
                  'fec_audit'
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
        return $this->belongsTo('App\Models\User','id_usuario_cierre','id');
    }

    /**
     * Get the IncidenciasTipo for this model.
     *
     * @return App\Models\IncidenciasTipo
     */
    public function IncidenciasTipo()
    {
        return $this->belongsTo('App\Models\IncidenciasTipo','id_tipo_incidencia','id_tipo_incidencia');
    }

    /**
     * Get the Cliente for this model.
     *
     * @return App\Models\Cliente
     */
    public function Cliente()
    {
        return $this->belongsTo('App\Models\Cliente','id_cliente','id_cliente');
    }

    /**
     * Get the EstadosIncidencia for this model.
     *
     * @return App\Models\EstadosIncidencia
     */
    public function EstadosIncidencia()
    {
        return $this->belongsTo('App\Models\EstadosIncidencia','id_estado','id_estado');
    }

    /**
     * Get the incidenciasAcciones for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function incidenciasAcciones()
    {
        return $this->hasMany('App\Models\IncidenciasAccione','id_incidencia','id_incidencia');
    }



}
