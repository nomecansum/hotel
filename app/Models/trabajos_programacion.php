<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class trabajos_programacion extends Model
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
    protected $table = 'trabajos_programacion';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_programacion';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'id_plan',
                  'id_trabajo_plan',
                  'fec_programada',
                  'fec_inicio',
                  'fec_fin',
                  'id_operario_inicio',
                  'id_operario_fin',
                  'observaciones',
                  'val_tiempo_estimado',
                  'val_tiempo_empleado'
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
    



}
