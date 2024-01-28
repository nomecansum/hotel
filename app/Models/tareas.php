<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class tareas extends Model
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
    protected $table = 'tareas_programadas';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'cod_tarea';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'des_tarea',
                  'det_diames',
                  'det_diasemana',
                  'det_horaminuto',
                  'det_minuto',
                  'dias_semana',
                  'fec_ult_ejecucion',
                  'hora_fin',
                  'hora_inicio',
                  'nom_comando',
                  'nom_queue',
                  'nom_tarea',
                  'tip_comando',
                  'txt_resultado',
                  'val_color',
                  'val_icono',
                  'val_intervalo',
                  'val_parametros',
                  'val_timeout',
                  'clientes',
                  'usu_audit'
              ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'fec_ult_ejecucion',
    ];
    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];
    

    /**
     * Set the det_horaminuto.
     *
     * @param  string  $value
     * @return void
     */
    public function setDetHoraminutoAttribute($value)
    {
        $this->attributes['det_horaminuto'] = !empty($value) ? Carbon::parse($value) : null;
    }

    /**
     * Set the hora_fin.
     *
     * @param  string  $value
     * @return void
     */
    public function setHoraFinAttribute($value)
    {
        $this->attributes['hora_fin'] = !empty($value) ? Carbon::parse($value) : null;
    }

    /**
     * Set the hora_inicio.
     *
     * @param  string  $value
     * @return void
     */
    public function setHoraInicioAttribute($value)
    {

        $this->attributes['hora_inicio'] = !empty($value) ? Carbon::parse($value) : null;
    }

    /**
     * Set the val_parametros.
     *
     * @param  string  $value
     * @return void
     */
    public function setValParametrosAttribute($value)
    {
        $this->attributes['val_parametros'] = json_encode($value);
    }

    /**
     * Get det_horaminuto in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getDetHoraminutoAttribute($value)
    {
        return $value;
    }

    /**
     * Get hora_fin in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getHoraFinAttribute($value)
    {
        return $value;
    }

    /**
     * Get hora_inicio in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getHoraInicioAttribute($value)
    {
        return $value;
    }

    /**
     * Get val_parametros in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getValParametrosAttribute($value)
    {
        return json_decode($value) ?: [];
    }

}
