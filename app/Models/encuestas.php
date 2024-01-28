<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class encuestas extends Model
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
    protected $table = 'encuestas';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_encuesta';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'fec_fin',
                  'fec_inicio',
                  'id_cliente',
                  'id_tipo_encuesta',
                  'list_edificios',
                  'list_perfiles',
                  'list_plantas',
                  'list_puestos',
                  'list_tags',
                  'list_tipos',
                  'mca_activa',
                  'mca_anonima',
                  'mca_mostrar_comentarios',
                  'pregunta',
                  'titulo',
                  'token',
                  'val_color',
                  'val_icono',
                  'val_momento',
                  'val_periodo_minimo'
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
     * Get the EncuestasTipo for this model.
     *
     * @return App\Models\EncuestasTipo
     */
    public function EncuestasTipo()
    {
        return $this->belongsTo('App\Models\EncuestasTipo','id_tipo_encuesta','id_tipo_encuesta');
    }



}
