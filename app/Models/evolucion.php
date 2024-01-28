<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class evolucion extends Model
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
    protected $table = 'eventos_evolucion';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_evolucion';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'cod_regla',
                  'data1',
                  'data2',
                  'data3',
                  'data4',
                  'data5',
                  'fec_inicio',
                  'fec_iteracion',
                  'mca_fin',
                  'val_iteracion'
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
     * Set the data1.
     *
     * @param  string  $value
     * @return void
     */
    public function setData1Attribute($value)
    {
        $this->attributes['data1'] = json_encode($value);
    }

    /**
     * Set the data2.
     *
     * @param  string  $value
     * @return void
     */
    public function setData2Attribute($value)
    {
        $this->attributes['data2'] = json_encode($value);
    }

    /**
     * Set the data3.
     *
     * @param  string  $value
     * @return void
     */
    public function setData3Attribute($value)
    {
        $this->attributes['data3'] = json_encode($value);
    }

    /**
     * Set the data4.
     *
     * @param  string  $value
     * @return void
     */
    public function setData4Attribute($value)
    {
        $this->attributes['data4'] = json_encode($value);
    }

    /**
     * Set the data5.
     *
     * @param  string  $value
     * @return void
     */
    public function setData5Attribute($value)
    {
        $this->attributes['data5'] = json_encode($value);
    }

    /**
     * Get data1 in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getData1Attribute($value)
    {
        return json_decode($value) ?: [];
    }

    /**
     * Get data2 in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getData2Attribute($value)
    {
        return json_decode($value) ?: [];
    }

    /**
     * Get data3 in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getData3Attribute($value)
    {
        return json_decode($value) ?: [];
    }

    /**
     * Get data4 in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getData4Attribute($value)
    {
        return json_decode($value) ?: [];
    }

    /**
     * Get data5 in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getData5Attribute($value)
    {
        return json_decode($value) ?: [];
    }

}
