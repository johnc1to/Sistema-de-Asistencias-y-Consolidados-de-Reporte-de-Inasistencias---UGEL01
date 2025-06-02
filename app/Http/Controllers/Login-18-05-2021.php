<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Especialistas;
use Illuminate\Support\Facades\Session;
use DB;

class Login extends Controller{

    public function ingreso_esp(Request $request){
        //Cerrar las sessiones activas
        $request->session()->invalidate();
        $user = Especialistas::where(['estado'=>1,'idespecialista'=>$request->input('id')])->select('usuario','pass')->first();
        $datos = $this->login_admin($user['usuario'],$user['pass']);
        $request->session()->put('siic01_admin',$datos);
        return redirect($request->input('url'));
	}

    public function cerrarsession(Request $request){
        $request->session()->invalidate();
    }

    public function login_admin($usuario,$password){
        //$datos = Especialistas::where(['estado'=>1,'usuario'=>$usuario])->select('*')->first();
        $datos = $this->verespecialista($usuario);
        if($datos['pass'] == $password){
            $datos['modulos_adicionales'] = $this->modulos_adicionales($datos['idespecialista'],$datos['modulos_por_defecto'],$datos['id_area'],$datos['id_oficina'],' AND grupo IS NULL ');
            $grupos_adicionales = $this->grupos_adicionales($datos['idespecialista'],$datos['id_area'],$datos['id_oficina']);
            for ($i=0; $i < count($grupos_adicionales); $i++) {
                if($grupos_adicionales[$i]['grupo']){
                    $grupos_adicionales[$i]['modulos'] = $this->modulos_adicionales($datos['idespecialista'],$datos['modulos_por_defecto'],$datos['id_area'],$datos['id_oficina'],"AND grupo = '".$grupos_adicionales[$i]['grupo']."'");
                }
            }
            $datos['grupos_adicionales'] = $grupos_adicionales;        
        }else{
            echo 'Clave incorrecta';
        }
        return $datos;
    }

    function versession(){
        dd(session()->get('siic01_admin'));
    }

    //Modelo

    function verespecialista($usuario){
        $result = DB::select(
       "SELECT E.*,
        Ar.Descripcion as area, 
        Ar.DescripcionCorta as areacorta,
        IFNULL(Eq.Descripcion,Ar.Descripcion) as equipo, 
        IFNULL(Eq.DescripcionCorta,Ar.DescripcionCorta) as equipocorta,
        IFNULL(Eq.SedeOficinaId,Ar.SedeOficinaId) as id_oficina 
        FROM especialistas E
        LEFT JOIN t_SedeOficina Ar ON E.id_area    = Ar.SedeOficinaId
        LEFT JOIN t_SedeOficina Eq ON E.id_oficina = Eq.SedeOficinaId
        WHERE E.estado = 1 and E.usuario =".$usuario);
        if($result){
            return (array)$result[0];
        }else{
            return false;
        }
    }

    function modulos_adicionales($idespecialista,$modulos_por_defecto, $id_area, $id_oficina, $where=''){
        $result = DB::select("SELECT*FROM app_modulos 
        WHERE estado = 1 $where
        AND ( 
                (idespecialista = 'todo' AND $modulos_por_defecto<>0)     OR 
                (idespecialista = 'jefatura' AND $idespecialista IN(select idespecialista from especialistas where estado = 1 and (cargo like '%jefe%' or cargo like '%jefa%' or cargo like '%coordinador%' or cargo like '%direct%')) )     OR 
                CONCAT(',',idespecialista,',') LIKE '%,$idespecialista,%' OR 
                CONCAT(',',id_area,',') LIKE '%,$id_area,%'               OR 
                CONCAT(',',id_oficina,',') LIKE '%,$id_oficina,%'         
            ) 
            ORDER BY orden ASC");
        if ($result) {
            $arr = [];
            foreach($result as $row) $arr[] = (array) $row;
            return $arr;
        }else{
            return false;
        }
    }

    function grupos_adicionales($idespecialista, $id_area, $id_oficina){
        $result = DB::select("SELECT grupo FROM app_modulos 
        WHERE estado = 1 AND grupo IS NOT NULL
        AND ( 
                (idespecialista = 'todo' AND $idespecialista<>111)        OR 
                CONCAT(',',idespecialista,',') LIKE '%,$idespecialista,%' OR 
                CONCAT(',',id_area,',') LIKE '%,$id_area,%'               OR 
                CONCAT(',',id_oficina,',') LIKE '%,$id_oficina,%'         
            )
        GROUP BY grupo");
        if ($result) {
            $arr = [];
            foreach($result as $row) $arr[] = (array) $row;
            return $arr;
        }else{
            return false;
        }
    }
    //Modelo
}
