<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Evento extends Model
{
    use HasFactory;
    protected $table = "evento";

    public  function geteventos(){

        $sql= "select e.id ,e.nombre , e.descripcion , e.direccion , e.fecha , e.precio_entrada  from evento e order by id desc ";
        $eventos = DB::select($sql);
        return $eventos;



    }
}
