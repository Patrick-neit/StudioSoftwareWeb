<?php

use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
//use App\Models\Permiso;
use App\Models\Persona;
use App\Models\Cliente;
use App\Models\Evento;
use App\Models\Fotografo;
use App\Models\Organizador;







/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/ping', function (Request $request) {
    return 'pong';
});

Route::post('/registrar-usuario', function (Request $request) {
    //nombres,primer_apellido,segundo_apellido,genero,ci,ci_exp,celular,direccion,correo
    $persona = new Persona();
    
    $persona->nombre = $request->nombre;
    $persona->primer_apellido = $request->primer_apellido;
    $persona->segundo_apellido = $request->segundo_apellido;
    $persona->genero=$request->genero;
    $persona->ci=$request->ci;
    $persona->ci_Exp=$request->ci_Exp;
   
    $persona->celular = $request->celular;
    $persona->direccion=$request->direccion;
    $persona->correo=$request->correo; // validar si el correo existe

    $tipo=$persona->tipo = $request->tipo;
    
    // validar correo, nombre, primer_apellido, ci
    $persona->save();

    if($tipo=='organizador'){
        $organizador = new Organizador();
        $organizador->id=$persona->id;
        $organizador->login=$persona->ci;
        $organizador->pass = md5($persona->ci);
        $organizador->save();
    }else if($tipo=='cliente'){
        $cliente = new Cliente();
        $cliente->id=$persona->id;
        $cliente->login=$persona->ci;
        $cliente->pass = md5($persona->ci);
        $cliente->save();
    }else if($tipo=='fotografo'){
        $fotografo = new Fotografo();
        $fotografo->id=$persona->id;
        $fotografo->login=$persona->ci;
        $fotografo->pass = md5($persona->ci);
        $fotografo->save();
    }

    

    $nombre_completo = "$persona->nombres $request->primer_apellido $request->segundo_apellido";
    $respuesta =['success'=>true,'id'=>$persona->id,'login'=>$persona->ci,'nombre'=>$nombre_completo];
    return response($respuesta, 200)->header('Content-Type', 'application/json');
    
});



Route::post('/autentificar', function (Request $request) { //Revisar

    $app = $request->app;
    $login = $request->login;
    $password =  md5($request->password) ;

    if($app=='cliente'){
        $cliente = new Cliente();
        $usuario = $cliente->obtenerUsuarioCliente($login, $password);
    
    }else if($app=='organizador'){
        $organizador = new Organizador();
        $usuario = $organizador->obtenerUsuarioOrganizador($login, $password); 

    }else if($app=='fotografo'){
        $fotografo = new Fotografo();
        $usuario = $fotografo->obtenerUsuarioFotografo($login, $password);
    }
    
    
    
    if ($usuario) {
        $respuesta =['success'=>true,'tipo'=>$usuario->tipo,'id'=>$usuario->id,'login'=>$usuario->login,'nombre'=>$usuario->nombre_completo];
        return response($respuesta, 200)->header('Content-Type', 'application/json');
    } else {
        $respuesta=['success'=>false,"mensaje"=>"usuario de tipo $app no encontrado"];
        return response($respuesta, 200)->header('Content-Type', 'application/json');
    }
});

Route::post('/crear/evento', function (Request $request){
   // $organizador_id=$request->organizador_id;
    $evento= new Evento();
    $evento->nombre = $request->nombre;
    $evento->descripcion= $request->descripcion;
    $evento->direccion= $request->direccion;
    $evento->fecha= $request->fecha;
    $evento->precio_entrada= $request->precio_entrada;
    
    $evento->organizador_id= $request->organizador_id;
    //$evento->cliente_id= $request->cliente_id ;
    //$evento->fotografo_id= $request->fotografo_id;
   
    //$evento->organizador_id=$organizador_id;
    $evento->save();

    return response("Creado con exito", 200)->header('Content-Type', 'application/json');
});


Route::get('obtener/eventos', function (Request $request) {
    $eventos = new Evento();
    $eventosregistrados= $eventos->geteventos();

    $respuesta = [
        'success'=>true,
        'eventos'=>$eventosregistrados
    ];
    return response($respuesta, 200)->header('Content-Type', 'application/json');
});

Route::get('listado/fotografos', function (Request $request) {
    $fotografos= new Fotografo();
    $listadofotografos= $fotografos->obtenerlistadofotografos();

    $respuesta= [
        'succes'=>true,
        'fotografo'=>$listadofotografos
    ];
    return response($respuesta,200)->header('Content-Type', 'application/json');
});

Route::post('crear/album', function (Request  $request) {
$album = new Album();
$album->cantidad_fotos = $request->cantidad_fotos;
$album->precio=$request->precio;
$album->fotografo_id= $request->fotografo_id;
$album->save();

return response("Creado con exito", 200)->header('Content-Type', 'application/json');
});

Route::get('obtener/albums', function (Request $request) {
    $albums= new Album();
    $fotografo_id =$request->fotografo_id;
    $listadoalbums= $albums->obteneralbumfotografo($fotografo_id);

    $respuesta= [
        'succes'=>true,
        'album'=>$listadoalbums
    ];
    return response($respuesta,200)->header('Content-Type', 'application/json');



});













Route::get('/empleados', function (Request $request) {
    $empleado = new Empleado();
    $buscar=$request->buscar;
    $pagina=$request->pagina;
    $resultado = $empleado->obtenerEmpleados($buscar, $pagina);
    $respuesta = [
         'empleados'=>$resultado['empleados'],
         'total'=>$resultado['total'],
         'parPaginacion'=>$resultado['parPaginacion'],
    ];
    return response($respuesta, 200)->header('Content-Type', 'application/json');
});

Route::get('/empleados/datos', function (Request $request) {
    $empleado = new Empleado();
    /// obtener empleado_id a partir del session_id
    $empleado_id=$request->empleado_id;

    if ($empleado_id) {
        $datos = $empleado->obtenerDatosEmpleado($empleado_id);
        $respuesta = [
            "id"=> $datos->id,
            "correo_corporativo"=> $datos->correo_corporativo,
            "profesion"=> $datos->profesion,
            "activo"=>$datos->activo,
            "persona"=> $datos->persona,
            "cargo"=> $datos->cargo,
            "sucursal"=> $datos->sucursal,
            "sueldo_basico"=> $datos->sueldo_basico,
            "fecha_inicio"=> $datos->fecha_inicio,
            "fecha_final"=> $datos->fecha_final,
        ];
        if ($datos) {
            return response($respuesta, 200)->header('Content-Type', 'application/json');
        } else {
            return response(null, 404)->header('Content-Type', 'application/json');
        }
    } else {
        return response("Ingrese el identificador del empleado", 409)->header('Content-Type', 'application/json');
    }
});


Route::get('/vacaciones/dias', function (Request $request) {
    $vacacion = new Vacacion();
    $empleado_id=$request->empleado_id;
    if ($empleado_id) {
        $resultado = $vacacion->obtenerDiasVacacionesEmpleado($empleado_id);
        $respuesta = [
                'dias_aprobados'=>$resultado,
                'dias_vacaciones'=>15
        ];
        return response($respuesta, 200)->header('Content-Type', 'application/json');
    } else {
        return response("Ingrese el identificador del empleado", 409)->header('Content-Type', 'application/json');
    }
});

Route::get('/vacaciones/dias-vacaciones', function (Request $request) {
    $vacacion = new Vacacion();
    $fecha_ini=$request->fecha_ini;
    $fecha_fin=$request->fecha_fin;
    $dias = $vacacion->CalcularDiasVacaciones($fecha_ini, $fecha_fin);
    return response($dias, 200)->header('Content-Type', 'application/json');
});
Route::get('/vacaciones/solicitudes', function (Request $request) {
    $vacacion = new Vacacion();
    $empleado_id=$request->empleado_id;
    $solVacaciones = $vacacion->getSolicitudesVacaciones($empleado_id);
    $respuesta = [
        'success'=>true,
        'vacaciones'=>$solVacaciones
    ];
    return response($respuesta, 200)->header('Content-Type', 'application/json');
});
Route::get('/boletas', function (Request $request) {
    $boleta = new Boleta();
    $empleado_id=$request->empleado_id;
    $boletas = $boleta->obtenerListadoBoletas($empleado_id);
    $respuesta = [
        'success'=>true,
        'boletas'=>$boletas
    ];
    return response($respuesta, 200)->header('Content-Type', 'application/json');
});

Route::post('/vacaciones/solicitud', function (Request $request) {
    $fecha_ini=$request->fecha_ini;
    $fecha_fin=$request->fecha_fin;
    $observacion=$request->observacion;
    $empleado_id=$request->empleado_id;

    $vacacion = new Vacacion();
    $dias = $vacacion->CalcularDiasVacaciones($fecha_ini, $fecha_fin);
    
    $solicitudVacacion = new SolicitudVacacion();
    $solicitudVacacion->fecha_ini=$fecha_ini;
    $solicitudVacacion->fecha_fin=$fecha_fin;
    $solicitudVacacion->observacion=$observacion;
    $solicitudVacacion->empleado_id=$empleado_id;
    $solicitudVacacion->dias=$dias;
    $solicitudVacacion->estado='PENDIENTE';
    $solicitudVacacion->activo=true;
    $solicitudVacacion->eliminado=false;
    $solicitudVacacion->save();

    return response("OK", 200)->header('Content-Type', 'application/json');
});


Route::get('/personas', function (Request $request) {
    $persona = new Persona();
    $buscar=$request->buscar;
    $pagina=$request->pagina;
    $resultado = $persona->obtenerPersonas($buscar, $pagina);
    $respuesta = [
         'personas'=>$resultado['personas'],
         'total'=>$resultado['total'],
         'parPaginacion'=>$resultado['parPaginacion'],
   ];
    return response($respuesta, 200)->header('Content-Type', 'application/json');
});

// Route::post('/listar-vacaciones', function (Request $request) {
//     $login =$request->login;
//     $pass =$request->pass;
//     if(existe) return '{"success":true}';
// });
