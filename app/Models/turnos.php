<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class turnos extends Model
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
    protected $table = 'turnos';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_turno';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'id_cliente',
                  'des_turno',
                  'dias_semana',
                  'fec_inicio',
                  'fec_fin',
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
    

    /**
     * Set the dias_semana.
     *
     * @param  string  $value
     * @return void
     */
    public function setDiasSemanaAttribute($value)
    {
        $this->attributes['dias_semana'] = json_encode($value);
    }



    /**
     * Get dias_semana in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getDiasSemanaAttribute($value)
    {
        return json_decode($value) ?: [];
    }

    /**
     * Get fec_inicio in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getFecInicioAttribute($value)
    {
      return $value;
    }

    /**
     * Get fec_fin in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getFecFinAttribute($value)
    {
       return $value;
    }

}
