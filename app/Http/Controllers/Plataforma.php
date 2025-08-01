<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ventanillas;
use DB;

class Plataforma extends Controller{
    
    
    public function listar_Plataforma(){
        
        return view("Plataforma/listar_Plataforma");
    }
    
    public function tabla_Plataforma(){
        $sql = Ventanillas::where('estVen',1)->get();
        echo json_encode($sql);
    }
    public function guardar_Plataforma(Request $request){
        $idVen = $request['idVen'];
        $ins['nroVen'] = $request['nroVen'];
        $ins['rutVen'] = $request['rutVen'];
        $ins['ipVen']  = $request['ipVen'];

        if($idVen){
            Ventanillas::where('idVen',$idVen)->update($ins);
            $ins['idVen'] = $idVen;
        }else{
            $ins['idVen'] = Ventanillas::insertGetId($ins);
        }       
        return $ins;
    }

    public function eliminar_Plataforma(Request $request){
        $idVen=$request['idVen'];
        Ventanillas::where('idVen',$idVen)->update(['estVen'=>0]);
        return 1;
    }
    
}