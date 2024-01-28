<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class puestos_ronda extends Model
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
    protected $table = 'puestos_ronda';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'key_id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'fec_fin',
                  'fec_inicio',
                  'id_puesto',
                  'id_ronda',
                  'user_audit'
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
     * Get the Puesto for this model.
     *
     * @return App\Models\Puesto
     */
    public function Puesto()
    {
        return $this->belongsTo('App\Models\Puesto','id_puesto','id_puesto');
    }

    /**
     * Get the key for this model.
     *
     * @return App\Models\Key
     */
    public function key()
    {
        return $this->belongsTo('App\Models\Key','key_id');
    }

    /**
     * Get the RondasLimpieza for this model.
     *
     * @return App\Models\RondasLimpieza
     */
    public function RondasLimpieza()
    {
        return $this->belongsTo('App\Models\RondasLimpieza','num_ronda','id_ronda');
    }

    /**
     * Get the User for this model.
     *
     * @return App\Models\User
     */
    public function User()
    {
        return $this->belongsTo('App\Models\User','user_audit','id');
    }



}
