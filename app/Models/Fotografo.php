<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Fotografo extends Model
{
    use HasFactory;
    protected $table="fotografo";

    public function obtenerUsuarioFotografo($login,$password){
        $sql =  "select concat(coalesce(p.primer_apellido,''),' ',coalesce(p.segundo_apellido,''),' ',p.nombre) as nombre_completo, f.login ,f.id ,p.tipo     
        from fotografo f 
       inner join persona p on p.id = f.id 
       where f.login =? and   f.pass=?
       and f.activo =1 and f.eliminado =0";
        $usuarios = DB::select($sql, [$login,$password]);
        if (count($usuarios)>0) {
            return $usuarios[0];
        } else {
            return null;
        }

    }

    public function obtenerlistadofotografos(){
        $sql= "  select f.id, concat(coalesce(p.primer_apellido,''),' ',coalesce(p.segundo_apellido,''),' ',p.nombre) as nombre_completo ,p.ci,p.celular ,p.correo ,p.direccion , f.tarifa_contratacion    
        from fotografo f 
       inner join persona p on p.id = f.id 
      order by f.id  ";
      $fotografo = DB::select($sql);
      return $fotografo;
    }
}
