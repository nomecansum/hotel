<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class notificaciones_tipos extends Model
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
    protected $table = 'notificaciones_tipos';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_tipo_notificacion';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'des_tipo_notificacion',
                  'img_notificacion',
                  'url_base',
                  'val_prioridad',
                  'val_color'
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
     * Get the notificacione for this model.
     *
     * @return App\Models\Modelo
     */
    public function notificacione()
    {
        return $this->hasOne('App\Models\Modelo','id_tipo_notificacion','id_tipo_notificacion');
    }



}
