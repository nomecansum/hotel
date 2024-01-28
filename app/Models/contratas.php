<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class contratas extends Model
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
    protected $table = 'contratas';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_contrata';
    public $incrementing = true;
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'des_contrata',
                  'id_cliente',
                  'img_logo',
                  'val_color'
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
