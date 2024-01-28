<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class bloqueos extends Model
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
    protected $table = 'bloqueo_programado';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_bloqueo';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'des_motivo',
                  'fec_fin',
                  'fec_inicio',
                  'id_turno',
                  'usu_audit'
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
     * Set the fec_fin.
     *
     * @param  string  $value
     * @return void
     */
    public function setFecFinAttribute($value)
    {
        $this->attributes['fec_fin'] = !empty($value) ? \DateTime::createFromFormat('j/n/Y g:i A', $value) : null;
    }

    /**
     * Set the fec_inicio.
     *
     * @param  string  $value
     * @return void
     */
    public function setFecInicioAttribute($value)
    {
        $this->attributes['fec_inicio'] = !empty($value) ? \DateTime::createFromFormat('j/n/Y g:i A', $value) : null;
    }

    /**
     * Get fec_fin in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getFecFinAttribute($value)
    {
        return \DateTime::createFromFormat($this->getDateFormat(), $value)->format('j/n/Y g:i A');
    }

    /**
     * Get fec_inicio in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getFecInicioAttribute($value)
    {
        return \DateTime::createFromFormat($this->getDateFormat(), $value)->format('j/n/Y g:i A');
    }

}
