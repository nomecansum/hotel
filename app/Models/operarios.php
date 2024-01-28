<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class operarios extends Model
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
    protected $table = 'contratas_operarios';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_operario';
    public $incrementing = true;
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'nom_operario',
                  'id_cliente',
                  'id_contrata',
                  'id_usuario',
                  'val_color',
                  'val_icono'
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
