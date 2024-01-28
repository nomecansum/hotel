<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class bloqueo_puestos extends Model
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
    protected $table = 'bloqueo_puestos';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_bloqueo';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'id_puesto'
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
