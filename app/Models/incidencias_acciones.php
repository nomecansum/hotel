<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class incidencias_acciones extends Model
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
    protected $table = 'incidencias_acciones';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_accion';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'id_incidencia',
                  'des_accion',
                  'id_usuario',
                  'fec_accion',
                  'mca_resuelve',
                  'img_attach1',
                  'img_attach2',
                  'num_accion',
                  'id_estado',
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
     * Get the Incidencia for this model.
     *
     * @return App\Models\Incidencia
     */
    public function Incidencia()
    {
        return $this->belongsTo('App\Models\Incidencia','id_incidencia','id_incidencia');
    }

    /**
     * Get the User for this model.
     *
     * @return App\Models\User
     */
    public function User()
    {
        return $this->belongsTo('App\Models\User','id_usuario','id');
    }



}
