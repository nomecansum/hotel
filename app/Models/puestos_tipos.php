<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class puestos_tipos extends Model
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
    protected $table = 'puestos_tipos';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_tipo_puesto';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'des_tipo_puesto',
                  'mca_fijo',
                  'id_cliente',
                  'val_icono',
                  'val_color',
                  'max_usos',
                  'hora_liberar',
                  'observaciones',
                  'mca_liberar_auto',
                  'abreviatura',
                  'val_tiempo_limpieza',
                  'val_dias_antelacion',
                  'mca_reserva_masiva',
                  'mca_reserva_multiple',
                  'mca_checkin',
                  'mca_publico',
                  'mca_antelacion_obligatoria',
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
     * Get the puestos for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function puestos()
    {
        return $this->hasMany('App\Models\Puesto','id_tipo_puesto','id_tipo_puesto');
    }

    /**
     * Set the hora_liberar.
     *
     * @param  string  $value
     * @return void
     */
    public function setHoraLiberarAttribute($value)
    {
        $this->attributes['hora_liberar'] = $value;
    }

    /**
     * Set the val_tiempo_limpieza.
     *
     * @param  string  $value
     * @return void
     */
    public function setValTiempoLimpiezaAttribute($value)
    {
        $this->attributes['val_tiempo_limpieza'] = $value;
    }

    /**
     * Get hora_liberar in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getHoraLiberarAttribute($value)
    {
        return $value;
    }

    /**
     * Get val_tiempo_limpieza in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getValTiempoLimpiezaAttribute($value)
    {
        return$value;
    }

}
