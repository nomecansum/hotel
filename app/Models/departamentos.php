<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class departamentos extends Model
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
    protected $table = 'departamentos';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'cod_departamento';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'cod_centro',
                  'id_cliente',
                  'cod_departamento_padre',
                  'nom_departamento',
                  'num_nivel',
                  'sync_at',
                  'id_externo',
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
        return $this->belongsTo('App\Models\Cliente','cod_cliente','id_cliente');
    }



}