<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class plantas extends Model
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
    protected $table = 'plantas';

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
                  'abreviatura',
                  'des_planta',
                  'factor_letra',
                  'factor_puestob',
                  'factor_puestoh',
                  'factor_puestor',
                  'factor_puestow',
                  'factor_grid',
                  'factor_zoom',
                  'height',
                  'id_cliente',
                  'id_edificio',
                  'img_plano',
                  'num_orden',
                  'posiciones',
                  'width',
                  'zonas',
                  'factor_transp',
                  'mca_publica'
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
     * Get the Edificio for this model.
     *
     * @return App\Models\Edificio
     */
    public function Edificio()
    {
        return $this->belongsTo('App\Models\Edificio','id_edificio','id_edificio');
    }

    /**
     * Get the puestos for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function puestos()
    {
        return $this->hasMany('App\Models\Puesto','id_planta','id_planta');
    }

    /**
     * Get the plantasUsuarios for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function plantasUsuarios()
    {
        return $this->hasMany('App\Models\PlantasUsuario','id_planta','id_planta');
    }

    /**
     * Set the zonas.
     *
     * @param  string  $value
     * @return void
     */
    public function setZonasAttribute($value)
    {
        $this->attributes['zonas'] = json_encode($value);
    }

    /**
     * Get zonas in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getZonasAttribute($value)
    {
        return json_decode($value) ?: [];
    }

}
