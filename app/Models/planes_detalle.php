<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class planes_detalle extends Model
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
    protected $table = 'trabajos_planes_detalle';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'key_id';
    public $incrementing = true;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'fec_prox_ejecucion',
                  'fec_ult_ejecucion',
                  'id_contrata',
                  'id_grupo_trabajo',
                  'id_plan',
                  'id_planta',
                  'id_trabajo',
                  'id_zona',
                  'list_operarios',
                  'num_operarios',
                  'val_periodo',
                  'val_tiempo',
                  'mca_activa',
                  'txt_observaciones',
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
     * Get the key for this model.
     *
     * @return App\Models\Key
     */
    public function key()
    {
        return $this->belongsTo('App\Models\Key','key_id');
    }



}
