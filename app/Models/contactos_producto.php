<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class contactos_producto extends Model
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
    protected $table = 'contactos_producto';

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
                  'comentario',
                  'fec_audit',
                  'id_contacto',
                  'id_producto',
                  'id_usuario_com'
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
