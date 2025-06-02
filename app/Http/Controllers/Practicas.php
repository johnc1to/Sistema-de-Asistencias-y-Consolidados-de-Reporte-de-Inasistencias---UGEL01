<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\Practica;
use DB;//Conexion a BD 
use Storage;

class Practicas extends Controller
{
    public function listar_Practicas(){
        $info['categoria'] =  DB::connection('buenaspracticas')->select("SELECT * FROM `categorias` WHERE estado=1");
        return view('BuenasPracticas/listar_Practicas',$info);
    }
    public function tabla_Practicas(){
        $sql = DB::connection('buenaspracticas')->select("SELECT P.*, C.titulo as descategoria
        FROM buenas_practicas P 
        INNER JOIN categoria C ON P.idCategoria=C.idCategoria
         WHERE P.estado=1");
        echo json_encode($sql);
    }
    public function guardar_Practicas(Request $request){
        $idpracticas = $request['idpracticas'];
        $ins['titulo'] = $request['Titulo'];
        $ins['descripcion'] = $request['descripcion'];
        $ins['idCategoria'] = $request['idCategoria'];
        $ins['video'] = $request['video'];
        //$ins['pdf'] = $request['pdf'];
        if($request->hasfile('pdf')){
        $archivo = $request->file('pdf')->store('public/buenaspracticas');
        $ins['pdf']= Storage::url($archivo);
        }

        if($request->hasfile('imagen')){
        $archivo = $request->file('imagen')->store('public/buenaspracticas');
        $ins['imagen']= Storage::url($archivo);
        }

        if($idpracticas){
            Practica::where('idpracticas',$idpracticas)->update($ins);
            $ins['idpracticas'] = $idpracticas;
        }else{
            $ins['idpracticas'] = Practica::insertGetId($ins);
        }       
        return $ins;
    }

    public function eliminar_Practicas(Request $request){
        $idpracticas=$request['idpracticas'];
        Practica::where('idpracticas',$idpracticas)->update(['estado'=>0]);
        return 1;
    }
    
    
    public function listar_Categorias(){
        $info['data'] = array();
        return view('BuenasPracticas/listar_Categorias',$info);
    }
    public function tabla_Categorias(){
        $sql = DB::connection('buenaspracticas')->select("SELECT * FROM `categorias` WHERE estado=1");
        echo json_encode($sql);
    }
    public function guardar_Categorias(Request $request){
        $idCategoria=$request['idCategoria'];
        $ins['titulo'] = $request['titulo'];
        $ins['descripcion'] = $request['descripcion'];

        if($request->hasfile('imagen')){
            $archivo = $request->file('imagen')->store('public/buenaspracticas');
            $ins['imagen']= Storage::url($archivo);
            }

        if($idCategoria){
            Categoria::where('idCategoria',$idCategoria)->update($ins);
            $ins['idCategoria'] = $idCategoria;
        }else{
            $ins['idCategoria'] = Categoria::insertGetId($ins);
        }       
        return $ins;
    }

    public function eliminar_Categorias(Request $request){
        $idCategoria=$request['idCategoria'];
        Categoria::where('idCategoria',$idCategoria)->update(['estado'=>0]);
        return 1;
    }
}
