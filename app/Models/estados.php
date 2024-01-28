<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class estados extends Model
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
    protected $table = 'estados_puestos';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_estado';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'des_estado',
                  'val_color'
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
     * Get the logCambiosEstados for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function logCambiosEstados()
    {
        return $this->hasMany('App\Models\Logpuesto','id_estado','id_estado');
    }



}
