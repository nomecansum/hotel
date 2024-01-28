<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class causas_cierre extends Model
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
    protected $table = 'causas_cierre';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_causa_cierre';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'des_causa',
                  'id_cliente',
                  'mca_fija',
                  'val_color',
                  'val_icono',
                  'mca_default',
                  'id_causa_externo',
                  'mca_aplica'
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



}
