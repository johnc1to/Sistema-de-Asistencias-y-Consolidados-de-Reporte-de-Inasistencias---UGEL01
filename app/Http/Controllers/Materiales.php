<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material_educativo;
use DB;//Conexion a BD 

class Materiales extends Controller
{
    public function listar_Material(){
        $info['area'] = DB::connection('formularios')->select("SELECT * FROM `material_area` WHERE estado=1");
        return view('Material/listar_Material',$info);
    }
    
    public function Reporte_Material(){
        $info['area'] = DB::connection('formularios')->select("SELECT * FROM `material_area` WHERE estado=1");
        return view('Material/Reporte_Material',$info);
    }

    public function tabla_Reporte(Request $request){
        $idarea = $request['idarea'];
        $where ="";
        $where .= ($idarea)?" AND M.idarea= $idarea":"";
        $sql = DB::connection('formularios')->select("SELECT R.*,M.*,IFNULL(M.observacion,'') as observacion,IFNULL(M.Pecosa,'') AS Pecosa, A.area  
        FROM material_educativo M 
        INNER JOIN material_area A ON M.idarea=A.idarea 
        INNER JOIN iiee_a_evaluar_rie R ON M.codmod=R.codmod
        WHERE M.estado=1 $where 
        ORDER BY R.codmod");
        echo json_encode($sql);
    }
    
    public function Registro_Material(){
        $info['session'] = session()->get('siic01');
        $info['area'] = DB::connection('formularios')->select("SELECT * FROM `material_area` WHERE estado=1");
        return view('Material/Registro_Material',$info);
    }

    public function tabla_Material(Request $request){
        $idarea = $request['idarea'];
        $codmod = $request['codmod'];
        $where ="";
        $where .= ($idarea)?" AND M.idarea= $idarea":"";
        $where .= ($codmod)?" AND M.codmod= $codmod":"";
        $sql = DB::connection('formularios')->select("SELECT M.*,IFNULL(M.observacion,'') as observacion, IFNULL(M.Pecosa,'') AS Pecosa, IFNULL(M.cantidad,'') AS cantidad, A.area  
        FROM material_educativo M 
        INNER JOIN material_area A ON M.idarea=A.idarea 
        WHERE M.estado=1".$where);
        echo json_encode($sql);
    }
    public function guardar_Material(Request $request){
        $idMaterial  =$request['idMaterial'];
        $ins['codmod'] = $request['codmod'];
        $ins['grado'] = $request['grado'];
        $ins['idarea'] = $request['idarea'];
        $ins['programado'] = $request['programado'];
        $ins['situacion'] = $request['situacion'];
        $ins['cantidad'] = $request['cantidad'];
        $ins['Pecosa'] = $request['Pecosa'];
        $ins['observacion'] = $request['observacion'];
        if($idMaterial){
            Material_educativo::where('idMaterial',$idMaterial)->update($ins);
            $ins['idMaterial'] = $idMaterial;
        }else{
            $ins['idMaterial'] = Material_educativo::insertGetId($ins);
        }       
        return $ins;
    }

    public function guardar_Material_Masivo(Request $request){
        
        for ($i=0; $i <count($request['idMaterial']); $i++) { 
            $ins['situacion']   = $request['situacion'][$i];
            $ins['cantidad']    = $request['cantidad'][$i];
            $ins['Pecosa']    = $request['Pecosa'][$i];
            $ins['observacion'] = $request['observacion'][$i];
            Material_educativo::where('idMaterial',$request['idMaterial'][$i])->update($ins);
        }

        return 1;


        //print_r($request['idMaterial']);
        //print_r($request);
        //$request->all();
        //dd($request);
    }

    public function eliminar_Material(Request $request){
        $idMaterial=$request['idMaterial'];
        Material_educativo::where('idMaterial',$idMaterial)->update(['estado'=>0]);
        return 1;
    }
    
     public function reporte_material_pbi(){
         $info['url'] = 'https://app.powerbi.com/view?r=eyJrIjoiODk3ODEzYTctZmViMi00MmE2LTk4MWUtMjQ5NDA3ZjUyZTVmIiwidCI6ImQ3OTg3NDY2LWM3YjQtNDEyYS1hNzk0LThjNjA2N2Q1YzU1YSIsImMiOjR9';
        return view('reports/pbi',$info);
    }
}
