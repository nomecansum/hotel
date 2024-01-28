<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class reglas extends Model
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
    protected $table = 'eventos_reglas';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'cod_regla';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'clientes',
                  'cod_cliente',
                  'cod_grupo',
                  'cod_usuario',
                  'fec_fin',
                  'fec_inicio',
                  'fec_prox_ejecucion',
                  'fec_ult_ejecucion',
                  'intervalo',
                  'mca_activa',
                  'nom_comando',
                  'nom_regla',
                  'nomolestar',
                  'tip_nomolestar',
                  'param_comando',
                  'schedule',
                  'timezone'
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
        return $this->belongsTo('App\Models\Cliente','cod_cliente','id_cliente');
    }

    /**
     * Get the eventosAcciones for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function eventosAcciones()
    {
        return $this->hasMany('App\Models\EventosAccione','cod_regla','cod_regla');
    }

    /**
     * Set the param_comando.
     *
     * @param  string  $value
     * @return void
     */
    public function setParamComandoAttribute($value)
    {
        $this->attributes['param_comando'] = json_encode($value);
    }

    /**
     * Set the schedule.
     *
     * @param  string  $value
     * @return void
     */
    public function setScheduleAttribute($value)
    {
        $this->attributes['schedule'] = json_encode($value);
    }

    /**
     * Get param_comando in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getParamComandoAttribute($value)
    {
        return json_decode($value) ?: [];
    }

    /**
     * Get schedule in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getScheduleAttribute($value)
    {
        return json_decode($value) ?: [];
    }

}
