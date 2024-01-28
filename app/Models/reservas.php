<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class reservas extends Model
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
    protected $table = 'reservas';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_reserva';
    public $incrementing = true;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'id_cliente',
                  'id_usuario',
                  'id_puesto',
                  'fec_reserva',
                  'fec_fin_reserva',
                  'fec_utilizada'
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
    

    // /**
    //  * Set the fec_reserva.
    //  *
    //  * @param  string  $value
    //  * @return void
    //  */
    // public function setFecReservaAttribute($value)
    // {
    //     $this->attributes['fec_reserva'] = !empty($value) ? \DateTime::createFromFormat('[% date_format %]', $value) : null;
    // }

    // /**
    //  * Get fec_reserva in array format
    //  *
    //  * @param  string  $value
    //  * @return array
    //  */
    // public function getFecReservaAttribute($value)
    // {
    //     return \DateTime::createFromFormat($this->getDateFormat(), $value)->format('j/n/Y g:i A');
    // }

}
