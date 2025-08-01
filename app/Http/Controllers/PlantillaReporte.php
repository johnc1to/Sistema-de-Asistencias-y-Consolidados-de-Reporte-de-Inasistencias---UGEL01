<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plantilla_reporte;
use App\Models\Plantilla_variable;
use DB;

class PlantillaReporte extends Controller{
    
    
    public function listar_PlantillaReporte(){
        
        return view("PlantillaReporte/listar_PlantillaReporte");
    }
    
    public function tabla_PlantillaReporte(){
        $sql = Plantilla_reporte::where('estPla',1)->get();
        echo json_encode($sql);
    }
    public function guardar_PlantillaReporte(Request $request){
        $idPla = $request['idPla'];
        $ins['nomPla'] = $request['nomPla'];
        $ins['desPla'] = $request['desPla'];
        $ins['conPla'] = $request['conPla'];
        $ins['conSqlPla'] = $request['conSqlPla'];

        if($idPla){
            Plantilla_reporte::where('idPla',$idPla)->update($ins);
            $ins['idPla'] = $idPla;
        }else{
            $ins['idPla'] = Plantilla_reporte::insertGetId($ins);
        }       
        return $ins;
    }

    public function eliminar_PlantillaReporte(Request $request){
        $idPla=$request['idPla'];
        Plantilla_reporte::where('idPla',$idPla)->update(['estPla'=>0]);
        return 1;
    }
    
    public function listar_PlantilaVariables(Request $request){
        $info['plantilla'] = Plantilla_reporte::where('idPla',$request['idPla'])->get()->toArray()[0];
        //dd($info['plantilla']);
        return view('PlantillaReporte/listar_PlantilaVariables',$info);
    }
    
    public function tabla_PlantilaVariables(Request $request){
        $sql = Plantilla_variable::where(['idPla'=>$request['idPla'],'estVar'=>1])->get()->toArray();
        echo json_encode($sql);
    }
    
    public function guardar_PlantilaVariables(Request $request){
        //dd($request->all());
        
         $data = explode('&&',$request['datos']);
        if($data){
            foreach ($data as $fila) {
                $col = explode('||',$fila);
                $key = array();
                $idVar  = $col['0'];
                $key['idPla'] = $request['idPla'];
                $key['varVar'] = $col['1'];
                $key['texVar'] = $col['2'];
                $key['ancVar']  = $col['5'];
                $key['filVar']  = ($col['6'])?$col['6']:'0';
                $key['valVar']  = $col['7'];
                
                if($idVar){
                    $key['flgVar'] = $col['3'];
                    $key['estVar'] = $col['4'];
                    $key['ancVar']  = $col['5'];
                    $key['filVar']  = ($col['6'])?$col['6']:'0';
                    $key['valVar']  = $col['7'];
                    Plantilla_variable::where(['idVar'=>$idVar])->update($key);
                }else{
                    Plantilla_variable::insert($key);
                }
            }
        }
    }
    
    public function verreportegenerado(Request $request){
        $info['plantilla'] = Plantilla_reporte::where('idPla',$request['idPla'])->get()->toArray()[0];
        $info['variales']  = Plantilla_variable::where(['idPla'=>$request['idPla'],'estVar'=>1])->get()->toArray();
        $info['filtros']   = Plantilla_variable::select("texVar","varVar","valVar")->where(['filVar'=>1,'idPla'=>$request['idPla'],'estVar'=>1])->get()->toArray();
        return view('PlantillaReporte/verreportegenerado',$info);
    }
    
    public function tabla_verreportegenerado(Request $request){
        $plantilla = Plantilla_reporte::where('idPla',$request['idPla'])->get()->toArray()[0];
        $data = DB::connection($plantilla['conPla'])->select($plantilla['conSqlPla']);
        echo json_encode($data);
    }
    
    public function pruebareporte(Request $request){
        $data = array();
        echo json_encode($data);
    }
}



