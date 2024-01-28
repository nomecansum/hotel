<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class trabajos_grupos extends Model
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
    protected $table = 'trabajos_grupos';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'key_id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'id_grupo',
                  'id_padre',
                  'id_trabajo',
                  'num_nivel',
                  'num_orden'
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
     * Get the GruposTrabajo for this model.
     *
     * @return App\Models\GruposTrabajo
     */
    public function GruposTrabajo()
    {
        return $this->belongsTo('App\Models\GruposTrabajo','id_grupo','id_grupo');
    }

    /**
     * Get the Trabajo for this model.
     *
     * @return App\Models\Trabajo
     */
    public function Trabajo()
    {
        return $this->belongsTo('App\Models\Trabajo','id_trabajo','id_trabajo');
    }

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
