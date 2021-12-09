<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Organizador extends Model
{
    use HasFactory;
    protected $table = "organizador";

    public function obtenerUsuarioOrganizador($login,$password){

        $sql = "select concat(coalesce(p.primer_apellido,''),' ',coalesce(p.segundo_apellido,''),' ',p.nombre) as nombre_completo, o.login ,o.id ,p.tipo      
        from organizador o 
       inner join persona p on p.id = o.id 
       where o.login =? and   o.pass =?
       and o.activo =1 and o.eliminado =0";
       
        $usuarios = DB::select($sql, [$login,$password]);
        if (count($usuarios)>0) {
            return $usuarios[0];
        } else {
            return null;
        }
    }
    }

