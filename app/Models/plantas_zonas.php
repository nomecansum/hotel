<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class plantas_zonas extends Model
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
    protected $table = 'plantas_zonas';

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
                  'id_planta',
                  'num_zona',
                  'des_zona',
                  'val_alto',
                  'val_ancho',
                  'val_x',
                  'val_y'
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
