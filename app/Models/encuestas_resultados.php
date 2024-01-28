<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class encuestas_resultados extends Model
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
    protected $table = 'encuestas_resultados';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_resultado';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'iid_encuesta',
                  'id_usuario',
                  'fecha',
                  'valor',
                  'comentario'
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
