<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class puestos extends Model
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
    protected $table = 'puestos';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_puesto';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'id_edificio',
                  'id_planta',
                  'id_cliente',
                  'cod_puesto',
                  'des_puesto',
                  'id_estado',
                  'val_color',
                  'token',
                  'fec_ult_estado',
                  'val_icono',
                  'id_usuario_usando',
                  'mca_acceso_anonimo',
                  'mca_reservar',
                  'max_horas_reservar',
                  'img_puesto',
                  'id_tipo_puesto',
                  'mca_incidencia',
                  'fec_liberacion_auto',
                  'top',
                  'left',
                  'offset_top',
                  'offset_left',
                  'width',
                  'height',
                  'border',
                  'font',
                  'roundness',
                  'val_dias_antelacion',
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
     * Get the Edificio for this model.
     *
     * @return App\Models\Edificio
     */
    public function Edificio()
    {
        return $this->belongsTo('App\Models\Edificio','id_edificio','id_edificio');
    }

    /**
     * Get the Planta for this model.
     *
     * @return App\Models\Planta
     */
    public function Planta()
    {
        return $this->belongsTo('App\Models\Planta','id_planta','id_planta');
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
     * Get the User for this model.
     *
     * @return App\Models\User
     */
    public function User()
    {
        return $this->belongsTo('App\Models\User','id_usuario_usando','id');
    }

    /**
     * Get the PuestosTipo for this model.
     *
     * @return App\Models\PuestosTipo
     */
    public function PuestosTipo()
    {
        return $this->belongsTo('App\Models\PuestosTipo','id_tipo_puesto','id_tipo_puesto');
    }

    /**
     * Get the puestosRondas for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function puestosRondas()
    {
        return $this->hasMany('App\Models\PuestosRonda','id_puesto','id_puesto');
    }

    /**
     * Get the salas for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function salas()
    {
        return $this->hasMany('App\Models\Sala','id_puesto','id_puesto');
    }

    /**
     * Get the logCambiosEstado for this model.
     *
     * @return App\Models\Logpuesto
     */
    public function logCambiosEstado()
    {
        return $this->hasOne('App\Models\Logpuesto','id_puesto','id_puesto');
    }

    /**
     * Get the puestosAsignado for this model.
     *
     * @return App\Models\PuestosAsignado
     */
    public function puestosAsignado()
    {
        return $this->hasOne('App\Models\PuestosAsignado','id_puesto','id_puesto');
    }

    /**
     * Get the tagsPuestos for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function tagsPuestos()
    {
        return $this->hasMany('App\Models\TagsPuesto','id_puesto','id_puesto');
    }



}
