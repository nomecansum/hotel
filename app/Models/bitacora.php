<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class bitacora extends Model
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
    protected $table = 'bitacora';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_bitacora';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'accion',
                  'fecha',
                  'id_modulo',
                  'id_seccion',
                  'id_usuario',
                  'status'
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
     * Set the fecha.
     *
     * @param  string  $value
     * @return void
     */
    public function setFechaAttribute($value)
    {
        $this->attributes['fecha'] = !empty($value) ? \DateTime::createFromFormat('[% date_format %]', $value) : null;
    }

    /**
     * Get fecha in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getFechaAttribute($value)
    {
        return \DateTime::createFromFormat($this->getDateFormat(), $value)->format('j/n/Y g:i A');
    }

}
