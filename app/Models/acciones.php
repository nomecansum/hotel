<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class acciones extends Model
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
    protected $table = 'eventos_acciones';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'cod_accion';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'cod_regla',
                  'nom_accion',
                  'num_orden',
                  'param_accion',
                  'val_iteracion'
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
     * Get the EventosRegla for this model.
     *
     * @return App\Models\EventosRegla
     */
    public function EventosRegla()
    {
        return $this->belongsTo('App\Models\EventosRegla','cod_regla','cod_regla');
    }

    /**
     * Set the param_accion.
     *
     * @param  string  $value
     * @return void
     */
    public function setParamAccionAttribute($value)
    {
        $this->attributes['param_accion'] = json_encode($value);
    }

    /**
     * Get param_accion in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getParamAccionAttribute($value)
    {
        return json_decode($value) ?: [];
    }

}
