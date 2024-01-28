<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class trabajos extends Model
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
    protected $table = 'trabajos';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_trabajo';
    public $incrementing = true;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'des_trabajo',
                  'fec_fin',
                  'fec_inicio',
                  'id_cliente',
                  'id_tipo_trabajo',
                  'val_operarios',
                  'val_color',
                  'val_icono',
                  'val_tiempo',
                  'id_externo',
                  'num_nivel',
                  'id_padre'
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
     * Get the TrabajosTipo for this model.
     *
     * @return App\Models\TrabajosTipo
     */
    public function TrabajosTipo()
    {
        return $this->belongsTo('App\Models\TrabajosTipo','id_tipo_trabajo','id_tipo_trabajo');
    }



}
