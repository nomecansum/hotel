<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ferias extends Model
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
    protected $table = 'ferias';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_feria';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'des_feria',
                  'fec_feria',
                  'id_cliente'
              ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['fec_feria'];
    
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



}
