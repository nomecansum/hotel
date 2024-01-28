<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class planes extends Model
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
    protected $table = 'trabajos_planes';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_plan';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'des_plan',
                  'id_cliente',
                  'id_externo',
                  'mca_activo',
                  'val_color',
                  'val_icono',
                  'num_dias_programar',
                  'id_edificio',
                  'espacios',
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
