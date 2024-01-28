<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class salas extends Model
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
    protected $table = 'salas';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_sala';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'id_puesto',
                  'val_capacidad',
                  'mca_proyector',
                  'mca_pantalla',
                  'mca_videoconferencia',
                  'id_cliente',
                  'obs_sala',
                  'mca_manos_libres',
                  'mca_pizarra',
                  'mca_pizarra_digital',
                  'id_externo_salas'
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
     * Get the Puesto for this model.
     *
     * @return App\Models\Puesto
     */
    public function Puesto()
    {
        return $this->belongsTo('App\Models\Puesto','id_puesto','id_puesto');
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



}
