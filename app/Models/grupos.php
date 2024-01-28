<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class grupos extends Model
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
    protected $table = 'grupos_trabajos';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_grupo';
    public $incrementing = true;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'des_grupo',
                  'fec_fin',
                  'fec_inicio',
                  'id_cliente',
                  'id_externo',
                  'num_trabajos',
                  'val_color',
                  'val_icono',
                  'val_operarios',
                  'val_tiempo',
                  'val_estructura',
                  'nestedset',
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
     * Get the trabajosGrupo for this model.
     *
     * @return App\Models\TrabajosGrupo
     */
    public function trabajosGrupo()
    {
        return $this->hasOne('App\Models\TrabajosGrupo','id_grupo','id_grupo');
    }


}
