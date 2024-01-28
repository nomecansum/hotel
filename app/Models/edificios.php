<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class edificios extends Model
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
    protected $table = 'edificios';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_edificio';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'des_edificio',
                  'id_cliente',
                  'abreviatura',
                  'id_provincia'
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
     * Get the Cliente for this model.
     *
     * @return App\Models\Cliente
     */
    public function Cliente()
    {
        return $this->belongsTo('App\Models\Cliente','id_cliente','id_cliente');
    }

    /**
     * Get the plantas for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function plantas()
    {
        return $this->hasMany('App\Models\Planta','id_edificio','id_edificio');
    }

    /**
     * Get the puestos for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function puestos()
    {
        return $this->hasMany('App\Models\Puesto','id_edificio','id_edificio');
    }



}
