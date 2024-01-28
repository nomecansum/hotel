<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class limpiadores extends Model
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
    protected $table = 'limpiadores_ronda';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_limpiador';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'id_ronda',
                  'id_limpiador'
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
