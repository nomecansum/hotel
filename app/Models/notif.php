<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class notif extends Model
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
    protected $table = 'notificaciones';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_notificacion';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'fec_notificacion',
                  'id_tipo_notificacion',
                  'id_usuario',
                  'mca_leida',
                  'txt_notificacion',
                  'url_notificacion'
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
     * Get the NotificacionesTipo for this model.
     *
     * @return App\Models\NotificacionesTipo
     */
    public function NotificacionesTipo()
    {
        return $this->belongsTo('App\Models\NotificacionesTipo','id_tipo_notificacion','id_tipo_notificacion');
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
