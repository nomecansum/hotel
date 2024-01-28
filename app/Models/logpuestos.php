<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class logpuestos extends Model
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
    protected $table = 'log_cambios_estado';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_log';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'fecha',
                  'id_estado',
                  'id_user',
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
    
    /**
     * Get the EstadosPuesto for this model.
     *
     * @return App\Models\EstadosPuesto
     */
    public function EstadosPuesto()
    {
        return $this->belongsTo('App\Models\EstadosPuesto','id_estado','id_estado');
    }

    /**
     * Get the Puesto for this model.
     *
     * @return App\Models\Puesto
     */
    public function Puesto()
    {
        return $this->belongsTo('App\Models\Puesto','id_puesto','id_puesto');
    }



}
