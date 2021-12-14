<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Album extends Model
{
    use HasFactory;
    protected $table = "album";

    public function obteneralbumfotografo($fotografo_id){
        $sql = "select a.id , concat(coalesce(p.primer_apellido,''),' ',coalesce(p.segundo_apellido,''),' ',p.nombre) as nombre_completo, f.tarifa_contratacion  , a.cantidad_fotos , a.precio 
        from album a
        inner join fotografo f on f.id = a.fotografo_id 
        inner join persona p on p.id = f.id  
        where  a.eliminado =0 and f.id = $fotografo_id
        order by a.id ";
        $album = DB::select($sql);
        return $album;
    }


}
