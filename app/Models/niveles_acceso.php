<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class niveles_acceso extends Model
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
    protected $table = 'niveles_acceso';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'cod_nivel';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'val_nivel_acceso',
                  'des_nivel_acceso',
                  'id_cliente',
                  'home_page',
                  'mca_fijo',
                  'mca_reserva_multiple',
                  'mca_liberar_auto',
                  'mca_reservar_sabados',
                  'mca_reservar_domingos',
                  'mca_reservar_festivos',
                  'mca_saltarse_antelacion',
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
     * Get the puestosAsignados for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function puestosAsignados()
    {
        return $this->hasMany('App\Models\PuestosAsignado','id_perfil','cod_nivel');
    }



}
