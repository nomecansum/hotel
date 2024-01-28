<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class estados_incidencias extends Model
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
    protected $table = 'estados_incidencias';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_estado';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'des_estado',
                  'id_cliente',
                  'val_icono',
                  'val_color',
                  'mca_fijo',
                  'mca_cierre',
                  'id_estado_salas',
                  'id_estado_externo',
                  'mca_defecto',
                  'mca_aplica'
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
     * Get the Cliente for this model.
     *
     * @return App\Models\Cliente
     */
    public function Cliente()
    {
        return $this->belongsTo('App\Models\Cliente','id_cliente','id_cliente');
    }

    /**
     * Get the incidencias for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function incidencias()
    {
        return $this->hasMany('App\Models\Incidencia','id_estado','id_estado');
    }

    /**
     * Get the incidenciasTipos for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function incidenciasTipos()
    {
        return $this->hasMany('App\Models\IncidenciasTipo','id_estado_inicial','id_estado');
    }



}
