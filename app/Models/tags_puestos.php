<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tags_puestos extends Model
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
    protected $table = 'tags_puestos';

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
                  'id_puesto'
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
     * Get the Tag for this model.
     *
     * @return App\Models\Tag
     */
    public function Tag()
    {
        return $this->belongsTo('App\Models\Tag','id_tag','id_tag');
    }

    /**
     * Get the Puesto for this model.
     *
     * @return App\Models\Puesto
     */
    public function Puesto()
    {
        return $this->belongsTo('App\Models\Puesto','id_puesto','id_puesto');
    }



}
