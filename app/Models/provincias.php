<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class provincias extends Model
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
    protected $table = 'provincias';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_prov';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'nombre',
                  'nombre_cal',
                  'cod_pais',
                  'cod_region'
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
    



}
