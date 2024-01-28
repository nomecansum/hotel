<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class contactos extends Model
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
    protected $table = 'contactos';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_contacto';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'email',
                  'empresa',
                  'fec_audit',
                  'id_cliente',
                  'id_feria',
                  'id_usuario',
                  'mca_acepto',
                  'mca_enviar',
                  'mensaje',
                  'nombre',
                  'token'
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

    // /**
    //  * Set the fec_audit.
    //  *
    //  * @param  string  $value
    //  * @return void
    //  */
    // public function setFecAuditAttribute($value)
    // {
    //     $this->attributes['fec_audit'] = !empty($value) ? \DateTime::createFromFormat('[% date_format %]', $value) : null;
    // }

    // /**
    //  * Get fec_audit in array format
    //  *
    //  * @param  string  $value
    //  * @return array
    //  */
    // public function getFecAuditAttribute($value)
    // {
    //     return \DateTime::createFromFormat($this->getDateFormat(), $value)->format('j/n/Y g:i A');
    // }

}
