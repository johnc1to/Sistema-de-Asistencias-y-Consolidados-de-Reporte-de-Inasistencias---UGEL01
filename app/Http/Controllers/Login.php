<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Especialistas;
use App\Models\Contacto;
use Illuminate\Support\Facades\Session;
use DB;

class Login extends Controller{

    //--------Director-------------------------------
    public function ingreso_dir(Request $request){
        //Cerrar las sessiones activas
        $request->session()->invalidate();
        $datos = Contacto::where(['estado'=>1,'flg'=>1,'id_contacto'=>$request['id']])->get()->toArray();
        $datos = ($datos)?$datos[0]:false;        
        if($datos){
            $conf_permisos_group = $this->conf_permisos_group($datos['id_contacto']);
            if($conf_permisos_group) $datos = array_merge($datos,$conf_permisos_group);
            $datos['conf_permisos'] = $this->conf_permisos($datos['id_contacto']);
            $datos['modulos_adicionales'] = $this->modulos_adicionales($conf_permisos_group['codlocal'],$datos['modulos_por_defecto'],0,0," AND tipo='DIR' AND grupo IS NULL ");
            $grupos_adicionales = $this->grupos_adicionales($conf_permisos_group['codlocal'],0,0," AND tipo='DIR' ");
            if($grupos_adicionales){
            for ($i=0; $i < count($grupos_adicionales); $i++) {
                if($grupos_adicionales[$i]['grupo']){
                    $grupos_adicionales[$i]['modulos'] = $this->modulos_adicionales($conf_permisos_group['codlocal'],$datos['modulos_por_defecto'],0,0," AND tipo='DIR' AND grupo = '".$grupos_adicionales[$i]['grupo']."'");
                }
            }
            }
            $datos['grupos_adicionales'] = $grupos_adicionales;        
        }        
        $request->session()->put('siic01',$datos);
        return redirect($request['url']);
	}
    

    //--------Director-------------------------------

    //--------Especialista---------------------------
    public function ingreso_esp(Request $request){
        //Cerrar las sessiones activas
        $request->session()->invalidate();
        $user = Especialistas::where(['estado'=>1,'idespecialista'=>$request->input('id')])->select('usuario','pass')->first();
        $datos = $this->login_admin($user['usuario'],$user['pass']);
        $request->session()->put('siic01_admin',$datos);
        return redirect($request->input('url'));
	}  

    public function login_admin($usuario,$password){
        $datos = $this->verespecialista($usuario);
        if($datos['pass'] == $password){
            $datos['modulos_adicionales'] = $this->modulos_adicionales($datos['idespecialista'],$datos['modulos_por_defecto'],$datos['id_area'],$datos['id_oficina']," AND tipo='ESP' AND grupo IS NULL ",$datos['grupo']);
            $grupos_adicionales = $this->grupos_adicionales($datos['idespecialista'],$datos['id_area'],$datos['id_oficina']," AND tipo='ESP' ",$datos['grupo']);
            if($grupos_adicionales){
            for ($i=0; $i < count($grupos_adicionales); $i++) {
                if($grupos_adicionales[$i]['grupo']){
                    $grupos_adicionales[$i]['modulos'] = $this->modulos_adicionales($datos['idespecialista'],$datos['modulos_por_defecto'],$datos['id_area'],$datos['id_oficina']," AND tipo='ESP' AND subgrupo IS NULL AND grupo = '".$grupos_adicionales[$i]['grupo']."'",$datos['grupo']);
                    
                    $sub_grupos_adicionales = $this->sub_grupos_adicionales($datos['idespecialista'],$datos['modulos_por_defecto'],$datos['id_area'],$datos['id_oficina'],$grupos_adicionales[$i]['grupo'],$datos['grupo']);
					    if($sub_grupos_adicionales){
					        for ($k=0; $k < count($sub_grupos_adicionales); $k++) {
					            $sub_grupos_adicionales[$k]['modulos'] = $this->modulos_adicionales($datos['idespecialista'],$datos['modulos_por_defecto'], $datos['id_area'], $datos['id_oficina']," AND subgrupo = '".$sub_grupos_adicionales[$k]['subgrupo']."'",$datos['grupo']);
					        }
					    }
					    $grupos_adicionales[$i]['sub_grupos_adicionales'] = $sub_grupos_adicionales;
                    
                }
            }
            }
            $datos['grupos_adicionales'] = $grupos_adicionales;        
        }else{
            echo 'Clave incorrecta';
        }
        return $datos;
    }
    //--------Especialista---------------------------
    function versession(){
        dd(session()->get('siic01'));
    }

    public function cerrarsession(Request $request){
        $request->session()->invalidate();
    }
    //Modelo

    function conf_permisos($id_contacto){
        $result = DB::select(
       "SELECT
        R.nivel as d_niv_mod,
        R.distrito d_dist,
        N.nivel_pap,
        C.idconf_permisos,
        C.id_contacto,
        C.codplaza,
        C.esc_codmod,
        R.institucion as cen_edu,
        C.creado,
        C.idnivel,
        R.codlocal,
        C.estado,
        R.red,
        R.idmodalidad,
        R.turno,
        0 as propuesta,
        '' as politica,
        R.gestion d_gestion,
        R.gestion_dependencia as d_ges_dep
        FROM conf_permisos C 
        INNER JOIN iiee_a_evaluar_RIE R ON C.esc_codmod = R.codmod AND C.idnivel = R.idnivel
        INNER JOIN niveles N ON C.idnivel    = N.idnivel 
        WHERE C.estado = 1 AND C.id_contacto = $id_contacto
        ORDER BY C.idnivel DESC");
        if ($result) {
            $arr = [];
            foreach($result as $row) $arr[] = (array) $row;
            return $arr;
        }else{
            return false;
        }
    }

    function conf_permisos_group($id_contacto){
        $result = DB::select(
       "SELECT
        GROUP_CONCAT(DISTINCT(R.nivel)) as d_niv_mod,
        GROUP_CONCAT(DISTINCT(R.distrito)) as d_dist,
        GROUP_CONCAT(DISTINCT(N.nivel_pap)) as niveles,
        GROUP_CONCAT(DISTINCT(C.esc_codmod)) as codmods,
        GROUP_CONCAT(DISTINCT(R.institucion)) as iiee,
        GROUP_CONCAT(DISTINCT(C.idnivel)) as idnivel,
        GROUP_CONCAT(DISTINCT(R.codlocal)) as codlocal,
        GROUP_CONCAT(DISTINCT(R.red)) as red,
        GROUP_CONCAT(DISTINCT(R.idmodalidad)) as idmodalidad,
        GROUP_CONCAT(DISTINCT(R.modalidad)) as modalidad,
        GROUP_CONCAT(DISTINCT(R.gestion)) as d_gestion,
        GROUP_CONCAT(DISTINCT(R.gestion_dependencia)) as d_ges_dep,
        GROUP_CONCAT(DISTINCT(R.turno)) as turno
        FROM conf_permisos C 
        INNER JOIN iiee_a_evaluar_RIE R ON C.esc_codmod = R.codmod AND C.idnivel = R.idnivel
        INNER JOIN niveles N ON C.idnivel    = N.idnivel 
        WHERE C.estado = 1 AND C.id_contacto = $id_contacto");
        if ($result) {
            return (array)$result[0];
        }else{
            return false;
        }
    }

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
        WHERE E.estado = 1 and E.usuario ='".$usuario."'");
        if($result){
            return (array)$result[0];
        }else{
            return false;
        }
    }

    function modulos_adicionales($idespecialista,$modulos_por_defecto, $id_area, $id_oficina, $where='',$fkgrupo=1){
        if($fkgrupo==5){
            $whereEsp = "CONCAT(',',idespecialista,',') LIKE '%,$idespecialista,%'";
        }else{
            $whereEsp = "(idespecialista = 'todo' AND $modulos_por_defecto<>0)     OR 
                      (idespecialista = 'jefatura' AND $idespecialista IN(select idespecialista from especialistas where estado = 1 and (cargo like '%jefe%' or cargo like '%jefa%' or cargo like '%coordinador%' or cargo like '%direct%')) )     OR 
                      CONCAT(',',idespecialista,',') LIKE '%,$idespecialista,%' OR 
                      CONCAT(',',id_area,',') LIKE '%,$id_area,%'               OR 
                      CONCAT(',',id_oficina,',') LIKE '%,$id_oficina,%'         ";
        }
        
        $result = DB::select("SELECT*FROM app_modulos 
        WHERE estado = 1 $where
        AND ($whereEsp) 
            ORDER BY orden ASC");
        if ($result) {
            $arr = [];
            foreach($result as $row) $arr[] = (array) $row;
            return $arr;
        }else{
            return false;
        }
    }

    function grupos_adicionales($idespecialista, $id_area, $id_oficina,$where='',$fkgrupo=1){
        if($fkgrupo==5){
            $whereEsp = "CONCAT(',',idespecialista,',') LIKE '%,$idespecialista,%'";
        }else{
            $whereEsp = "(idespecialista = 'todo' AND $idespecialista<>111)     OR 
                         CONCAT(',',idespecialista,',') LIKE '%,$idespecialista,%' OR 
                         CONCAT(',',id_area,',') LIKE '%,$id_area,%'               OR 
                         CONCAT(',',id_oficina,',') LIKE '%,$id_oficina,%'   ";
        }
        $result = DB::select("SELECT grupo FROM app_modulos 
        WHERE estado = 1 AND grupo IS NOT NULL
        AND ($whereEsp) 
        $where 
        GROUP BY grupo");
        if ($result) {
            $arr = [];
            foreach($result as $row) $arr[] = (array) $row;
            return $arr;
        }else{
            return false;
        }
    }
    
    function sub_grupos_adicionales($idespecialista,$modulos_por_defecto=0, $id_area, $id_oficina,$grupo=false,$fkgrupo=1){
        $where = ($grupo)?" and grupo = '$grupo'":"";
        if($fkgrupo==5){
            $whereEsp = "CONCAT(',',idespecialista,',') LIKE '%,$idespecialista,%'";
        }else{
            $whereEsp = "(idespecialista = 'todo' AND $idespecialista<>111)        OR 
                         CONCAT(',',idespecialista,',') LIKE '%,$idespecialista,%' OR 
                         CONCAT(',',id_area,',') LIKE '%,$id_area,%'               OR 
                         CONCAT(',',id_oficina,',') LIKE '%,$id_oficina,%' ";
        }
        $result = DB::select("SELECT subgrupo FROM app_modulos 
        WHERE estado = 1 and tipo='ESP' AND subgrupo IS NOT NULL $where
        AND ($whereEsp)
        GROUP BY subgrupo");
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
