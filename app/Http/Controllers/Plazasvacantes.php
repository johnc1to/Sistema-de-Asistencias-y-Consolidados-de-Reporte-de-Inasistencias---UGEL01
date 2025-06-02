<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Alertaplazas;
use App\Models\Alertamotivo;
use DB;

class Plazasvacantes extends Controller{
    
    function alertarplazasvacantes(){
        if(!session()->get('siic01')){ return redirect()->to('https://siic01.ugel01.gob.pe/'); }
        $info['session'] = session()->get('siic01');
        $info['alertamotivo'] = Alertamotivo::where('estado',1)->OrderBy('descripcion')->get()->toArray();
        return view('Plazasvacantes/alertarplazasvacantes',$info);
    }
    
    function tabla_alertarplazasvacantes(Request $request){
       $codmod = $request['codmod'];
       $sql = DB::select("SELECT 
        IFNULL(P.idalertplaza,'') as idalertplaza,
        IFNULL(P.exp,'') as exp,
        IFNULL(P.fecha_exp,'') as fecha_exp,
        IFNULL(M.descripcion,'') as motivoalerta,
        IFNULL(P.idmotivo,'') as idmotivo,
        IFNULL(P.obs,'') as obs,
        IFNULL(P.situacion_alerta,'') as situacion_alerta,
        IFNULL(P.situacion_obs,'') as situacion_obs,
        N.codmodce,
        N.nombie,
        N.codplaza,
        N.descargo,
        N.situacion,
        
        N.obser,
        IF(N.numdocum='VACANTE','VACANTE',CONCAT(N.nombres,' ',N.apellipat,' ',N.apellimat)) as docente,
        N.codcatrem,
        N.jornlab,
        N.descmovim,
        N.tiporegistro
        FROM nexus N 
        LEFT JOIN alertaplazas P ON N.codplaza = P.codplaza and P.estado=1
        LEFT JOIN alertamotivo M ON P.idmotivo = M.idmotivo
        WHERE N.estado = 1 and jestado IS NULL and N.codmodce = '$codmod' 
        ORDER BY IF(N.descargo like 'DIRECTOR I.E%',1,IF(N.descargo like '%DIRECTOR%',2,IF(N.descargo like '%PROFESOR%',3,N.descargo))),N.situacion");
       echo json_encode($sql);
    }
    
    function guardar_alertarplazasvacantes(Request $request){
        $data = $request->all();
        $idalertplaza = $data['idalertplaza'];
        unset($data['idalertplaza']);
        unset($data['_token']);
        //$user = Especialistas::where(['estado'=>1,'idespecialista'=>$request->input('id')])->select('usuario','pass')->first();
        $alerta = Alertaplazas::where('idalertplaza',$idalertplaza)->select('idalertplaza','situacion_alerta')->get()->toArray();
        if(count($alerta)){
            if($alerta[0]['situacion_alerta']=='SOLICITADO'){ Alertaplazas::where('idalertplaza',$idalertplaza)->update($data); return 1;}else{ return 0; }
        }else{
            Alertaplazas::insertGetId($data);
            return 1;
        }
    }
    
    function eliminar_alertarplazasvacantes(Request $request){
        Alertaplazas::where('idalertplaza',$request['idalertplaza'])->update(['estado'=>0]);
        return 1;
    }
    
    function plazasvacantesreportadas(){
        echo "plazasvacantesreportadas";
    }
    
    //ESPECIALISTA
    
    function reporteplazasvacantes(){
        if(!session()->get('siic01_admin')){ return redirect()->to('https://siic01.ugel01.gob.pe/'); }
        $info['session'] = session()->get('siic01_admin');
        $info['alertamotivo'] = Alertamotivo::where('estado',1)->get()->toArray();
        return view('Plazasvacantes/reporteplazasvacantes',$info);
    }
    
    function tabla_reporteplazasvacantes(Request $request){
       $where  = "";
       $where .= ($request['situacion_alerta'])?" and P.situacion_alerta='".$request['situacion_alerta']."'":"";
       $where .= ($request['institucion'])?" and N.nombie LIKE '%".$request['institucion']."%'":"";
       $where .= ($request['fecha_exp'])?" and P.fecha_exp='".$request['fecha_exp']."'":"";
       $where .= ($request['anio'])?" and YEAR(P.fecha_exp)='".$request['anio']."'":"";
       $where .= ($request['aniomes'])?" and DATE_FORMAT(P.fecha_exp,'%Y-%m')='".$request['aniomes']."'":"";
       $limite = ($request['limite'])?'LIMIT '.$request['limite']:'';
       //$where .= ($request['fatencion'])?" and P.fatencion='%".$request['fatencion']."%'":"";
       $sql = DB::select("SELECT 
        IFNULL(P.idalertplaza,'') as idalertplaza,
        IFNULL(P.exp,'') as exp,
        IFNULL(P.fecha_exp,'') as fecha_exp,
        IFNULL(M.descripcion,'') as motivoalerta,
        IFNULL(P.idmotivo,'') as idmotivo,
        IFNULL(P.obs,'') as obs,
        IFNULL(P.situacion_alerta,'') as situacion_alerta,
        IFNULL(P.situacion_obs,'') as situacion_obs,
        N.codmodce,
        N.nombie,
        N.codplaza,
        N.descargo,
        N.situacion,
        N.descniveduc,
        N.obser,
        IF(N.numdocum='VACANTE','VACANTE',CONCAT(N.nombres,' ',N.apellipat,' ',N.apellimat)) as docente,
        N.codcatrem,
        N.jornlab,
        N.descmovim,
        N.tiporegistro
        FROM nexus N 
        INNER JOIN alertaplazas P ON N.codplaza = P.codplaza and P.estado=1
        INNER JOIN alertamotivo M ON P.idmotivo = M.idmotivo
        WHERE N.estado = 1 and N.jestado IS NULL and P.estado=1 $where 
        ORDER BY IF(N.descargo like 'DIRECTOR I.E%',1,IF(N.descargo like '%DIRECTOR%',2,IF(N.descargo like '%PROFESOR%',3,N.descargo))),N.situacion 
        $limite");
       echo json_encode($sql);
    }
    
    function guardar_reporteplazasvacantes(Request $request){
        date_default_timezone_set('America/Lima');
        $r_datos_modulo = array();
	    $datos_modulo = explode("&&",$request['datos_modulo']);
	    if($datos_modulo){
    	   for ($i=0; $i < count($datos_modulo); $i++) { 
            	   $r_datos_modulo[] = explode("||",$datos_modulo[$i]);
            }
	    }
	    
        if($r_datos_modulo){
            foreach ($r_datos_modulo as $key) {
                $data = array();
                $idalertplaza = $key[0];
                $data['situacion_alerta'] = $key[1];
                $data['situacion_obs'] = $key[2];
                $data['idespecialista'] = $request['idespecialista'];
                Alertaplazas::where('idalertplaza',$idalertplaza)->update($data);
            }
        }
    }
    
    
    
    
    
}

