<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class turnos_usuarios extends Model
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
    protected $table = 'turnos_usuarios';

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
                  'id_turno',
                  'id_usuario',
                  'fec_audit'
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
     * Get the key for this model.
     *
     * @return App\Models\Key
     */
    public function key()
    {
        return $this->belongsTo('App\Models\Key','key_id');
    }

    /**
     * Get the Turno for this model.
     *
     * @return App\Models\Turno
     */
    public function Turno()
    {
        return $this->belongsTo('App\Models\Turno','id_turno','id_turno');
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

    /**
     * Set the fec_audit.
     *
     * @param  string  $value
     * @return void
     */
    public function setFecAuditAttribute($value)
    {
        $this->attributes['fec_audit'] = !empty($value) ? \DateTime::createFromFormat('[% date_format %]', $value) : null;
    }

    /**
     * Get fec_audit in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getFecAuditAttribute($value)
    {
        return \DateTime::createFromFormat($this->getDateFormat(), $value)->format('j/n/Y g:i A');
    }

}
