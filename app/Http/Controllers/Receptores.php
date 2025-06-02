<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Receptor;
use DB;//Conexion a BD 

class Receptores extends Controller
{
    public function listar_receptor(){
        $info['data'] = array();
        return view('Receptor/listar_receptor',$info);
    }
    public function tabla_receptor(Request $request){
        $dni = $request['dni'];
        $nombre = $request['nombre'];
        $apellido_paterno = $request['apellido_paterno'];
        $apellido_materno = $request['apellido_materno'];

        $where = '';
        $where .= ($dni)?" and documento='$dni'":"";
        $where .= ($nombre)?" and (nombres LIKE '%$nombre%' or correo LIKE '%$nombre%' or celular LIKE '%$nombre%')":"";
        $where .= ($apellido_paterno)?" and apellido_paterno LIKE'%$apellido_paterno%'":"";
        $where .= ($apellido_materno)?" and apellido_materno LIKE'%$apellido_materno%'":"";

        $sql = DB::connection('notificacion')->select(
            "SELECT
            R.*,
            CONCAT(
                IF(R.tipo_via<>'',CONCAT('',R.tipo_via),''),
                IF(R.domicilio<>'',CONCAT(' ',R.domicilio),''),
                IF(R.inmueble<>'',CONCAT(' ',R.inmueble),''),
                IF(R.interior<>'',CONCAT(' dptm ',R.interior),''),
                IF(R.piso<>'',CONCAT(' piso ',R.piso),''),
                IF(R.mz<>'',CONCAT(' Mz ',R.mz),''),
                IF(R.lote<>'',CONCAT(' lote ',R.lote),''),
                IF(R.km<>'',CONCAT(' Km ',R.km),''),
                IF(R.sector<>'',CONCAT(' sector ',R.sector),''),
                IF(R.block<>'',CONCAT(' block ',R.block),''),
                IF(R.tipo_zona<>'',CONCAT(' - ',R.tipo_zona),''),
                IF(R.nombre_zona<>'',CONCAT(' ',R.nombre_zona),'')
                ) as texto_domicilio
            FROM receptor R WHERE estado=1 and etapa_de_registro IN (1,2) $where");

        echo json_encode($sql);
    }
    public function guardar_receptor(Request $request){
        $idreceptor =$request['idreceptor'];
        $ins['tipodocumento'] = $request['tipodocumento'];
        $ins['documento'] = $request['documento'];
        $ins['nombres'] = $request['nombres'];
        $ins['apellido_paterno'] = $request['apellido_paterno'];
        $ins['apellido_materno'] = $request['apellido_materno'];
        $ins['correo'] = $request['correo'];
        $ins['correopersonal'] = $request['correopersonal'];
        $ins['celular']     =$request['celular'];

        
       
        //print_r($ins);
        //exit();
        if($idreceptor){
            Receptor::where('idreceptor',$idreceptor)->update($ins);
            $ins['idreceptor'] = $idreceptor;
        }else{
            $ins['idreceptor'] = Receptor::insertGetId($ins);
        }       
        return $ins;
    }

    public function eliminar_receptor(Request $request){
        $idreceptor=$request['idreceptor'];
        Receptor::where('idreceptor',$idreceptor)->update(['estado'=>0]);
        return 1;
    }
    public function ver_editar_receptor(Request $request){
        $idreceptor=$request['idreceptor'];
        $info['tipodocumento'] = DB::connection('notificacion')->select("SELECT tipodocumento FROM receptor WHERE estado=1 and tipodocumento IS NOT NULL and tipodocumento<>'' GROUP BY tipodocumento");
        $info['receptor'] = DB::connection('notificacion')->select("SELECT * FROM receptor WHERE estado=1 and idreceptor=$idreceptor");
        $info['receptor'] = (count($info['receptor']))?$info['receptor'][0]:false;

        return view('Receptor/ver_editar_receptor',$info);
    }
    public function cambiar_clave(Request $request){
        $idreceptor=$request['idreceptor'];
        DB::connection('notificacion')->select("UPDATE receptor SET clave=MD5(documento),etapa_de_registro=1 WHERE idreceptor=".$idreceptor);
        //Receptor::where('idreceptor',$idreceptor)->update(['clave'=>MD5()]);
        return 1;
    }


}
