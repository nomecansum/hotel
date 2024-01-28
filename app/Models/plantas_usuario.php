<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class plantas_usuario extends Model
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
    protected $table = 'plantas_usuario';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_planta';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'id_usuario'
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
     * Get the Planta for this model.
     *
     * @return App\Models\Planta
     */
    public function Planta()
    {
        return $this->belongsTo('App\Models\Planta','id_planta','id_planta');
    }

    /**
     * Get the User for this model.
     *
     * @return App\Models\User
     */
    public function User()
    {
        return $this->belongsTo('App\Models\User','id_usuario','id');
    }



}
