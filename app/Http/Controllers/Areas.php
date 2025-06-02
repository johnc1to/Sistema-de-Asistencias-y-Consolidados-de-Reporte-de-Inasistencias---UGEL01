<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;
use DB;//Conexion a BD 

class Areas extends Controller
{
    public function listar_Area(){
        $info['data'] = array();
        return view('Area/listar_Area',$info);
    }
    public function tabla_Area(){
        $sql = DB::connection('formularios')->select("SELECT * FROM `material_area` WHERE estado=1");
        echo json_encode($sql);
    }
    public function guardar_Area(Request $request){
        $idarea =$request['idarea'];
        $ins['area'] = $request['area'];
        $ins['estado'] = $request['estado'];
        if($idarea){
            Areas::where('idarea',$idarea)->update($ins);
            $ins['idarea'] = $idarea;
        }else{
            $ins['idarea'] = Areas::insertGetId($ins);
            
        }       
        return $ins;
    }

    public function eliminar_Area(Request $request){
        $idarea=$request['idarea'];
        Areas::where('idarea',$idarea)->update(['estado'=>0]);
        return 1;
    }
}
