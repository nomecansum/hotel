<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



//Auth::routes();
////////////////////////// RUTAS PARA LA OPERACION DE USUARIOS SIN LOGIN ////////////////////////////////
//Route::view('/', 'scan');
//Route::post('/getsitio','HomeController@getsitio');
// Route::get('/setqr/{sitio}','HomeController@setqr');
// Route::get('/getqr/{sitio}','HomeController@getqr');

//URL de primer escaneo
Route::get('/puesto/{puesto}','HomeController@scan_puesto');
//Formiulario para pedir el pin cuando se escanea un puesto
Route::post('/id/{puesto}','HomeController@ident_puesto');
//Formulario de menu de opciones de un puesto
Route::get('/form/{puesto}/{pin}','HomeController@getpuesto');

//Formulario de solicitud para un puesto
Route::get('/solicitud/{puesto}/{pin}','IncidenciasController@solicitud_cliente');

//Formulario de incidencia para un puesto
Route::get('/incidencia/{puesto}/{pin}','IncidenciasController@incidencia_cliente');
Route::post('/incidencia/save_clientes','IncidenciasController@save_clientes');

//Listado de incidencias para clientes
Route::get('/incidencia/listado_clientes/{puesto}/{pin}','IncidenciasController@listado_clientes');

//Detalle de una incidencia para clientes
Route::get('/incidencia/det/{puesto}/{pin}/{id}','IncidenciasController@detalle_clientes');

Route::get('/puesto/estado/{puesto}/{estado}','HomeController@estado_puesto');
Route::post('/puesto/estado/{puesto}/{estado}','HomeController@estado_puesto');

Route::view('/prueba_mail','emails.mail_incidencia');
// Route::view('/test','test');
Route::get('/token','PuestosController@generar_token');
Route::get('/pwd_hash/{pwd}','UsersController@pwd_hash');
Route::view('/reminder','auth.passwords.email');
Route::post('/gen_qr','HomeController@gen_qr');

Route::group(['prefix' => 'MKD'], function () {
    Route::get('/plano/{planta}/{token}/{vista?}','MKDController@plano');
    Route::get('/datos_plano/{planta}/{token}','MKDController@datos_plano');
    Route::get('/sala/{sala?}','SalasController@mkd')->where('sala', '[0-9]+');
});  

//Visualizacion de la encuesta
Route::get('/encuestas/get/{token}','EncuestasController@get_encuesta');
Route::post('/encuestas/save_data','EncuestasController@save_data');

 //Cambiar pwd por defecto
 Route::get('/firstlogin','Auth\LoginController@firstlogin')->name('firstlogin');

 //Links del footer
Route::get('/politica','HomeController@politica');
Route::get('/terminos','HomeController@terminos');
Route::get('/cookies','HomeController@cookies');
Route::post('/next_cron','TrabajosController@next_cron');

/////////////////////////////COSAS DE LOGIN///////////////////////////////////////////////////////////
//Mi login de toda la vida
//Route::post('/login','Auth\LoginController@login');

//Login en dos pasos
Route::post('/prelogin', 'Auth\LoginController@prelogin')->name('prelogin');
Route::get('/prelogin',function () {
    return redirect('/login')->withErrors(['email'=>'El usuario no existe o la contraseña no es valida']);
});
Route::get('/logout','Auth\LoginController@logout')->name('logout');;

//Login con google
Route::get('/login/google', 'Auth\LoginController@redirectToGoogleProvider')->name('login.google');
Route::get('/auth/google/callback','Auth\LoginController@authToGooglecallback')->name('login.google.callback');
Route::get('/auth/google/redirect', function () {
    return Socialite::driver('google')->redirect();
});

//Login con Microsofr
Route::get('/login/microsoft', 'Auth\LoginController@redirectToMicrosoftProvider')->name('login.microsoft');
Route::get('/auth/microsoft/callback','Auth\LoginController@authToMicrosoftcallback')->name('login.microsoft.callback');

//Login con SAML2
// //Login con SAML2
// Route::get('/auth/saml_login','Auth\LoginController@saml_login');
// Route::post('/auth/saml_login','Auth\LoginController@saml_login');
// Route::get('/saml2/{uuid}/login','Auth\LoginController@saml_login');


Route::get('/auth/saml_error','Auth\LoginController@saml_error');

Route::get('/auth/saml_logout','Auth\LoginController@saml_logout');
Route::post('/auth/saml_logout','Auth\LoginController@saml_logout');
 
/////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////
//// RUTAS PARA LA LANDING PAGE DE EVENTOS
////
route::view('/welcome','landing.welcome');
Route::group(['prefix' => '/landing'], function () {
    Route::post('save', 'LandingController@save');
    Route::get('products','LandingController@products');
    Route::post('products/save','LandingController@save_product');
    Route::get('/marca/{marca}','LandingController@get_marca');
    Route::get('/scan/{id?}', 'LandingController@scan')->name('landing_scan');
    Route::get('/asoc/{marca}/{persona}','LandingController@save_product2');
    Route::post('/comentario','LandingController@comentario');
});
//////////////////////////////////////////////////////////////

Route::get('/index', 'HomeController@index');

//Tareas programadas
Route::get('/runTask2/{id}/', 'TareasController@ejecutar_tarea_web');

Route::group(['middleware' => 'auth'], function () {

    //mIPERFIL
    Route::get('/miperfil/{id}','UsersController@miperfil')->name('users.miperfil');
    Route::post('miperfil/update/{id}','UsersController@update_perfil')->name('users.update_perfil');
    Route::view('/2fa','auth.2fa_enabler')->name('home.2fa');
    
    // Scan
    Route::get('/scan_usuario', 'HomeController@scan_usuario')->name('main_scan');
    Route::get('/scan_mantenimiento', 'HomeController@scan_mantenimiento')->name('mantenimiento_scan');

    //Check de notificaciones y cambios
    Route::get('/check', 'HomeController@check_notificaciones')->name('check_notificaciones');

    //Relogin
    Route::get("relogin/{id}",['middleware' => 'permissions:["ReLogin"],["R"]', 'uses' => 'UsersController@authwith']);
    Route::get("reback",'UsersController@reback');
    
    //Pagina pricipal
    Route::get('/', 'HomeController@index');
    Route::get('/home', 'HomeController@index');
    Route::view('/scan', 'scan');
    Route::view('/lockscreen','lock');

    //Escaneo de sala
    Route::get('/sala/{sala}','SalasController@getpuesto');

    //Resetear los permisos del superadmin
    Route::get('/reset_admin','HomeController@reset_perfil_admin');
    //Lista todos los iconos del fontawesome para añadirlos en el iconpicker
    Route::get('/regenera_fontawesome','HomeController@regenera_fontawesome');

    //Buscador
    Route::post('/search','HomeController@search')->name('home.search');
    Route::get('/target/{tipo}/{id}/{nombre?}','HomeController@target')->name('home.target');
    
    ////////////////////GESTION DE USUAR IOS////////////////////
    Route::group(['prefix' => 'users'], function () {
        
        Route::get('/',['middleware'=>'permissions:["Usuarios"],["R"]','uses'=>'UsersController@index'])->name('users.index');
        Route::get('/create',['middleware'=>'permissions:["Usuarios"],["C"]','uses'=>'UsersController@create'])->name('users.users.create');
        Route::get('/show/{users}',['middleware'=>'permissions:["Usuarios"],["R"]','uses'=>'UsersController@show'])->name('users.users.show');
        Route::get('/{users}/edit',['middleware'=>'permissions:["Usuarios"],["W"]','uses'=>'UsersController@edit'])->name('users.users.edit');
        Route::get('/edit/{id}',['middleware'=>'permissions:["Usuarios"],["W"]','uses'=>'UsersController@edit'])->name('users.users.edit2');
        Route::get('/plantas/{id}/{check}',['middleware'=>'permissions:["Usuarios"],["W"]','uses'=>'UsersController@plantas_usuario'])->name('users.plantas');
        Route::post('/',['middleware'=>'permissions:["Usuarios"],["W"]','uses'=>'UsersController@store'] )->name('users.users.store');
        Route::post('update/{users}',['middleware'=>'permissions:["Usuarios"],["W"]','uses'=>'UsersController@update'])->name('users.users.update');
        Route::get('/delete/{id}',['middleware'=>'permissions:["Usuarios"],["D"]','uses'=>'UsersController@soft_delete'])->name('users.users.soft_delete');
        Route::get('/destroy/{id}',['middleware'=>'permissions:["Usuarios"],["D"]','uses'=>'UsersController@destroy'])->name('users.users.destroy');
        Route::post('/edit_modificar_usuarios',['middleware'=>'permissions:["Usuarios"],["W"]','uses'=>'UsersController@editor_modificar_usuarios'])->name('users.users.edit_modificar');
        Route::post('/modificar_usuarios',['middleware'=>'permissions:["Usuarios"],["W"]','uses'=>'UsersController@modificar_usuarios'])->name('users.users.modificar_usuarios');
        Route::post('/search',['middleware'=>'permissions:["Usuarios"],["W"]','uses'=>'UsersController@search'])->name('users.users.search');
        Route::get('/activar_2fa/{id}/{accion}',['middleware'=>'permissions:["Usuarios"],["W"]','uses'=>'UsersController@desactivar_2fa'])->name('users.usersactivar_2fa');
        
        Route::get('/addplanta/{usuario}/{planta}',['middleware'=>'permissions:["Usuarios"],["W"]','uses'=>'UsersController@addplanta'])->name('users.addplanta');
        Route::get('/delplanta/{usuario}/{planta}',['middleware'=>'permissions:["Usuarios"],["W"]','uses'=>'UsersController@delplanta'])->name('users.delplanta');

        Route::get('/setdefcamera/{id}','UsersController@setdefcamera');
        Route::get('/setzoom/{id}/{zoom}/{mobile}','UsersController@setzoom');

        Route::post('/borrar_usuarios',['middleware'=>'permissions:["Usuarios"],["D"]','uses'=>'UsersController@borrar_usuarios'])->name('users.users.delete_muchos');
        Route::post('/asignar_plantas',['middleware'=>'permissions:["Usuarios"],["W"]','uses'=>'UsersController@asignar_plantas'])->name('users.users.asignar_plantas');
        Route::post('/asignar_supervisor',['middleware'=>'permissions:["Usuarios"],["W"]','uses'=>'UsersController@asignar_supervisor'])->name('users.users.asignar_supervisor');
        Route::get('/puestos_supervisor/{id}',['middleware'=>'permissions:["Usuarios"],["W"]','uses'=>'UsersController@puestos_supervisor'])->name('users.puestos_supervisor');
        Route::get('/puestos_usuario/{id}',['middleware'=>'permissions:["Usuarios"],["W"]','uses'=>'UsersController@puestos_usuario'])->name('users.puestos_usuario');
        Route::get('/add_puesto_supervisor/{id}/{puesto}/{accion}',['middleware'=>'permissions:["Usuarios"],["W"]','uses'=>'UsersController@add_puesto_supervisor'])->name('users.add_puesto_supervisor');
        Route::post('/asignar_temporal',['middleware'=>'permissions:["Usuarios"],["W"]','uses'=>'UsersController@asignar_temporal'])->name('users.asignar_temporal');
        Route::post('/anular_asignacion_temporal',['middleware'=>'permissions:["Usuarios"],["W"]','uses'=>'UsersController@anular_asignacion_temporal'])->name('users.anular_asignacion_temporal');

        Route::get('/plantas_usuarios',['middleware'=>'permissions:["Plantas usuarios"],["W"]','uses'=>'UsersController@plantas_usuarios'])->name('users.plantas_usuarios');
        Route::get('/puestos_supervisores',['middleware'=>'permissions:["Puestos supervisores"],["W"]','uses'=>'UsersController@puestos_supervisores'])->name('users.puestos_supervisores');

        Route::get('/supervisor_planta/{id}/{planta}/{accion}',['middleware'=>'permissions:["Usuarios"],["W"]','uses'=>'UsersController@supervisor_planta'])->name('users.supervisor_planta');
        Route::get('/supervisor_edificio/{id}/{edificio}/{accion}',['middleware'=>'permissions:["Usuarios"],["W"]','uses'=>'UsersController@supervisor_edificio'])->name('users.supervisor_edificio');

        Route::get('/addtodaplanta/{estado}/{planta}',['middleware'=>'permissions:["Plantas usuarios"],["W"]','uses'=>'UsersController@addtodaplanta'])->name('users.addtodaplanta');
        Route::get('/addtodouser/{estado}/{usuario}',['middleware'=>'permissions:["Plantas usuarios"],["W"]','uses'=>'UsersController@addtodouser'])->name('users.addtodouser');
        Route::get('/gen_token/{id}',['middleware'=>'permissions:["API_TOKEN"],["R"]','uses'=>'APIAuthController@gen_token'])->name('users.api.gen_token');
        Route::get('/gen_password/{id}',['middleware'=>'permissions:["API_TOKEN"],["R"]','uses'=>'UsersController@gen_password'])->name('users.api.gen_password');
        Route::get('/turno/{id}/{turno}/{estado}',['middleware'=>'permissions:["Usuarios"],["W"]','uses'=>'UsersController@turno_usuario'])->name('users.turno_usuario');

        Route::post('/enviar_recordatorio_asignacion',['middleware'=>'permissions:["Usuarios"],["W"]','uses'=>'UsersController@enviar_recordatorio_asignacion'])->name('users.enviar_recordatorio_asignacion');
        

        Route::post('/tema','UsersController@tema_usuario');
        Route::post('/osid','UsersController@osid_usuario');
        
    });

    ////////////////////BITACORA////////////////////
    Route::group(['prefix' => 'bitacoras'], function () {
        Route::get('/', 'BitacorasController@index')->name('bitacoras.bitacora.index');
        Route::post('/search', 'BitacorasController@search')->name('bitacoras.bitacora.search');
        Route::get('/detalle/{id}', 'BitacorasController@ver_entrada')->name('bitacoras.ver_entrada');
    });

    ////////////////////PERFILES////////////////////
    Route::group(['prefix' => 'profiles'], function () {
        Route::get('/',['middleware'=>'permissions:["Perfiles"],["R"]','uses'=>'PermissionsController@profiles']);
        Route::get('/edit/{id}',['middleware'=>'permissions:["Perfiles"],["C"]','uses'=>'PermissionsController@profilesEdit']);
        Route::post('/save',['middleware'=>'permissions:["Perfiles"],["W"]','uses'=>'PermissionsController@profilesSave']);
        Route::post('/update',['middleware'=>'permissions:["Perfiles"],["C"]','uses'=>'PermissionsController@profilesSave']);
        Route::get('/delete/{id}',['middleware'=>'permissions:["Perfiles"],["D"]','uses'=>'PermissionsController@profilesDelete']);
    });

    ////////////////////PERFILES SECCIONES Y PERMISOS////////////////////
    Route::group(['prefix' => 'sections'], function () {
        Route::get('/',['middleware'=>'permissions:["Secciones"],["R"]','uses'=>'PermissionsController@sections']);
        Route::get('/edit/{id}',['middleware'=>'permissions:["Secciones"],["C"]','uses' => 'PermissionsController@sectionsEdit']);
        Route::post('/save',['middleware'=>'permissions:["Secciones"],["W"]','uses' => 'PermissionsController@sectionsSave']);
        Route::post('/update',['middleware'=>'permissions:["Secciones"],["C"]','uses' => 'PermissionsController@sectionsSave']);
        Route::get('/delete/{id}',['middleware'=>'permissions:["Secciones"],["D"]','uses' => 'PermissionsController@sectionsDelete']);
    });

    ////////////////////GESTION DE CLIENTES////////////////////
    Route::group(['prefix' => 'clientes'], function () {
        Route::get('/',['middleware'=>'permissions:["Clientes"],["R"]', 'uses' => 'CustomersController@index'])->name('clientes.index');
        Route::get('create',['middleware'=>'permissions:["Clientes"],["C"]', 'uses' => 'CustomersController@create']);
        Route::post('save',['middleware'=>'permissions:["Clientes"],["W"]', 'uses' => 'CustomersController@save']);
        Route::get('edit/{id}',['middleware'=>'permissions:["Clientes"],["W"]', 'uses' => 'CustomersController@edit']);
        Route::post('update',['middleware'=>'permissions:["Clientes"],["W"]', 'uses' => 'CustomersController@update']);
        Route::get('delete/{id}',['middleware'=>'permissions:["Clientes"],["D"]', 'uses' => 'CustomersController@delete']);
        Route::get('gen_key',['middleware'=>'permissions:["Clientes"],["W"]', 'uses' => 'CustomersController@gen_key']);
        Route::post('session_cliente',['middleware'=>'permissions:["Clientes"],["R"]', 'uses' => 'CustomersController@session_cliente'])->name('cliente.menu');
    });

    ////////////////////GESTION DE PUESTOS////////////////////
    Route::group(['prefix' => 'puestos'], function () {
        Route::get('/',['middleware'=>'permissions:["Puestos"],["R"]', 'uses' => 'PuestosController@index'])->name('puestos.index');
        Route::post('/',['middleware'=>'permissions:["Puestos"],["R"]', 'uses' => 'PuestosController@search']);
	    Route::get('/edit/{id}',['middleware'=>'permissions:["Puestos"],["W"]', 'uses' => 'PuestosController@edit']);
	    Route::post('/update',['middleware'=>'permissions:["Puestos"],["W"]', 'uses' => 'PuestosController@update']);
        Route::get('/delete/{id}',['middleware'=>'permissions:["Puestos"],["D"]', 'uses' => 'PuestosController@delete']);
        Route::get('/ver_puesto/{id}',['middleware'=>'permissions:["Puestos"],["R"]', 'uses' => 'PuestosController@ver_puesto']);
        Route::get('/savesnapshot/{id}',['middleware'=>'permissions:["Puestos"],["R"]', 'uses' => 'PuestosController@savesnapshot']);
        Route::post('/accion_estado',['middleware'=>'permissions:["Puestos"],["W"]', 'uses' => 'PuestosController@accion_estado']);
        Route::Post('/print_qr','PuestosController@print_qr');
        Route::Post('/export_qr','PuestosController@export_qr');
        Route::Post('/preview_qr','PuestosController@preview_qr');
        Route::Post('/save_config_print','PuestosController@save_config_print');

       
        Route::post('/ronda_limpieza',['middleware'=>'permissions:["Rondas de limpieza"],["R"]', 'uses' => 'PuestosController@ronda_limpieza']);
        Route::get('/mapa',['middleware'=>'permissions:["Puestos"],["R"]', 'uses' => 'PuestosController@mapa']);
        Route::get('/plano',['middleware'=>'permissions:["Puestos"],["R"]', 'uses' => 'PuestosController@plano']);
        Route::get('/lista',['middleware'=>'permissions:["Puestos"],["R"]', 'uses' => 'PuestosController@mapa']);
        Route::post('/mapa',['middleware'=>'permissions:["Puestos"],["R"]', 'uses' => 'PuestosController@mapa']);
        Route::post('/plano',['middleware'=>'permissions:["Puestos"],["R"]', 'uses' => 'PuestosController@plano']);
        Route::post('/lista',['middleware'=>'permissions:["Puestos"],["R"]', 'uses' => 'PuestosController@mapa']);
        Route::post('/anonimo',['middleware'=>'permissions:["Puestos"],["W"]', 'uses' => 'PuestosController@cambiar_anonimo']);
        Route::post('/mca_rerserva',['middleware'=>'permissions:["Puestos"],["W"]', 'uses' => 'PuestosController@cambiar_reserva']);
        Route::post('/borrar_puestos',['middleware'=>'permissions:["Puestos"],["D"]', 'uses' => 'PuestosController@borrar_puestos']);
        Route::post('/modificar_puestos',['middleware'=>'permissions:["Puestos"],["W"]', 'uses' => 'PuestosController@modificar_puestos']);
        Route::get('/vmapa/{id}',['middleware'=>'permissions:["Puestos"],["R"]', 'uses' => 'PuestosController@ver_en_mapa']);
        Route::get('/compas',['middleware'=>'permissions:["Compañeros"],["R"]', 'uses' => 'PuestosController@ver_companeros']);
        Route::post('/compas',['middleware'=>'permissions:["Compañeros"],["R"]', 'uses' => 'PuestosController@ver_companeros']);
        Route::get('/save_pos/{id}/{top}/{left}/{ot}/{ol}',['middleware'=>'permissions:["Puestos"],["R"]', 'uses' => 'PuestosController@save_pos']);
        Route::get('/tipos',['middleware'=>'permissions:["Tipos de puesto"],["R"]', 'uses' => 'PuestosController@index_tipos'])->name('puestos_tipos.index');
        Route::post('/tipos/save',['middleware'=>'permissions:["Tipos de puesto"],["W"]', 'uses' => 'PuestosController@tipos_save']);
        Route::get('/tipos/edit/{id?}',['middleware'=>'permissions:["Tipos de puesto"],["C"]', 'uses' => 'PuestosController@tipos_edit']);
        Route::get('/tipos/delete/{id?}',['middleware'=>'permissions:["Tipos de puesto"],["D"]', 'uses' => 'PuestosController@tipos_delete']);
        

    });

    Route::group(['prefix' => 'bloqueo'], function () {
        Route::get('/',['middleware'=>'permissions:["Bloqueo de puestos"],["R"]', 'uses' => 'PuestosController@index_bloqueo'])->name('bloqueos.index');
        Route::post('/save',['middleware'=>'permissions:["Bloqueo de puestos"],["W"]', 'uses' => 'PuestosController@bloqueo_save']);
        Route::get('/edit/{id?}',['middleware'=>'permissions:["Bloqueo de puestos"],["C"]', 'uses' => 'PuestosController@bloqueo_edit']);
        Route::get('/delete/{id?}',['middleware'=>'permissions:["Bloqueo de puestos"],["D"]', 'uses' => 'PuestosController@bloqueo_delete']);
        

    });

    ////////////////////GESTION DE TAGS////////////////////
    Route::group(['prefix' => 'tags'], function () {
        Route::get('/',['middleware'=>'permissions:["Tags"],["R"]', 'uses' => 'TagsController@index'])->name('tags.index');
        Route::post('/save',['middleware'=>'permissions:["Tags"],["W"]', 'uses' => 'TagsController@save']);
        Route::get('/edit/{id?}',['middleware'=>'permissions:["Tags"],["C"]', 'uses' => 'TagsController@edit']);
        Route::get('/delete/{id?}',['middleware'=>'permissions:["Tags"],["D"]', 'uses' => 'TagsController@delete']);
    });

    ////////////////////EDIFICIOS////////////////////
    Route::group(['prefix' => 'edificios'], function () {
        Route::get('/', 'EdificiosController@index')->name('edificios.edificios.index');
        Route::get('/create','EdificiosController@create')->name('edificios.edificios.create');
        Route::get('/show/{edificios}','EdificiosController@show')->name('edificios.edificios.show')->where('id', '[0-9]+');
        Route::get('/edit/{edificios}','EdificiosController@edit')->name('edificios.edificios.edit')->where('id', '[0-9]+');
        Route::post('/', 'EdificiosController@store')->name('edificios.edificios.store');
        Route::put('edificios/{edificios}', 'EdificiosController@update')->name('edificios.edificios.update')->where('id', '[0-9]+');
        Route::get('/delete/{edificios}','EdificiosController@destroy')->name('edificios.edificios.destroy')->where('id', '[0-9]+');
    });

    ////////////////////PLANTAS////////////////////
    Route::group(['prefix' => 'plantas'], function () {
        Route::get('/', 'PlantasController@index')->name('plantas.plantas.index');
        Route::get('/create','PlantasController@create')->name('plantas.plantas.create');
        Route::get('/show/{plantas}','PlantasController@show')->name('plantas.plantas.show')->where('id', '[0-9]+');
        Route::get('/edit/{plantas}','PlantasController@edit')->name('plantas.plantas.edit')->where('id', '[0-9]+');
        Route::post('/', 'PlantasController@store')->name('plantas.plantas.store');
        Route::put('plantas/{plantas}', 'PlantasController@update')->name('plantas.plantas.update')->where('id', '[0-9]+');
        Route::get('/delete/{plantas}','PlantasController@destroy')->name('plantas.plantas.destroy')->where('id', '[0-9]+');
        Route::get('/puestos/{id}','PlantasController@puestos')->name('plantas.puestos.index')->where('id', '[0-9]+');
        Route::post('/puestos','PlantasController@puestos_save')->name('plantas.puestos.save')->where('id', '[0-9]+');
        Route::post('/puestos/custom','PlantasController@puestos_custom')->name('plantas.puestos.custom')->where('id', '[0-9]+');
        Route::post('/puestos/custom_reset','PlantasController@borrar_custom')->name('plantas.puestos.custom_reset')->where('id', '[0-9]+');
        Route::get('/zonas/detalle/{id}','PlantasController@zonas_detalle')->name('plantas.zonas.detalle');
        Route::get('/zonas/{id}','PlantasController@zonas')->name('plantas.zonas.index')->where('id', '[0-9]+');
        Route::post('/save_zonas','PlantasController@save_zonas')->name('plantas.puestos.save_zonas')->where('id', '[0-9]+');
       
    });

    ////////////////////////////   DEPARTAMENTOS   ////////////////////////////////
	Route::group(['prefix' => 'departments'], function () {
	    Route::get('/',['middleware'=>'permissions:["Departamentos"],["R"]', 'uses' => 'DepartmentsController@index'])->name('departamentos.index');
		Route::get('create',['middleware'=>'permissions:["Departamentos"],["W"]', 'uses' => 'DepartmentsController@create']);
		Route::post('save',['middleware'=>'permissions:["Departamentos"],["W"]', 'uses' => 'DepartmentsController@save']);
        Route::get('edit/{id}',['middleware'=>'permissions:["Departamentos"],["C"]', 'uses' => 'DepartmentsController@edit']);
		Route::post('update',['middleware'=>'permissions:["Departamentos"],["C"]', 'uses' => 'DepartmentsController@update']);
		Route::get('delete/{id}',['middleware'=>'permissions:["Departamentos"],["D"]', 'uses' => 'DepartmentsController@delete']);
        Route::get('estructura/{id?}',['middleware'=>'permissions:["Departamentos"],["R"]', 'uses' => 'DepartmentsController@estructura']);
	});

    ////////////////////////////   COLECTIVOS   ////////////////////////////////
    Route::group(['prefix' => 'collective'], function () {
        Route::get('/',['middleware'=>'permissions:["Colectivos"],["R"]', 'uses' => 'CollectiveController@index'])->name('departamentos.index');
        Route::get('create',['middleware'=>'permissions:["Colectivos"],["W"]', 'uses' => 'CollectiveController@edit']);
        Route::post('save',['middleware'=>'permissions:["Colectivos"],["W"]', 'uses' => 'CollectiveController@save']);
        Route::get('edit/{id}',['middleware'=>'permissions:["Colectivos"],["C"]', 'uses' => 'CollectiveController@edit']);
        Route::post('update/{id}',['middleware'=>'permissions:["Colectivos"],["C"]', 'uses' => 'CollectiveController@update']);
        Route::get('delete/{id}',['middleware'=>'permissions:["Colectivos"],["D"]', 'uses' => 'CollectiveController@delete']);
    });

    ////////////////////RONDAS DE LIMPIEZA Y MANTENIMIENTO////////////////////
    Route::group(['prefix' => 'rondas'], function () {
        Route::get('/view/{id}/{print}', 'LimpiezaController@view')->name('rondas.view');
        Route::post('/estado_puesto_ronda', 'LimpiezaController@estado_puesto')->name('rondas.estado_puesto_ronda');
        Route::get('/index/{tipo?}/{f1?}/{f2?}', 'LimpiezaController@index')->name('rondas.index');
        Route::get('/completar_ronda/{id}/{empleado}', 'LimpiezaController@completar_ronda')->name('rondas.completar');
        Route::get('/detallelimp/{id}', 'LimpiezaController@view_limpia')->name('rondas.detalle_limpiador');
        Route::get('/scan', 'LimpiezaController@scan')->name('rondas.estado_puesto');
    });

    Route::group(['prefix' => 'limpieza'], function () {
        Route::get('/pendientes', ['middleware'=>'permissions:["Pendientes limpieza"],["R"]', 'uses' => 'LimpiezaController@pendientes'])->name('limpieza.pendientes');
        //Route::post('/process_import',['middleware'=>'permissions:["Importar datos"],["W"]', 'uses' => 'ImportController@process_import'])->name('import.process_import');
    });

    ////////////////////IMPORTACION DE DATOS////////////////////
    Route::group(['prefix' => 'import'], function () {
        Route::view('/', ['middleware'=>'permissions:["Importar datos"],["R"]', 'uses' => 'import/index'])->name('import.index');
        Route::post('/process_import',['middleware'=>'permissions:["Importar datos"],["W"]', 'uses' => 'ImportController@process_import'])->name('import.process_import');
        Route::get('/import_from_db', ['middleware'=>'permissions:["Importar datos"],["R"]', 'uses' => 'ImportController@import_from_db'])->name('import.import_from_db');
        
    });

    ////////////////////RESERVAS////////////////////
    Route::group(['prefix' => 'reservas'], function () {
        Route::get('/',['middleware'=>'permissions:["Reservas"],["R"]','uses'=>'ReservasController@index']);
        Route::get('/create/{fecha}',['middleware'=>'permissions:["Reservas"],["C"]','uses'=>'ReservasController@create']);
        Route::get('/edit/{id}',['middleware'=>'permissions:["Reservas"],["C"]','uses' => 'ReservasController@edit']);
        Route::post('/save',['middleware'=>'permissions:["Reservas"],["W"]','uses' => 'ReservasController@save']);
        Route::post('/cancelar',['middleware'=>'permissions:["Reservas"],["W"]','uses' => 'ReservasController@delete']);
        Route::post('loadMonthSchedule',['middleware'=>'permissions:["Reservas"],["R"]', 'uses' => 'ReservasController@loadMonthSchedule']);
        Route::post('/comprobar',['middleware'=>'permissions:["Reservas"],["W"]','uses' => 'ReservasController@comprobar_puestos']);
        Route::post('/comprobar_plano',['middleware'=>'permissions:["Reservas"],["W"]','uses' => 'ReservasController@comprobar_plano']);
        Route::get('/puestos_usuario/{id}/{desde}/{hasta}',['middleware'=>'permissions:["Reservas"],["W"]','uses'=>'ReservasController@puestos_usuario'])->name('reservas.puestos_usuario');
        Route::post('/asignar_reserva_multiple',['middleware'=>'permissions:["Reservas"],["W"]','uses' => 'ReservasController@asignar_reserva_multiple']);
        Route::post('/puestos_usuario/{id}/{desde}/{hasta}',['middleware'=>'permissions:["Reservas"],["W"]','uses'=>'ReservasController@puestos_usuario'])->name('reservas.puestos_usuario_post');
        Route::get('/cancelar_puesto/{id}',['middleware'=>'permissions:["Reservas"],["D"]','uses'=>'ReservasController@cancelar_reserva_puesto'])->name('reservas.cancelar_reserva_puesto');
        Route::post('/reservas_multiples_admin',['middleware'=>'permissions:["Reservas"],["W"]','uses'=>'ReservasController@reservas_multiples_admin'])->name('reservas.reservas_multiples_admin');
        Route::get('/slots/{id}/{id_reserva}',['middleware'=>'permissions:["Reservas"],["D"]','uses'=>'ReservasController@slots'])->name('reservas.slots');
    });

    ////////////////////INCIDENCIAS////////////////////
    Route::group(['prefix' => 'incidencias'], function () {
        Route::get('/',['middleware'=>'permissions:["Incidencias"],["R"]','uses'=>'IncidenciasController@index'])->name('incidencias.index');
        Route::post('/',['middleware'=>'permissions:["Incidencias"],["R"]','uses'=>'IncidenciasController@search'])->name('incidencias.search');
        Route::get('/mis_incidencias',['middleware'=>'permissions:["Incidencias > Mis incidencias"],["R"]','uses'=>'IncidenciasController@mis_incidencias'])->name('incidencias.mis_incidencias');
        Route::post('/upload_imagen',['middleware'=>'permissions:["Incidencias"],["C"]', 'uses' => 'IncidenciasController@subir_adjuntos']);
        Route::get('/create/{puesto}/{embed?}','IncidenciasController@nueva_incidencia')->name('incidencias.nueva');
        Route::get('/edit/{id}',['middleware'=>'permissions:["Incidencias"],["W"]','uses' => 'IncidenciasController@edit']);
        Route::post('/save',['middleware'=>'permissions:["Incidencias"],["C"]','uses' => 'IncidenciasController@save']);
        Route::post('/cerrar',['middleware'=>'permissions:["Incidencias"],["W"]','uses' => 'IncidenciasController@cerrar']);
        Route::post('/reabrir',['middleware'=>'permissions:["Incidencias"],["W"]','uses' => 'IncidenciasController@reabrir']);
        Route::get('/form_cierre/{id}',['middleware'=>'permissions:["Incidencias"],["W"]','uses' => 'IncidenciasController@form_cierre']);
        Route::get('/edit/{id}',['middleware'=>'permissions:["Incidencias"],["R"]','uses' => 'IncidenciasController@detalle_incidencia']);
        Route::get('/delete/{id}',['middleware'=>'permissions:["Incidencias"],["D"]','uses' => 'IncidenciasController@delete']);
        Route::get('/get_detalle/{id?}',['middleware'=>'permissions:["Incidencias"],["C"]', 'uses' => 'IncidenciasController@get_detalle_scan']);
        Route::get('/form_accion/{id}',['middleware'=>'permissions:["Incidencias"],["W"]','uses' => 'IncidenciasController@form_accion']);
        Route::post('/accion',['middleware'=>'permissions:["Incidencias"],["W"]','uses' => 'IncidenciasController@add_accion']);
        Route::get('/create/{puesto}','IncidenciasController@nueva_incidencia')->name('incidencias.nueva');
        Route::get('/nueva_incidencia',['middleware'=>'permissions:["Incidencias"],["C"]','uses'=>'IncidenciasController@selector_puestos'])->name('incidencias.nueva_incidencia_blanco');
        Route::get('/show/{id}',['middleware'=>'permissions:["Incidencias"],["R"]','uses'=>'IncidenciasController@show'])->name('incidencias.show');
        //Tipos de incidencia
        Route::get('/tipos',['middleware'=>'permissions:["Tipos de incidencia"],["R"]', 'uses' => 'IncidenciasController@index_tipos'])->name('incidencias_tipos.index');
        Route::post('/tipos/save',['middleware'=>'permissions:["Tipos de incidencia"],["W"]', 'uses' => 'IncidenciasController@tipos_save']);
        Route::get('/tipos/edit/{id?}',['middleware'=>'permissions:["Tipos de incidencia"],["C"]', 'uses' => 'IncidenciasController@tipos_edit']);
        Route::get('/tipos/delete/{id?}',['middleware'=>'permissions:["Tipos de incidencia"],["D"]', 'uses' => 'IncidenciasController@tipos_delete']);
        Route::get('/tipos/postprocesado/{id}/{momento}',['middleware'=>'permissions:["Tipos de incidencia"],["W"]', 'uses' => 'IncidenciasController@edit_postprocesado']);
        Route::get('/tipos/add_procesado/{id}/{momento}',['middleware'=>'permissions:["Tipos de incidencia"],["W"]', 'uses' => 'IncidenciasController@add_postprocesado']);
        Route::get('/tipos/fila_postprocesado/delete/{id}',['middleware'=>'permissions:["Tipos de incidencia"],["W"]', 'uses' => 'IncidenciasController@del_fila_postprocesado']);
        Route::get('/tipos/fila_postprocesado/{id}/{metodo}/{momento}',['middleware'=>'permissions:["Tipos de incidencia"],["W"]', 'uses' => 'IncidenciasController@fila_postprocesado']);
        Route::post('/tipos/postprocesado/save',['middleware'=>'permissions:["Tipos de incidencia"],["W"]', 'uses' => 'IncidenciasController@save_postprocesado']);
        Route::post('/tipos/postprocesado/copiar',['middleware'=>'permissions:["Tipos de incidencia"],["W"]', 'uses' => 'IncidenciasController@copiar_postprocesado']);
        //Causas de cierre
        Route::get('/causas',['middleware'=>'permissions:["Causas de cierre"],["R"]', 'uses' => 'IncidenciasController@index_causas'])->name('incidencias_causas.index');
        Route::post('/causas/save',['middleware'=>'permissions:["Causas de cierre"],["W"]', 'uses' => 'IncidenciasController@causas_save']);
        Route::get('/causas/edit/{id?}',['middleware'=>'permissions:["Causas de cierre"],["W"]', 'uses' => 'IncidenciasController@causas_edit']);
        Route::get('/causas/delete/{id?}',['middleware'=>'permissions:["Causas de cierre"],["D"]', 'uses' => 'IncidenciasController@causas_delete']);
        //Estados de incidencia
        Route::get('/estados',['middleware'=>'permissions:["Estados de incidencia"],["R"]', 'uses' => 'IncidenciasController@index_estados'])->name('incidencias_estados.index');
        Route::post('/estados/save',['middleware'=>'permissions:["Estados de incidencia"],["W"]', 'uses' => 'IncidenciasController@estados_save']);
        Route::get('/estados/edit/{id?}',['middleware'=>'permissions:["Estados de incidencia"],["W"]', 'uses' => 'IncidenciasController@estados_edit']);
        Route::get('/estados/delete/{id?}',['middleware'=>'permissions:["Estados de incidencia"],["D"]', 'uses' => 'IncidenciasController@estados_delete']);
        
    });

    Route::group(['prefix' => 'solicitudes'], function () {
        Route::get('/',['middleware'=>'permissions:["Incidencias"],["R"]','uses'=>'IncidenciasController@index'])->name('solicitudes.index');
        Route::post('/',['middleware'=>'permissions:["Incidencias"],["R"]','uses'=>'IncidenciasController@search'])->name('solicitudes.search');
        Route::get('/mis_solicitudes',['middleware'=>'permissions:["Incidencias > Mis incidencias"],["R"]','uses'=>'IncidenciasController@mis_incidencias'])->name('solicitudes.mis_solicitudes');
        Route::get('/create/{puesto}/{embed?}','IncidenciasController@nueva_incidencia')->name('solicitudes.nueva');
    });

    ////////////////////ENCUESTAS PARA PUESTOS////////////////////
    Route::group(['prefix' => 'encuestas'], function () {
        Route::get('/',['middleware'=>'permissions:["Encuestas"],["R"]', 'uses' => 'EncuestasController@index'])->name('encuestas.index');
        Route::get('create',['middleware'=>'permissions:["Encuestas"],["C"]', 'uses' => 'EncuestasController@create']);
        Route::post('save',['middleware'=>'permissions:["Encuestas"],["W"]', 'uses' => 'EncuestasController@store']);
        Route::get('edit/{id}',['middleware'=>'permissions:["Encuestas"],["W"]', 'uses' => 'EncuestasController@edit']);
        Route::post('update',['middleware'=>'permissions:["Encuestas"],["W"]', 'uses' => 'EncuestasController@update']);
        Route::get('delete/{id}',['middleware'=>'permissions:["Encuestas"],["D"]', 'uses' => 'EncuestasController@delete']);
        Route::get('gen_key',['middleware'=>'permissions:["Encuestas"],["W"]', 'uses' => 'EncuestasController@gen_key']);
        Route::post('resultados',['middleware'=>'permissions:["Encuestas"],["R"]', 'uses' => 'EncuestasController@resultados']);
    });

    ////////////////////GESTION DE CONTACTOS EN FERIAs////////////////////
    Route::group(['prefix' => 'ferias'], function () {
        Route::get('/',['middleware'=>'permissions:["Ferias"],["R"]', 'uses' => 'FeriasController@index'])->name('ferias.index');
        Route::get('create',['middleware'=>'permissions:["Ferias"],["C"]', 'uses' => 'FeriasController@create']);
        Route::post('save',['middleware'=>'permissions:["Ferias"],["W"]', 'uses' => 'FeriasController@store']);
        Route::get('edit/{id}',['middleware'=>'permissions:["Ferias"],["W"]', 'uses' => 'FeriasController@edit']);
        Route::post('update/{id}',['middleware'=>'permissions:["Ferias"],["W"]', 'uses' => 'FeriasController@update']);
        Route::get('delete/{id}',['middleware'=>'permissions:["Ferias"],["D"]', 'uses' => 'FeriasController@delete']);

        Route::get('/marcas',['middleware'=>'permissions:["Ferias"],["R"]', 'uses' => 'FeriasController@marcas_index'])->name('marcas.index');
        Route::get('/marcas/edit/{id}',['middleware'=>'permissions:["Ferias"],["R"]', 'uses' => 'FeriasController@marcas_edit']);
        Route::get('/marcas/delete/{id}',['middleware'=>'permissions:["Ferias"],["D"]', 'uses' => 'FeriasController@marcas_delete']);
        Route::post('/marcas/save',['middleware'=>'permissions:["Ferias"],["W"]', 'uses' => 'FeriasController@marcas_save']);
        Route::post('/marcas/print_qr',['middleware'=>'permissions:["Ferias"],["R"]', 'uses' => 'FeriasController@print_qr_marcas']);
        Route::post('/marcas/export_qr',['middleware'=>'permissions:["Ferias"],["R"]', 'uses' => 'FeriasController@export_qr_marcas']);

        Route::get('/asistentes',['middleware'=>'permissions:["Ferias"],["R"]', 'uses' => 'FeriasController@asistentes_index'])->name('contactos.index');
        Route::post('/asistentes/search',['middleware'=>'permissions:["Ferias"],["R"]', 'uses' => 'FeriasController@asistentes_search'])->name('contactos.search');
        Route::get('/asistentes/edit/{id}',['middleware'=>'permissions:["Ferias"],["R"]', 'uses' => 'FeriasController@asistentes_edit']);
        Route::post('/asistentes/save',['middleware'=>'permissions:["Ferias"],["W"]', 'uses' => 'FeriasController@asistentes_save']);
        Route::get('/asistentes/delete/{id}',['middleware'=>'permissions:["Ferias"],["D"]', 'uses' => 'FeriasController@asistentes_delete']);
        Route::post('/asistentes/print_qr',['middleware'=>'permissions:["Ferias"],["R"]', 'uses' => 'FeriasController@print_qr_asistentes']);
        Route::post('/asistentes/export_qr',['middleware'=>'permissions:["Ferias"],["R"]', 'uses' => 'FeriasController@export_qr_asistentes']);
        Route::Post('/save_config_print','FeriasController@save_config_print');
        Route::Post('/subir_imagen','FeriasController@subir_imagen');
    });

    ////////////////////TAREAS////////////////////
	Route::group(['prefix' => 'tasks'], function () {
	    Route::get('/',['middleware' => 'permissions:["Tareas programadas"],["R"]', 'uses' => 'TareasController@index']);
	    Route::get('/create',['middleware' => 'permissions:["Tareas programadas"],["R"]', 'uses' => 'TareasController@create']);
	    Route::post('/save',['middleware' => 'permissions:["Tareas programadas"],["C"]', 'uses' => 'TareasController@save']);
	    Route::get('/edit/{id}',['middleware' => 'permissions:["Tareas programadas"],["W"]', 'uses' => 'TareasController@edit']);
	    Route::post('/update/{id}',['middleware' => 'permissions:["Tareas programadas"],["W"]', 'uses' => 'TareasController@update']);
		Route::get('/delete/{id}',['middleware' => 'permissions:["Tareas programadas"],["D"]', 'uses' => 'TareasController@delete']);
		Route::post('/param_comando/{id}',['middleware' => 'permissions:["Tareas programadas"],["W"]', 'uses' => 'TareasController@param_comando']);
		Route::get('/detalle/{id}',['middleware' => 'permissions:["Tareas programadas"],["R"]', 'uses' => 'TareasController@detalle_tarea']);
		Route::get('/log_tarea/{id}/{fecha}/{hora}',['middleware' => 'permissions:["Tareas programadas"],["R"]', 'uses' => 'TareasController@ver_log_tarea']);
		Route::get('/cola/{id}/',['middleware' => 'permissions:["Tareas programadas"],["R"]', 'uses' => 'TareasController@ver_cola']);
		Route::get('/runTask/{id}/',['middleware' => 'permissions:["Tareas programadas"],["R"]', 'uses' => 'TareasController@ejecutar_tarea_web']);
		Route::get('/log_tarea/{id}/{fecha}',['middleware' => 'permissions:["Tareas programadas"],["R"]', 'uses' => 'TareasController@log_tarea_web']);
		
	});

    ////////////////////INFORMES////////////////////
	Route::group(['prefix' => 'reports'], function () {
	    Route::get('/users',['middleware' => 'permissions:["Informes > Puestos por usuario"],["R"]', 'uses' => 'ReportsController@users_index']);
	    Route::post('/users/filter',['middleware' => 'permissions:["Informes > Puestos por usuario"],["R"]', 'uses' => 'ReportsController@users']);

        Route::get('/puestos',['middleware' => 'permissions:["Informes > Uso de puestos"],["R"]', 'uses' => 'ReportsController@puestos_index']);
	    Route::post('/puestos/filter',['middleware' => 'permissions:["Informes > Uso de puestos"],["R"]', 'uses' => 'ReportsController@puestos']);

        Route::get('/canceladas',['middleware' => 'permissions:["Informes > Reservas canceladas"],["R"]', 'uses' => 'ReportsController@canceladas_index']);
	    Route::post('/canceladas/filter',['middleware' => 'permissions:["Informes > Reservas canceladas"],["R"]', 'uses' => 'ReportsController@canceladas']);

        Route::get('/ferias',['middleware' => 'permissions:["Informes > Ferias"],["R"]', 'uses' => 'ReportsController@ferias_index']);
	    Route::post('/ferias/filter',['middleware' => 'permissions:["Informes > Ferias"],["R"]', 'uses' => 'ReportsController@ferias']);

        Route::get('/heatmap',['middleware' => 'permissions:["Informes > Uso de espacio"],["R"]', 'uses' => 'ReportsController@heatmap_index']);
	    Route::post('/heatmap/filter',['middleware' => 'permissions:["Informes > Uso de espacio"],["R"]', 'uses' => 'ReportsController@heatmap']);

        Route::get('/trabajos',['middleware' => 'permissions:["Informes > Trabajos planificados"],["R"]', 'uses' => 'ReportsController@trabajos_index']);
	    Route::post('/trabajos/filter',['middleware' => 'permissions:["Informes > Trabajos planificados"],["R"]', 'uses' => 'ReportsController@trabajos']);

        Route::get('/estado_usu',['middleware' => 'permissions:["Informes > Estado usuarios"],["R"]', 'uses' => 'ReportsController@estado_usu_index']);
	    Route::post('/estado_usu/filter',['middleware' => 'permissions:["Informes > Estado usuarios"],["R"]', 'uses' => 'ReportsController@estado_usu']);

        Route::get('/incidencias',['middleware' => 'permissions:["Informes > Incidencias"],["R"]', 'uses' => 'ReportsController@incidencias_index']);
	    Route::post('/incidencias/filter',['middleware' => 'permissions:["Informes > Incidencias"],["R"]', 'uses' => 'ReportsController@incidencias']);

        Route::get('program/{id}', ['middleware' => 'permissions:["Informes programados"],["R"]', 'uses' => 'ReportsController@reportFromEmail']);
		
	});

    ////////////////////////////   INFORMES PROGRAMADOS   ////////////////////////////////
	Route::post('/programar_informe',['middleware' => 'permissions:["Informes programados"],["W"]', 'uses' => 'ReportsController@programar_informe']);
	Route::get('/prog_report',['middleware' => 'permissions:["Informes programados"],["R"]', 'uses' => 'ReportsController@informes_programados_index']);
	Route::get('/prog_report/edit/{id}',['middleware' => 'permissions:["Informes programados"],["W"]', 'uses' => 'ReportsController@edit_informe_programado']);
	Route::post('/prog_report/save',['middleware' => 'permissions:["Informes programados"],["W"]', 'uses' => 'ReportsController@save_informe_programado']);
	Route::get('/prog_report/delete/{id}',['middleware' => 'permissions:["Informes programados"],["D"]', 'uses' => 'ReportsController@delete_informe_programado']);


     ////////////////////GESTION DE SALAS DE REUNIONES////////////////////
    Route::group(['prefix' => 'salas'], function () {
        Route::get('/reservas/{sala?}',['middleware'=>'permissions:["Reservas salas"],["R"]','uses'=>'SalasController@reservas']);
        Route::get('/dia/{fecha?}',['middleware'=>'permissions:["Reservas salas"],["R"]','uses'=>'SalasController@dia']);
        Route::get('/crear_reserva',['middleware'=>'permissions:["Reservas"],["R"]','uses'=>'SalasController@crear_reserva']);
        Route::get('/crear_reserva/sala/{sala}',['middleware'=>'permissions:["Reservas"],["R"]','uses'=>'SalasController@crear_reserva']);
        Route::post('/comprobar',['middleware'=>'permissions:["Reservas"],["R"]','uses'=>'SalasController@comprobar']);
        Route::get('/{sala?}',['middleware'=>'permissions:["Reservas"],["R"]','uses'=>'SalasController@index'])->where('sala', '[0-9]+');
        Route::post('/',['middleware'=>'permissions:["Reservas"],["R"]','uses'=>'SalasController@index'])->where('sala', '[0-9]+');
        Route::get('/mis_reservas',['middleware'=>'permissions:["Reservas"],["R"]','uses'=>'SalasController@mis_reservas']);
    });

    ////////////////////////////   SECCIONES  PERFILES Y PERMISOS ////////////////////////////////
    Route::get('profile-permissions',['middleware'=>'permissions:["Permisos"],["R"]','uses'=>'PermissionsController@profilePermissions']);
    Route::get('permissions/getProfiles',['middleware'=>'permissions:["Usuarios"],["W"]','uses'=>'PermissionsController@getProfiles']);
	Route::post('addPermissions',['middleware'=>'permissions:["Permisos"],["W"]','uses'=>'PermissionsController@addPermissions']);
	Route::post('removePermissions',['middleware'=>'permissions:["Permisos"],["W"]','uses'=>'PermissionsController@removePermissions']);
	Route::post('addPermissions_user',['middleware'=>'permissions:["Permisos"],["W"]','uses'=>'PermissionsController@addPermissions_user']);
    Route::post('removePermissions_user',['middleware'=>'permissions:["Permisos"],["W"]','uses'=>'PermissionsController@removePermissions_user']);

     ////////////////////////////   CONFIGURACION DE SEÑALETICA  ////////////////////////////////
    Route::group(['prefix' => 'MKD'], function () {
        // Route::get('/','MKDController@index');
        Route::post('/gen_config','MKDController@gen_config');
    });

    ////////////////////////////   GESTION DE TURNOS DE ASISTENCIA  ////////////////////////////////
    Route::group(['prefix' => 'turnos'], function () {
        Route::get('/',['middleware'=>'permissions:["Turnos"],["R"]', 'uses' =>   'TurnosController@index'])->name('turnos.index');
        Route::get('/edit/{id}',['middleware'=>'permissions:["Turnos"],["R"]', 'uses' =>   'TurnosController@edit']);
        Route::post('/save',['middleware'=>'permissions:["Turnos"],["W"]', 'uses' =>   'TurnosController@save']);
        Route::get('delete/{id}',['middleware'=>'permissions:["Turnos"],["D"]', 'uses' => 'TurnosController@delete']);
    });

    ////////////////////////////   FESTIVOS   ////////////////////////////////
	Route::group(['prefix' => 'festives'], function() {
		Route::get('/',['middleware'=>'permissions:["Festivos"],["R"]', 'uses' =>  'FestivesController@index'])->name('festivos.index');
		Route::get('create',['middleware'=>'permissions:["Festivos"],["W"]', 'uses' => 'FestivesController@create']);
		Route::post('save',['middleware'=>'permissions:["Festivos"],["W"]', 'uses' => 'FestivesController@save']);
		Route::get('edit/{id}/{cli}',['middleware'=>'permissions:["Festivos"],["C"]', 'uses' => 'FestivesController@edit']);
		Route::post('update/{id}',['middleware'=>'permissions:["Festivos"],["C"]', 'uses' => 'FestivesController@update']);
		Route::get('delete/{id}',['middleware'=>'permissions:["Festivos"],["D"]', 'uses' => 'FestivesController@delete']);

		Route::post('calendar',['middleware'=>'permissions:["Festivos"],["R"]', 'uses' =>   'FestivesController@calendar_regenerar']);
		Route::post('calendar-filter',['middleware'=>'permissions:["Festivos"],["R"]', 'uses' =>   'FestivesController@calendarFilter']);
        Route::post('tabla-filter',['middleware'=>'permissions:["Festivos"],["R"]', 'uses' =>   'FestivesController@TablaFilter']);
        Route::post('kpi-filter',['middleware'=>'permissions:["Festivos"],["R"]', 'uses' =>   'FestivesController@kpiFilter']);
	});
    
    //////////////////////////// EVENTOS /////////////////////////////////
    Route::group(['prefix' => 'events'], function() {
        Route::get('/',['middleware' => 'permissions:["Eventos"],["R"]', 'uses' => 'EventosController@list']);
        Route::get('/add',['middleware' => 'permissions:["Eventos"],["C"]', 'uses' => 'EventosController@new']);
        Route::post('/param_comando/{id}',['middleware' => 'permissions:["Eventos"],["R"]', 'uses' => 'EventosController@param_comando']);
        Route::get('/calendario/{id}',['middleware' => 'permissions:["Eventos"],["R"]', 'uses' => 'EventosController@calendario']);
        Route::get('/acciones/{id}',['middleware' => 'permissions:["Eventos"],["R"]', 'uses' => 'EventosController@acciones']);
        Route::post('/acciones/{id}',['middleware' => 'permissions:["Eventos"],["R"]', 'uses' => 'EventosController@acciones']);
        Route::post('/save',['middleware' => 'permissions:["Eventos"],["W"]', 'uses' => 'EventosController@save']);
        Route::get('/edit/{id}',['middleware' => 'permissions:["Eventos"],["R"]', 'uses' => 'EventosController@edit']);
        Route::get('/delete/{id}',['middleware' => 'permissions:["Eventos"],["D"]', 'uses' => 'EventosController@delete']);
        Route::get('/param_accion/{regla}/{accion}',['middleware' => 'permissions:["Eventos"],["R"]', 'uses' => 'EventosController@param_accion']);
        Route::post('/cambiar_accion',['middleware' => 'permissions:["Eventos"],["W"]', 'uses' => 'EventosController@cambiar_accion']);
        Route::get('/acciones/nueva/{regla}/{tipo}',['middleware' => 'permissions:["Eventos"],["W"]', 'uses' => 'EventosController@nueva']);
        Route::get('/acciones/delete/{regla}/{accion}/{iteracion}',['middleware' => 'permissions:["Eventos"],["W"]', 'uses' => 'EventosController@delete_accion']);
        Route::get('/acciones/duplicar/{regla}/{accion}',['middleware' => 'permissions:["Eventos"],["W"]', 'uses' => 'EventosController@duplicar_accion']);
        Route::get('/acciones/info/{accion}',['middleware' => 'permissions:["Eventos"],["R"]', 'uses' => 'EventosController@info_accion']);
        Route::post('/acciones/param_acciones/save',['middleware' => 'permissions:["Eventos"],["W"]', 'uses' => 'EventosController@acciones_save']);
        Route::post('/acciones/reindex/{regla}',['middleware' => 'permissions:["Eventos"],["W"]', 'uses' => 'EventosController@acciones_reindex']);
        Route::get('/detalle_evento/{id}',['middleware' => 'permissions:["Eventos"],["R"]', 'uses' => 'EventosController@detalle_evento']);
        Route::get('/log_evento/{id}/{fecha}/{hora}',['middleware' => 'permissions:["Eventos"],["R"]', 'uses' => 'EventosController@ver_log_evento']);
        Route::get('/log_tarea/{id}/{fecha}','EventosController@log_tarea_web');
        Route::post('/probar_comando',['middleware' => 'permissions:["Eventos"],["R"]', 'uses' => 'EventosController@test']);
        Route::post('/reset_estado',['middleware' => 'permissions:["Eventos"],["R"]', 'uses' => 'EventosController@reset_regla']);
    });

    ////////////////////GESTION DE TRABAJOS////////////////////
    Route::group(['prefix' => 'trabajos'], function () {
        //sECCIN DE PARAMETRIZACION - Gerstion de trabajos
        //Tipos de trabajo
        Route::get('/tipos', ['middleware'=>'permissions:["Trabajos tipos"],["R"]','uses'=>'TrabajosController@tipos_index'])->name('trabajos.index');
        Route::get('/tipos/edit/{id?}',['middleware'=>'permissions:["Trabajos tipos"],["W"]','uses'=>'TrabajosController@edit_tipo'])->where('id', '[0-9]+');
        Route::post('/tipos/save',['middleware'=>'permissions:["Trabajos tipos"],["W"]','uses'=>'TrabajosController@update_tipo']);
        Route::get('/tipos/delete/{id}',['middleware'=>'permissions:["Trabajos tipos"],["D"]','uses'=>'TrabajosController@delete_tipo'])->where('id', '[0-9]+');
        //Grupos de trabajo
        Route::get('/grupos',['middleware'=>'permissions:["Trabajos"],["R"]','uses'=>'TrabajosController@grupos_index'])->name('trabajos_grupos.index');
        Route::get('/grupos/edit/{id}',['middleware'=>'permissions:["Trabajos"],["W"]','uses'=>'TrabajosController@edit_grupo'])->where('id', '[0-9]+');
        Route::post('/grupos/save',['middleware'=>'permissions:["Trabajos"],["W"]','uses'=>'TrabajosController@update_grupo']);
        Route::get('/grupos/delete/{id}',['middleware'=>'permissions:["Trabajos"],["D"]','uses'=>'TrabajosController@delete_tipo'])->where('id', '[0-9]+');
        //Gestion de contratas
        Route::get('/contratas',['middleware'=>'permissions:["Trabajos contratas"],["R"]','uses'=>'TrabajosController@contratas_index'])->name('trabajos_contratas.index');
        Route::get('/contratas/edit/{id}',['middleware'=>'permissions:["Trabajos contratas"],["W"]','uses'=>'TrabajosController@edit_contrata'])->where('id', '[0-9]+');
        Route::post('/contratas/save',['middleware'=>'permissions:["Trabajos contratas"],["W"]','uses'=>'TrabajosController@update_contrata']);
        Route::get('/contratas/delete/{id}',['middleware'=>'permissions:["Trabajos contratas"],["D"]','uses'=>'TrabajosController@delete_contrata'])->where('id', '[0-9]+');
        Route::get('/contratas/usuarios_internos/{id}/{id_perfil?}',['middleware'=>'permissions:["Trabajos contratas"],["W"]','uses'=>'TrabajosController@usuarios_internos'])->where('id', '[0-9]+');
        Route::get('/contratas/usuarios_genericos/{id}',['middleware'=>'permissions:["Trabajos contratas"],["W"]','uses'=>'TrabajosController@usuarios_genericos'])->where('id', '[0-9]+');
        Route::post('/contratas/crear_usuarios',['middleware'=>'permissions:["Trabajos contratas"],["W"]','uses'=>'TrabajosController@crear_usuarios_genericos']);
        Route::post('/contratas/save_operario_generico',['middleware'=>'permissions:["Trabajos contratas"],["W"]','uses'=>'TrabajosController@save_operario_generico']);
        Route::post('/contratas/del_operario_generico',['middleware'=>'permissions:["Trabajos contratas"],["W"]','uses'=>'TrabajosController@del_operario_generico']);
        Route::get('/contratas/set_usuarios_contrata/{accion}/{id_contrata}/{id_operario}',['middleware'=>'permissions:["Trabajos contratas"],["W"]','uses'=>'TrabajosController@set_usuarios_contrata']);
        //Gestion de planes de trabajo
        Route::get('/planes',['middleware'=>'permissions:["Trabajos planificador"],["R"]','uses'=>'TrabajosController@planes_index'])->name('trabajos_planes.index');
        Route::get('/planes/edit/{id}',['middleware'=>'permissions:["Trabajos planificador"],["W"]','uses'=>'TrabajosController@edit_plan'])->where('id', '[0-9]+');
        Route::post('/planes/save',['middleware'=>'permissions:["Trabajos planificador"],["W"]','uses'=>'TrabajosController@update_plan']);
        Route::get('/planes/delete/{id}',['middleware'=>'permissions:["Trabajos planificador"],["D"]','uses'=>'TrabajosController@delete_plan'])->where('id', '[0-9]+');
        Route::post('/planes/detalle',['middleware'=>'permissions:["Trabajos planificador"],["W"]','uses'=>'TrabajosController@get_plan']);
        Route::post('/planes/detalle_trabajo/',['middleware'=>'permissions:["Trabajos planificador"],["W"]','uses'=>'TrabajosController@detalle_trabajo']);
        Route::get('/planes/detalle_periodo/{plan}/{grupo}/{trabajo}',['middleware'=>'permissions:["Trabajos planificador"],["W"]','uses'=>'TrabajosController@detalle_periodo'])->where('id', '[0-9]+');
        Route::get('/planes/mini_detalle/{plan}/{grupo}/{trabajo}/{contrata}/{mostrar_operarios}/{mostrar_tiempo}',['middleware'=>'permissions:["Trabajos planificador"],["W"]','uses'=>'TrabajosController@mini_detalle']);
        Route::post('/planes/detalle_save',['middleware'=>'permissions:["Trabajos planificador"],["W"]','uses'=>'TrabajosController@detalle_save']);
        Route::post('/planes/periodo_save',['middleware'=>'permissions:["Trabajos planificador"],["W"]','uses'=>'TrabajosController@periodo_save']);
        Route::get('/planes/delete_detalle/{id}',['middleware'=>'permissions:["Trabajos planificador"],["D"]','uses'=>'TrabajosController@delete_detalle'])->where('id', '[0-9]+');

        //Menu de servicios -> Mis trabajos
        Route::get('/mistrabajos', ['middleware'=>'permissions:["Trabajos mis trabajos"],["R"]','uses'=>'TrabajosController@mis_trabajos'])->name('mistrabajos.index');
        Route::get('/mistrabajos/load_calendario/{fecha}', ['middleware'=>'permissions:["Trabajos mis trabajos"],["R"]','uses'=>'TrabajosController@load_calendario'])->name('mistrabajos.load_calendario');
        Route::get('/mistrabajos/load_dia/{fecha}/{vista?}', ['middleware'=>'permissions:["Trabajos mis trabajos"],["R"]','uses'=>'TrabajosController@load_dia'])->name('mistrabajos.load_dia');
        Route::get('/mistrabajos/iniciar/{id}', ['middleware'=>'permissions:["Trabajos iniciar finalizar"],["R"]','uses'=>'TrabajosController@iniciar_trabajo'])->name('trabajos.iniciar');
        Route::get('/mistrabajos/finalizar/{id}', ['middleware'=>'permissions:["Trabajos iniciar finalizar"],["R"]','uses'=>'TrabajosController@finalizar_trabajo'])->name('trabajos.finalizar');
        Route::get('/mistrabajos/comentarios/{id}', ['middleware'=>'permissions:["Trabajos iniciar finalizar"],["R"]','uses'=>'TrabajosController@get_comentarios_trabajo'])->name('trabajos.comentarios');
        Route::post('/mistrabajos/comentarios', ['middleware'=>'permissions:["Trabajos iniciar finalizar"],["R"]','uses'=>'TrabajosController@save_comentarios_trabajo'])->name('trabajos.save_comentarios');
        Route::get('/mistrabajos/observaciones/{id}', ['middleware'=>'permissions:["Trabajos iniciar finalizar"],["R"]','uses'=>'TrabajosController@get_observaciones_trabajo'])->name('trabajos.observaciones');

        //Menu de servicios -> Planes
        Route::get('/planificacion', ['middleware'=>'permissions:["Trabajos planes"],["R"]','uses'=>'TrabajosController@servicios_planes'])->name('servicios.planes');
        Route::get('/planificacion/ver/{id}/{fecha?}', ['middleware'=>'permissions:["Trabajos planes"],["R"]','uses'=>'TrabajosController@servicios_ver_plan'])->name('servicios.ver_plan');
        Route::post('/servicios/detalle_trabajo', ['middleware'=>'permissions:["Trabajos planes"],["R"]','uses'=>'TrabajosController@servicios_detalle_trabajo'])->name('servicios.detalle_trabajo');

        //Home de operarios de contratas
        Route::post('/set_operario', ['middleware'=>'permissions:["Trabajos mis trabajos"],["R"]','uses'=>'HomeController@set_operario'])->name('home.set_operario');
        
        
    });

    ///////////////////COMBOS AJAX///////////////////
    Route::group(['prefix' => 'combos'], function () {
        Route::post('/limpiadores', 'CombosController@combo_limpiadores');
        Route::get('/plantas/{id_edificio}', 'CombosController@combo_plantas');
        Route::get('/plantas_usuario/{id_edificio}', 'CombosController@combo_plantas_usuario');
        Route::get('/plantas_salas/{id_edificio}', 'CombosController@combo_plantas_salas');
        Route::get('/edificios/{id_cliente}', 'CombosController@combo_edificios');
        Route::get('/clientes','CombosController@clientes');
        Route::post('/clientes_search','CombosController@search_clientes_json');
        Route::get('/regiones/{id_cliente}', 'CombosController@combo_regiones');
        Route::get('/provincias/{id_cliente}', 'CombosController@combo_provincias');
        Route::get('/paises/{id_cliente}', 'CombosController@combo_paises');
        Route::get('/ReloadDepartamentoPadre/{cliente}/{padre}/{id}', 'CombosController@ReloadDepartamentoPadre');
    });

    Route::group(['prefix' => 'filters'], function () {
        Route::post('/loadedificios', 'CombosController@loadedificios');
        Route::post('/loadplantas', 'CombosController@loadplantas');
        Route::post('/loadpuestos', 'CombosController@loadpuestos');
        Route::get('/loadclientes','FiltrosController@loadclientes');
    });

    ////////////////////NOTIFICACIONES////////////////////
    Route::group(['prefix' => 'notif'], function () {
        Route::get('/', 'NotifController@index')->name('notificaciones.index');
        Route::get('/list', 'NotifController@list')->name('notificaciones.list');
        Route::get('/leida', 'NotifController@leida')->name('notificaciones.leida');
        Route::get('/ver/{id}', 'NotifController@index')->name('notificaciones.leida');
    });
});







