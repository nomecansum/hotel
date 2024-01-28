<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class config_clientes extends Model
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
    protected $table = 'config_clientes';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_cliente';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'max_dias_reserva',
                  'mca_restringir_usuarios_planta',
                  'mca_limpieza',
                  'mca_checkin',
                  'mca_permitir_anonimo',
                  'theme_name',
                  'mca_reserva_horas',
                  'val_metodo_notificacion',
                  'tam_qr',
                  'modo_visualizacion_reservas',
                  'modo_visualizacion_puestos',
                  'val_layout_incidencias',
                  'num_imagenes_incidencias',
                  'val_campo_puesto_mostrar',
                  'mca_mostrar_nombre_usando',
                  'hora_liberar_puestos',
                  'mca_liberar_puestos_auto',
                  'mca_salas',
                  'min_hora_reservas',
                  'max_hora_reservas',
                  'mca_mostrar_datos_fijos',
                  'mca_requerir_2fa',
                  'mca_permitir_google',
                  'mca_permitir_microsoft',
                  'mca_saml2',
                  'saml2_idp_entityid',
                  'mca_incidencia_scan',
                  'mca_incidencia_reserva',
                  'mca_spotlinker_salas',
                  'config_print_qr',
                  'mca_mail_apertura_incidencia',
                  'mca_tipo_antelacion',
                  'config_print_qr_ferias',
                  'mca_solicitudes',
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
     * Set the hora_liberar_puestos.
     *
     * @param  string  $value
     * @return void
     */
    public function setHoraLiberarPuestosAttribute($value)
    {
        $this->attributes['hora_liberar_puestos'] = !empty($value) ?$value : null;
    }

    /**
     * Get hora_liberar_puestos in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getHoraLiberarPuestosAttribute($value)
    {
        return $value;
    }

}
