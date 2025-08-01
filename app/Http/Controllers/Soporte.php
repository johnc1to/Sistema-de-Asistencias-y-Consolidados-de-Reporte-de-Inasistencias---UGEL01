<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Soportetecnico;
use App\Models\Nexus;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use DB;
use function Psy\bin;

class Soporte extends Controller{
    //---------------Especialista-----------------------
    public function revisarsoporte(){
        $info['session'] = session()->get('siic01_admin');
        if($info['session']){
            return view("soporte/revisar",$info);
        }
    }

    public function versolicitudesesp(Request $request){
        
        $where  = "";
        $where .= ($request['box']==2)?" and etaSop='TRASLADO DE UGEL'":(($request['box'])?" and etaSop='SOLICITADO'":" and etaSop<>'SOLICITADO'");
        $where .= ($request['tfecha'])?" and DATE_FORMAT(creado_at,'%Y-%m-%d')='".$request['tfecha']."'":" and DATE_FORMAT(creado_at,'%Y-%m')='".$request['aniomes']."'";
        $where .= ($request['idTip'])?" and S.idTip IN(".$request['idTip'].")":"";
        $buscado = $request['buscador'];
        $where .= ($buscado)?" and ( CONCAT(S.nomSop,' ',S.apepatSop,' ',S.apematSop) like '%$buscado%' or S.cueSop like '%$buscado%' or S.dniSop like '%$buscado%' ) ":"";
        $where .= ($request['institucion'])?" and R.institucion like '%".$request['institucion']."%'":"";
        $where .= ($request['fcreacion'])?" and S.creado_at like '%".$request['fcreacion']."%'":"";
        $where .= ($request['fatencion'])?" and S.updated_at like '%".$request['fatencion']."%'":"";
        $limite = ($request['limite'])?' LIMIT '.$request['limite']:'';
        $data=DB::connection('formularios')->select("SELECT S.*,CONCAT(D.nombres,' ',D.apellipat,' ',D.apellimat) as director,D.celular_pers,D.correo_pers,R.nivel,T.desTip,IFNULL(cueSop,'') as cueSop,IFNULL(dniSop,'-') as dniSop,IFNULL(resSop,'') as resSop,R.institucion,R.nivel,
        DATE_FORMAT(creado_at,'%d/%m/%Y') as fecha,DATE_FORMAT(creado_at,'%H:%i:%s') as hora,
        IF(S.etaSop<>'SOLICITADO',DATE_FORMAT(S.updated_at,'%d/%m/%Y'),'') as atemfecha, IF(S.etaSop<>'SOLICITADO',DATE_FORMAT(S.updated_at,'%H:%i:%s'),'') as atemhora
        FROM soportetecnico S 
        INNER JOIN soportetecnicotipo T ON S.idTip = T.idTip
        INNER JOIN siic01ugel01gob_directores.iiee_a_evaluar_RIE R ON S.codmodSop = R.codmod 
        INNER JOIN siic01ugel01gob_directores.contacto           D ON S.id_contactoSop = D.id_contacto
        WHERE S.estSop=1 ".$where."
        ORDER BY ordTip ASC".$limite);
        echo json_encode($data);
    }

    public function guardarrespuesta(Request $request){
       date_default_timezone_set('America/Lima');
       $r_datos_modulo = array();
	   $datos_modulo = explode("&&",$request['datos_modulo']);
	   if($datos_modulo){
    	   for ($i=0; $i < count($datos_modulo); $i++) { 
            	   $r_datos_modulo[] = explode("||",$datos_modulo[$i]);
            }
	   }
	   //dd($r_datos_modulo);
       if($r_datos_modulo){
            foreach ($r_datos_modulo as $key) {
                $data = array();
                $data['etaSop'] = $key[3];
                $data['resSop'] = $key[4];
                $data['cueSop'] = $key[5];
                
                if($data['etaSop']=='ATENDIDO'){
                    if($request['box']){$data['idespecialista'] = $request['idespecialista'];}
                    if( $data['resSop']=='' and ($key[1]==1 or $key[1]==2 or $key[1]==5) ){ $data['resSop']='Clave restablecida a: Ugel01'.date('Y'); }
                    if( $data['resSop']=='' and ($key[1]==3 or $key[1]==4 or $key[1]==6) ){ $data['resSop']='Se pidió la creación al Minedu, en 4 días revise el correo del director aprendoencasa.pe'; }
                }
                
                if($data['etaSop']=='ATENDIDO MINEDU'){
                    if($request['box']){$data['idespecialista'] = $request['idespecialista'];}
                    if( $data['resSop']=='' and ($key[1]==1 or $key[1]==2 or $key[1]==5) ){ $data['resSop']='Se pidió restablecimiento masivo a MINEDU, en breve revise el correo del director aprendoencasa.pe'; }
                    if( $data['resSop']=='' and ($key[1]==3 or $key[1]==4 or $key[1]==6) ){ $data['resSop']='Se pidió la creación al Minedu, en 4 días revise el correo del director aprendoencasa.pe'; }
                }
                
                if($key[0]){
                    Soportetecnico::where('idSop',$key[0])->update($data);
                }
            }
        }
    }
    //---------------Especialista-----------------------

    //---------------Director---------------------------
    public function solicitarsoporte(){
        $info['session'] = session()->get('siic01');
        if($info['session']){
            return view("soporte/solicitar",$info);
        }
    }
    
    
    public function popup_alerta(){
        return view("soporte/popup_alerta");
    }
    
    public function popup_actualizaciondirector(){
        return view("soporte/popup_actualizaciondirector");
    }
    
    public function popup_crearaccesodirector(){
        return view("soporte/popup_crearaccesodirector");
    }

    public function popup_restableceracesodirector(){
        return view("soporte/popup_restableceracesodirector");
    }
    
    public function popup_accesodocente(Request $request){
        $info['nexus'] = Nexus::where(['estado'=>1,'codmodce'=>$request['codmod'],'desctipotrab'=>'DOCENTE'])->orderBy('nombres','ASC')->get()->toArray();
        return view("soporte/popup_accesodocente",$info);
    }

    public function popup_crearaccesodocente(Request $request){
        $info['nexus'] = Nexus::where(['estado'=>1,'codmodce'=>$request['codmod'],'desctipotrab'=>'DOCENTE'])->orderBy('nombres','ASC')->get()->toArray();
        return view("soporte/popup_crearaccesodocente",$info);
    }
    
    public function popup_restableceraccesoestudiante(Request $request){
        return view("soporte/popup_restableceraccesoestudiante",$request->all());
    }

    public function popup_crearaccesoestudiante(Request $request){
        return view("soporte/popup_crearaccesoestudiante",$request->all());
    }

    public function popup_masivorestableceraccesodocente(Request $request){
        return view("soporte/popup_masivorestableceraccesodocente",$request->all());
    }

    public function popup_masivocrearaccesodocente(Request $request){
        return view("soporte/popup_masivocrearaccesodocente",$request->all());
    }
        
    public function guardar_soporte(Request $request){
        date_default_timezone_set('America/Lima');
        $data=$request->all();
        unset($data['docente']);
        unset($data['docbox']);
        unset($data['_token']);
        Soportetecnico::insert($data);
        echo json_encode(1);
    }

    public function guardaraccesoestudiante(Request $request){
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
                $data['tipDocSop'] = $key[0];
                $data['codSop']    = $key[1];
                $data['dniSop']    = $key[2];
                $data['nomSop']    = $key[3];
                $data['apepatSop'] = $key[4];
                $data['apematSop'] = $key[5];
                $data['graSop']    = $key[6];
                $data['idTip']          = $request['idTip'];
                $data['cueSop']         = 'e'.((strpos($key[1],'-')>-1)?$key[1]:$key[2]).'o';
                $data['codmodSop']      = $request['codmodSop'];
                $data['id_contactoSop'] = $request['id_contactoSop'];
                Soportetecnico::insert($data);
            }
        }
    }
    
    public function guardaraccesodocente(Request $request){
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
                $data['tipDocSop'] = $key[0];
                $data['dniSop']    = $key[1];
                $data['nomSop']    = $key[2];
                $data['apepatSop'] = $key[3];
                $data['apematSop'] = $key[4];
                $data['corSop']    = $key[5];
                $data['telSop']    = $key[6];
                $data['idTip']          = $request['idTip'];
                $data['cueSop']         = 'd'.$key[1].'o';
                $data['codmodSop']      = $request['codmodSop'];
                $data['id_contactoSop'] = $request['id_contactoSop'];
                Soportetecnico::insert($data);
            }
        }
    }

    public function versolicitudes(Request $request){
        $data=Soportetecnico::select("soportetecnico.*","soportetecnicotipo.desTip",DB::raw("IFNULL(resSop,'') as resSop"),DB::raw("DATE_FORMAT(creado_at,'%d/%m/%Y') as fecha"))
        ->join('soportetecnicotipo','soportetecnico.idTip','soportetecnicotipo.idTip')
        ->where(['estSop'=>1,'codmodSop'=>$request['codmod']])
        ->orderBy('creado_at','DESC')
        ->get()->toArray();
        echo json_encode($data);
    }

    public function eliminarsolicitud(Request $request){
        date_default_timezone_set('America/Lima');
        Soportetecnico::where(['idSop'=>$request['idSop']])->update(['estSop'=>0]);
        echo json_encode(1);
    }
    //---------------Director---------------------------
}
