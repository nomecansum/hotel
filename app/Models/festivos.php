<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class festivos extends Model
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
    protected $table = 'festivos';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'cod_festivo';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'des_festivo',
                  'val_fecha',
                  'mca_interanual',
                  'cod_cliente',
                  'mca_nacional',
                  'cod_centro',
                  'cod_provincia',
                  'cod_pais',
                  'mca_fijo',
                  'val_color',
                  'cod_region'
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
