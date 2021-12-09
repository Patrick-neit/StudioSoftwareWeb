<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Album extends Model
{
    use HasFactory;
    protected $table = "album";

    public function obteneralbumempleado($fotografo_id){
        $sql = "select album.id , concat(coalesce(p.primer_apellido,''),' ',coalesce(p.segundo_apellido,''),' ',p.nombre) as nombre_completo, fotografo.tarifa_contratacion , album.cantidad_fotos ,album.precio 
        from album 
        inner join persona.id on fotografo.id  
        inner join album.fotografo_id on fotografo.id  
        where  album.eliminado =0
        and fotografo.id =  $fotografo_id
        order by album.id";
        $album = DB::select($sql);
        return $album;
    }


}
