<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class logtarea extends Model
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
    protected $table = 'tareas_programadas_log';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'cod_log';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'cod_tarea',
                  'fec_log',
                  'txt_log',
                  'tip_mensaje'
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
