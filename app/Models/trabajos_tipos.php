<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class trabajos_tipos extends Model
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
    protected $table = 'trabajos_tipos';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_tipo_trabajo';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'des_tipo_trabajo'
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
     * Get the trabajo for this model.
     *
     * @return App\Models\Trabajo
     */
    public function trabajo()
    {
        return $this->hasOne('App\Models\Trabajo','id_tipo_trabajo','id_tipo_trabajo');
    }



}
