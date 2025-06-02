<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Citasmedicas;
use App\Models\Nexus;
//use Illuminate\Support\Facades\Session;
use DB;

class Citamedica extends Controller{
    
    public function reportecitamedica(){
        if(session()->get('siic01_admin')){
            $info['editar']     = 1;
            $info['session']    = session()->get('siic01_admin');
            return view('citamedica/reportecitamedica',$info);
        }
    }
    
    public function consultareportecitamedica(){
        if(session()->get('siic01_admin')){
            $info['editar']     = 0;
            $info['session']    = session()->get('siic01_admin');
            return view('citamedica/reportecitamedica',$info);
        }
    }
    
    public function vercitamedica(Request $request){
        $citas = Citasmedicas::select("*",DB::raw("DATE_FORMAT(inicio,'%d/%m/%Y %H:%i:%s') as inicio"),DB::raw("IFNULL(dignostico,'') as dignostico"))->where(['idCmm'=>$request['idCmm']])->get()->toArray();
        echo json_encode($citas[0]);
    }
    
    public function editarcitamedica(Request $request){
        //dd($request->all());
        $idCmm = $request['idCmm'];
        unset($request['idCmm']);
        unset($request['_token']);
        Citasmedicas::where('idCmm',$idCmm)->update($request->all());
        echo json_encode($this->pdf_informemedico($idCmm));
    }
    
    public function pdf_informemedico($idCmm){
        //$idCmm = $request['idCmm'];
        DB::connection('formularios')->select("SET lc_time_names = 'es_ES'");
        $cita = Citasmedicas::select("*",DB::raw("DAY(inicio) as dia"),DB::raw("MONTHNAME(inicio) as mes"),DB::raw("YEAR(inicio) as anio"),DB::raw("DATE_FORMAT(inicio,'%d/%m/%Y %H:%i:%s') as inicio"),DB::raw("IFNULL(dignostico,'') as dignostico"))->where(['idCmm'=>$idCmm])->get()->toArray();
        if($cita){
        $cita = $cita[0];
        $pdf = \PDF::loadView('citamedica/pdf_informemedico', compact('cita'));
        $nomarchivo = 'storage/informemedico/'.date('Y').'/';
        if(!is_dir($nomarchivo)){ mkdir($nomarchivo,0755); }
        $nomarchivo .= rand().'.pdf';
        file_put_contents($nomarchivo, $pdf->output());
        Citasmedicas::where(['idCmm'=>$idCmm])->update(['informemedico'=>$nomarchivo]);
        return $nomarchivo;
        //return $pdf-> stream('pdf_informemedico.pdf');
        }else{
        return false;
        }
    }
    
    /*public function pdf_informemedico(Request $request){
        $idCmm = $request['idCmm'];
        DB::connection('formularios')->select("SET lc_time_names = 'es_ES'");
        $cita = Citasmedicas::select("*",DB::raw("DAY(inicio) as dia"),DB::raw("MONTHNAME(inicio) as mes"),DB::raw("YEAR(inicio) as anio"),DB::raw("DATE_FORMAT(inicio,'%d/%m/%Y %H:%i:%s') as inicio"),DB::raw("IFNULL(dignostico,'') as dignostico"))->where(['idCmm'=>$idCmm])->get()->toArray();
        if($cita){
        $cita = $cita[0];
        $pdf = \PDF::loadView('citamedica/pdf_informemedico', compact('cita'));
        //$nomarchivo = 'storage/informemedico/'.date('Y').'/';
        //if(!is_dir($nomarchivo)){ mkdir($nomarchivo,0755); }
        //$nomarchivo .= rand().'.pdf';
        //file_put_contents($nomarchivo, $pdf->output());
        //Citasmedicas::where(['idCmm'=>$idCmm])->update(['informemedico'=>$nomarchivo]);
        //return $nomarchivo;
        return $pdf-> stream('pdf_informemedico.pdf');
        }else{
        return false;
        }
    }*/
    
    public function listarreportecitamedica(Request $request){
        switch($request['boxcita']){
            case '1': $where = " and DATE_FORMAT(inicio,'%Y-%m-%d') <= DATE_FORMAT(now(),'%Y-%m-%d') and dignostico IS NULL"; break;
            case '2': $where = " and DATE_FORMAT(inicio,'%Y-%m-%d') > DATE_FORMAT(now(),'%Y-%m-%d') and dignostico IS NULL"; break;
            case '3': $where = " and dignostico IS NOT NULL"; break;
        }
        $citas = DB::connection('formularios')->select("SELECT*,DATE_FORMAT(inicio,'%d/%m/%Y %H:%i:%s') as inicio,IFNULL(dignostico,'') as dignostico FROM citasmedicas WHERE estado=1".$where);
        echo json_encode($citas);
    }

    public function listarresumencitamedica(){
        $citas = DB::connection('formularios')->select("SELECT 
        DATE_FORMAT(inicio,'%d/%m/%Y') as fecha, 
        count(*) as total,
        SUM(IF(dignostico='APTO',1,0)) as apto,
        SUM(IF(dignostico='NO APTO',1,0)) as noapto,
        SUM(IF(dignostico='NO SE PRESENTO',1,0)) as nosepresento
        FROM citasmedicas WHERE estado=1 GROUP BY DATE_FORMAT(inicio,'%d/%m/%Y')");
        echo json_encode($citas);
    }
    
    
    public function solicitarcitamedica(){
        if(session()->get('siic01')){
            $info['session']        = session()->get('siic01');
            return view('citamedica/solicitarcitamedica',$info);
        }else{
            
        }
    }
    
    public function listarcitamedica(Request $request){
        $info['siguiente_cita'] = $this->siguiente_cita('%d/%m/%Y %H:%i:%s');
        $info['nexus'] = DB::select("SELECT 
            N.nexus_id,
            N.nombue,
            N.numdocum,
            N.codplaza,
            N.nombres,
            N.apellipat,
            N.apellimat,
            N.desctipotrab,
            N.descsubtipt,
            N.descargo,
            N.situacion,
            N.jornlab,
            N.tiporegistro,
            IFNULL(R.correo,'') as correo,
            IFNULL(R.celular,'') as celular,
            GROUP_CONCAT(DATE_FORMAT(C.inicio,'%d/%m/%Y %H:%i:%s') SEPARATOR ' <br>') as inicio
            FROM nexus N 
            LEFT JOIN siic01_notificacion.receptor R    ON N.numdocum = R.documento and R.etapa_de_registro = 2 and R.estado = 1
            LEFT JOIN siic01_formularios.citasmedicas C ON N.numdocum = C.numdocum and C.estado=1 and N.codplaza = C.codplaza and C.dignostico IS NULL
            WHERE N.estado=1 and N.situacion<>'VACANTE' and N.codmodce='".$request['codmodce']."' 
            GROUP BY 
            N.nexus_id,
            N.nombue,
            N.numdocum,
            N.codplaza,
            N.nombres,
            N.apellipat,
            N.apellimat,
            N.desctipotrab,
            N.descsubtipt,
            N.descargo,
            N.situacion,
            N.jornlab,
            N.tiporegistro,
            R.correo,
            R.celular");
        $info['citas'] = Citasmedicas::select("*",DB::raw("DATE_FORMAT(inicio,'%d/%m/%Y %H:%i:%s') as inicio"),DB::raw("IFNULL(dignostico,'') as dignostico"))->where(['estado'=>1,'codmodce'=>$request['codmodce']])->orderBy('inicio', 'desc')->get()->toArray();
        echo json_encode($info);
    }
    
    public function guardarcitamedica(Request $request){
        //dd($request->all());
        if($request['box']){
            for ($i=0; $i < count($request['box']); $i++) {
                $nexus = Nexus::select(DB::raw('TIMESTAMPDIFF(YEAR,fecnac,CURDATE()) AS edad'),'codmodce','descniveduc','distrito','nombie','apellipat','apellimat','nombres','desctipotrab','descsubtipt','descargo','situacion','tiporegistro','codplaza','numdocum')->where('nexus_id',$request['box'][$i])->get()->toArray();
                $nexus = $nexus[0];
                $nexus['correo']      = $request['correo'][$i];
                $nexus['celular']     = $request['celular'][$i];
                $siguiente_cita       = $this->siguiente_cita();
                $nexus['inicio']      = $siguiente_cita['fecha_inicio'];
                $nexus['fin']         = $siguiente_cita['fecha_fin'];
                $nexus['id_contacto'] = $request['id_contacto'];
                Citasmedicas::insert($nexus);
            }
            echo json_encode(1);
        }
    }
    
    function siguiente_cita($formato='%Y-%m-%d %H:%i:%s',$hinicio='09:00:00',$hintermedio='13:00:00',$hfin='15:00:00',$no_atencion='1,3,5,6',$add_tiempo='00:15:00'){
    
    $select = "IF(DATE(MAX(fin))>DATE(NOW()),
    IF(MAX(fin)<CONCAT(DATE(MAX(fin)),' $hfin'),MAX(fin),
    IF(DATE_ADD(MAX(fin), INTERVAL 1  DAY) NOT IN(select fecha from siic01ugel01gob_directores.feriados where estado = 1) AND WEEKDAY(DATE_ADD(MAX(fin), INTERVAL 1  DAY)) NOT IN($no_atencion) ,CONCAT(DATE(DATE_ADD(MAX(fin), INTERVAL 1  DAY)),' $hinicio'),
    IF(DATE_ADD(MAX(fin), INTERVAL 2  DAY) NOT IN(select fecha from siic01ugel01gob_directores.feriados where estado = 1) AND WEEKDAY(DATE_ADD(MAX(fin), INTERVAL 2  DAY)) NOT IN($no_atencion) ,CONCAT(DATE(DATE_ADD(MAX(fin), INTERVAL 2  DAY)),' $hinicio'),
    IF(DATE_ADD(MAX(fin), INTERVAL 3  DAY) NOT IN(select fecha from siic01ugel01gob_directores.feriados where estado = 1) AND WEEKDAY(DATE_ADD(MAX(fin), INTERVAL 3  DAY)) NOT IN($no_atencion) ,CONCAT(DATE(DATE_ADD(MAX(fin), INTERVAL 3  DAY)),' $hinicio'),
    IF(DATE_ADD(MAX(fin), INTERVAL 4  DAY) NOT IN(select fecha from siic01ugel01gob_directores.feriados where estado = 1) AND WEEKDAY(DATE_ADD(MAX(fin), INTERVAL 4  DAY)) NOT IN($no_atencion) ,CONCAT(DATE(DATE_ADD(MAX(fin), INTERVAL 4  DAY)),' $hinicio'),
    IF(DATE_ADD(MAX(fin), INTERVAL 5  DAY) NOT IN(select fecha from siic01ugel01gob_directores.feriados where estado = 1) AND WEEKDAY(DATE_ADD(MAX(fin), INTERVAL 5  DAY)) NOT IN($no_atencion) ,CONCAT(DATE(DATE_ADD(MAX(fin), INTERVAL 5  DAY)),' $hinicio'),
    IF(DATE_ADD(MAX(fin), INTERVAL 6  DAY) NOT IN(select fecha from siic01ugel01gob_directores.feriados where estado = 1) AND WEEKDAY(DATE_ADD(MAX(fin), INTERVAL 6  DAY)) NOT IN($no_atencion) ,CONCAT(DATE(DATE_ADD(MAX(fin), INTERVAL 6  DAY)),' $hinicio'),
    IF(DATE_ADD(MAX(fin), INTERVAL 7  DAY) NOT IN(select fecha from siic01ugel01gob_directores.feriados where estado = 1) AND WEEKDAY(DATE_ADD(MAX(fin), INTERVAL 7  DAY)) NOT IN($no_atencion) ,CONCAT(DATE(DATE_ADD(MAX(fin), INTERVAL 7  DAY)),' $hinicio'),
    
    IF(DATE_ADD(MAX(fin), INTERVAL 8  DAY) NOT IN(select fecha from siic01ugel01gob_directores.feriados where estado = 1) AND WEEKDAY(DATE_ADD(MAX(fin), INTERVAL 8  DAY)) NOT IN($no_atencion) ,CONCAT(DATE(DATE_ADD(MAX(fin), INTERVAL 8  DAY)),' $hinicio'),
    IF(DATE_ADD(MAX(fin), INTERVAL 9  DAY) NOT IN(select fecha from siic01ugel01gob_directores.feriados where estado = 1) AND WEEKDAY(DATE_ADD(MAX(fin), INTERVAL 9  DAY)) NOT IN($no_atencion) ,CONCAT(DATE(DATE_ADD(MAX(fin), INTERVAL 9  DAY)),' $hinicio'),
    IF(DATE_ADD(MAX(fin), INTERVAL 10 DAY) NOT IN(select fecha from siic01ugel01gob_directores.feriados where estado = 1) AND WEEKDAY(DATE_ADD(MAX(fin), INTERVAL 10 DAY)) NOT IN($no_atencion) ,CONCAT(DATE(DATE_ADD(MAX(fin), INTERVAL 10 DAY)),' $hinicio'),
    IF(DATE_ADD(MAX(fin), INTERVAL 11 DAY) NOT IN(select fecha from siic01ugel01gob_directores.feriados where estado = 1) AND WEEKDAY(DATE_ADD(MAX(fin), INTERVAL 11 DAY)) NOT IN($no_atencion) ,CONCAT(DATE(DATE_ADD(MAX(fin), INTERVAL 11 DAY)),' $hinicio'),
    IF(DATE_ADD(MAX(fin), INTERVAL 12 DAY) NOT IN(select fecha from siic01ugel01gob_directores.feriados where estado = 1) AND WEEKDAY(DATE_ADD(MAX(fin), INTERVAL 12 DAY)) NOT IN($no_atencion) ,CONCAT(DATE(DATE_ADD(MAX(fin), INTERVAL 12 DAY)),' $hinicio'),
    IF(DATE_ADD(MAX(fin), INTERVAL 13 DAY) NOT IN(select fecha from siic01ugel01gob_directores.feriados where estado = 1) AND WEEKDAY(DATE_ADD(MAX(fin), INTERVAL 13 DAY)) NOT IN($no_atencion) ,CONCAT(DATE(DATE_ADD(MAX(fin), INTERVAL 13 DAY)),' $hinicio'),
    IF(DATE_ADD(MAX(fin), INTERVAL 14 DAY) NOT IN(select fecha from siic01ugel01gob_directores.feriados where estado = 1) AND WEEKDAY(DATE_ADD(MAX(fin), INTERVAL 14 DAY)) NOT IN($no_atencion) ,CONCAT(DATE(DATE_ADD(MAX(fin), INTERVAL 14 DAY)),' $hinicio'),'n')
    )
    )
    )
    )
    )
    )
    )
    )
    )
    )
    )
    )   
    )        
    ),
    IF(DATE_ADD(NOW(), INTERVAL 1  DAY) NOT IN(select fecha from siic01ugel01gob_directores.feriados where estado = 1) AND WEEKDAY(DATE_ADD(NOW(), INTERVAL 1  DAY)) NOT IN($no_atencion) ,CONCAT(DATE(DATE_ADD(NOW(), INTERVAL 1  DAY)),' $hinicio'),
    IF(DATE_ADD(NOW(), INTERVAL 2  DAY) NOT IN(select fecha from siic01ugel01gob_directores.feriados where estado = 1) AND WEEKDAY(DATE_ADD(NOW(), INTERVAL 2  DAY)) NOT IN($no_atencion) ,CONCAT(DATE(DATE_ADD(NOW(), INTERVAL 2  DAY)),' $hinicio'),
    IF(DATE_ADD(NOW(), INTERVAL 3  DAY) NOT IN(select fecha from siic01ugel01gob_directores.feriados where estado = 1) AND WEEKDAY(DATE_ADD(NOW(), INTERVAL 3  DAY)) NOT IN($no_atencion) ,CONCAT(DATE(DATE_ADD(NOW(), INTERVAL 3  DAY)),' $hinicio'),
    IF(DATE_ADD(NOW(), INTERVAL 4  DAY) NOT IN(select fecha from siic01ugel01gob_directores.feriados where estado = 1) AND WEEKDAY(DATE_ADD(NOW(), INTERVAL 4  DAY)) NOT IN($no_atencion) ,CONCAT(DATE(DATE_ADD(NOW(), INTERVAL 4  DAY)),' $hinicio'),
    IF(DATE_ADD(NOW(), INTERVAL 5  DAY) NOT IN(select fecha from siic01ugel01gob_directores.feriados where estado = 1) AND WEEKDAY(DATE_ADD(NOW(), INTERVAL 5  DAY)) NOT IN($no_atencion) ,CONCAT(DATE(DATE_ADD(NOW(), INTERVAL 5  DAY)),' $hinicio'),
    IF(DATE_ADD(NOW(), INTERVAL 6  DAY) NOT IN(select fecha from siic01ugel01gob_directores.feriados where estado = 1) AND WEEKDAY(DATE_ADD(NOW(), INTERVAL 6  DAY)) NOT IN($no_atencion) ,CONCAT(DATE(DATE_ADD(NOW(), INTERVAL 6  DAY)),' $hinicio'),
    IF(DATE_ADD(NOW(), INTERVAL 7  DAY) NOT IN(select fecha from siic01ugel01gob_directores.feriados where estado = 1) AND WEEKDAY(DATE_ADD(NOW(), INTERVAL 7  DAY)) NOT IN($no_atencion) ,CONCAT(DATE(DATE_ADD(NOW(), INTERVAL 7  DAY)),' $hinicio'),
    
    IF(DATE_ADD(NOW(), INTERVAL 8  DAY) NOT IN(select fecha from siic01ugel01gob_directores.feriados where estado = 1) AND WEEKDAY(DATE_ADD(NOW(), INTERVAL 8  DAY)) NOT IN($no_atencion) ,CONCAT(DATE(DATE_ADD(NOW(), INTERVAL 8  DAY)),' $hinicio'),
    IF(DATE_ADD(NOW(), INTERVAL 9  DAY) NOT IN(select fecha from siic01ugel01gob_directores.feriados where estado = 1) AND WEEKDAY(DATE_ADD(NOW(), INTERVAL 9  DAY)) NOT IN($no_atencion) ,CONCAT(DATE(DATE_ADD(NOW(), INTERVAL 9  DAY)),' $hinicio'),
    IF(DATE_ADD(NOW(), INTERVAL 10 DAY) NOT IN(select fecha from siic01ugel01gob_directores.feriados where estado = 1) AND WEEKDAY(DATE_ADD(NOW(), INTERVAL 10 DAY)) NOT IN($no_atencion) ,CONCAT(DATE(DATE_ADD(NOW(), INTERVAL 10 DAY)),' $hinicio'),
    IF(DATE_ADD(NOW(), INTERVAL 11 DAY) NOT IN(select fecha from siic01ugel01gob_directores.feriados where estado = 1) AND WEEKDAY(DATE_ADD(NOW(), INTERVAL 11 DAY)) NOT IN($no_atencion) ,CONCAT(DATE(DATE_ADD(NOW(), INTERVAL 11 DAY)),' $hinicio'),
    IF(DATE_ADD(NOW(), INTERVAL 12 DAY) NOT IN(select fecha from siic01ugel01gob_directores.feriados where estado = 1) AND WEEKDAY(DATE_ADD(NOW(), INTERVAL 12 DAY)) NOT IN($no_atencion) ,CONCAT(DATE(DATE_ADD(NOW(), INTERVAL 12 DAY)),' $hinicio'),
    IF(DATE_ADD(NOW(), INTERVAL 13 DAY) NOT IN(select fecha from siic01ugel01gob_directores.feriados where estado = 1) AND WEEKDAY(DATE_ADD(NOW(), INTERVAL 13 DAY)) NOT IN($no_atencion) ,CONCAT(DATE(DATE_ADD(NOW(), INTERVAL 13 DAY)),' $hinicio'),
    IF(DATE_ADD(NOW(), INTERVAL 14 DAY) NOT IN(select fecha from siic01ugel01gob_directores.feriados where estado = 1) AND WEEKDAY(DATE_ADD(NOW(), INTERVAL 14 DAY)) NOT IN($no_atencion) ,CONCAT(DATE(DATE_ADD(NOW(), INTERVAL 14 DAY)),' $hinicio'),'n')
    )
    )
    )
    )
    )
    )
    )
    )
    )
    )
    )
    )   
    )        
    )";
    //tramites = 'ACTUALIZACION DE LEGAJO PERSONAL'
    $result = DB::select(
        "SELECT 
        DATE_FORMAT(IF(DATE_FORMAT($select,'%H:%i:%s')='$hintermedio',ADDTIME($select,'01:00:00'),$select),'$formato') as fecha_inicio,
        ADDTIME($select,IF(DATE_FORMAT($select,'%H:%i:%s')='$hintermedio','01:15:00','$add_tiempo')) as fecha_fin
        FROM siic01_formularios.citasmedicas WHERE estado = 1");
        if($result){
            return (Array)$result[0];
        }else{
            return false;
        }
    }
    
    
}