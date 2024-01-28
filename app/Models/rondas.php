<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class rondas extends Model
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
    protected $table = 'rondas_limpieza';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_ronda';
    public $incrementing = true;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'des_ronda',
                  'fec_ronda',
                  'id_cliente',
                  'user_creado',
                  'tip_ronda'
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
     * Get the User for this model.
     *
     * @return App\Models\User
     */
    public function User()
    {
        return $this->belongsTo('App\Models\User','user_creado','id');
    }

    /**
     * Get the puestosRonda for this model.
     *
     * @return App\Models\PuestosRonda
     */
    public function puestosRonda()
    {
        return $this->hasOne('App\Models\PuestosRonda','num_ronda','id_ronda');
    }



}
