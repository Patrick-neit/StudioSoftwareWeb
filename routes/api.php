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

Route::get('obtener/albums/ofertados', function (Request $request){
    $albumo= new Album();
    
    $listadoalbumsofertados=  $albumo->obteneralbumsofertados();

    $respuesta= [
        'succes'=>true,
        'albumofertado'=>$listadoalbumsofertados
    ];
    return response($respuesta,200)->header('Content-Type', 'application/json');

});













