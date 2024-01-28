<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tags extends Model
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
    protected $table = 'tags';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_tag';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'id_cliente',
                  'nom_tag'
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
     * Get the tagsPuestos for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function tagsPuestos()
    {
        return $this->hasMany('App\Models\TagsPuesto','id_tag','id_tag');
    }



}
