<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tipodocumento;
use DB;//Conexion a BD 

class Tipodocumentos extends Controller
{
    public function listar_Tipodocumento(){
        $info['data'] = array();
        return view('Tipodocumentos/listar_Tipodocumento',$info);
    }
    public function tabla_tipodocumento(Request $request){
        $anio= $request['anio'];
        $sql = DB::connection('mysql')->select("SELECT * FROM `doc_tipodocumento` WHERE estado=1 AND YEAR(creado_at) = '$anio'");
        echo json_encode($sql);
    }
    public function guardar_tipodocumento(Request $request){
        $id_tipo = $request['id_tipo'];
        $ins['nivel'] = $request['nivel'];
        $ins['idnivel'] = $request['idnivel'];
        $ins['idmodalidad'] = $request['idmodalidad'];
        $ins['grupo'] = $request['grupo'];
        $ins['tipo_documento'] = $request['tipo_documento'];
        $ins['extenciones'] = $request['extenciones'];
        $ins['visble'] = $request['visble'];
        $ins['aprobado1'] = $request['aprobado1'];
        $ins['idarea1'] = $request['idarea1'];
        $ins['orden'] = $request['orden'];
        $ins['codlocal_habilitado'] = $request['codlocal_habilitado'];
        $ins['informar_al_director'] = $request['informar_al_director'];



        if($id_tipo){
            Tipodocumento::where('id_tipo',$id_tipo)->update($ins);
            $ins['id_tipo'] = $id_tipo;
        }else{
            $ins['id_tipo'] = Tipodocumento::insertGetId($ins);
            
        }       
        return $ins;
    }

    public function eliminar_tipodocumento(Request $request){
        $id_tipo = $request['id_tipo'];
        $ins['estado'] = 0;
        Tipodocumento::where('id_tipo',$id_tipo)->update($ins);
        return 1;
    }
}
