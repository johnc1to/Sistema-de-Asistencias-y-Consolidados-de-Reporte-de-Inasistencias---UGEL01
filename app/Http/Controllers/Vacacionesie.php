<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conf_permisos;
use App\Models\Vacaciones_director;
use App\Models\Vacaciones_administrador;
use DB;

class Vacacionesie extends Controller{

    public function director(){
    if(session()->get('siic01')){
        $vari = session()->get('siic01');
        $codmods = Conf_permisos::select("esc_codmod")->where('id_contacto',$vari['id_contacto'])->get()->toArray();
        $codes = $vari['conf_permisos'];
        for ($i=0; $i < count($codmods) ; $i++) {
            $validation["personal"] = $this->getPersonal($codmods[$i]["esc_codmod"], $vari['codplaza']);
        }
        for ($i=0; $i < count($codmods) ; $i++) {
            $validation["personals2"][$i] = $this->getPersonal($codmods[$i]["esc_codmod"], $vari['codplaza']);
        }
        $validation["codemode"] = $codmods;
        $validation["vari"] = $vari;
        $validation["vacacion"] = $this->getVacaciones($vari['conf_permisos'][0]['codlocal'], $vari['codplaza']);
        return view('vacacionesie/director',$validation);
    }
    }

    function eliminarVacaciondirector(Request $request){
			Vacaciones_director::where('id_vacaciones',$request['codigo'])->update(['estado'=>0]);
            $datos[] = array('message'=>'Vacación eliminada con Exito');
            header('Content-type: application/json');
            echo json_encode($datos);
		}
        
    function guardarVacacionesdir(Request $request){
			$vari = session()->get('siic01');
			$data = array(
					"codigo_plaza"=> ($request["codigoplaza"])?$request["codigoplaza"]:'',
					"dni" => $request["dni"],
					"descripcion"=>$request["descripcion"],
					"fecha_inicio" => $request["fecha_in"],
					"fecha_final" => $request["feha_fin"],
					"total_dias" => $request["total_dias"],
					"id_personal" => $request["remplazo"],
					"dniReemplazo" => $request["dniReemplazo"],
					"codigoLocal" => $request["local"],
					"id_contacto" => $vari['id_contacto']
				);
				
		//$this->getDirVaca($request["codigoplaza"],$request["dni"],$request["anio"]) + $request["total_dias"]<=30
		if(true){
            $validation = Vacaciones_director::insertGetId($data);
			if($validation){
        		$datos[] = array('message'=>'Vacación guardada con Exito');
          		header('Content-type: application/json');
          		echo json_encode($datos);
        	}else{
        		$datos[] = array('message'=>'No se pudo guardar Vacación');
          		header('Content-type: application/json');
          		echo json_encode($datos);
        	}
        }else{
            $datos[] = array('message'=>'No puede pasar de los 30 dias');
          		header('Content-type: application/json');
          		echo json_encode($datos);
        }

		}

    

    function getPersonal($codmods, $codeplaza){
        $query = DB::select("SELECT*FROM nexus WHERE codmodce='$codmods' and codplaza<>'$codeplaza' and desctipotrab IN('ADMINISTRATIVO', 'DOCENTE', 'CAS', 'PROFESIONAL DE LA SALUD') and situacion<>'VACANTE'  ");
        $result = array();
        foreach ($query as $key){
            $result[] = (Array)$key;
        }
        return $result;
    }

    function getVacaciones($local, $code){
        $anio = date('Y').",".(date('Y')+1);
        $query = DB::select("SELECT v.id_vacaciones,DATE_FORMAT(fecha_inicio,'%d/%m/%Y') as t_fecha_inicio,DATE_FORMAT(fecha_final,'%d/%m/%Y') as t_fecha_final, v.codigo_plaza, v.dni, v.descripcion, v.fecha_inicio, v.fecha_final, v.total_dias, concat(n.apellipat, ' ' , n.apellimat, ' ', n.nombres) as nombres 
                from vacaciones_director as v 
				inner join nexus as n on v.id_personal=n.codplaza 
                where v.estado=1 and v.codigoLocal='".$local."' and DATE_FORMAT(v.fecha_inicio,'%Y') IN(".$anio.") ");
        $result = array();
        foreach ($query as $key){
            $result[] = (Array)$key;
        }
        return $result;
	}

    public function administrativo(){
        if(session()->get('siic01')){
            $vari = session()->get('siic01');
            $codmods = Conf_permisos::select("esc_codmod")->where('id_contacto',$vari['id_contacto'])->get()->toArray();
            
            for ($i=0; $i < count($codmods) ; $i++) { 
	        	$validation["personals2"][$i] = $this->getPersonal2($codmods[$i]["esc_codmod"], $vari['codplaza']);
	        }

	        $validation["codemode"] = $codmods;
	        $validation["vacacion"] = $this->getVacacionesAdmin($vari['conf_permisos'][0]['codlocal']);

            return view('vacacionesie/administrativo',$validation);
	        //$this->load->view("admin/administrativo", $validation);
        }
    }

    public function guardarVacacionesAdmin(Request $request){
        $vari = session()->get('siic01');
			$data = array(
					"codigo_plaza"=> $request["codigoplaza"],
					"dni" => $request["dni"],
					"descripcion"  => $request["descripcion"],
					"fecha_inicio" => $request["fecha_in"],
					"fecha_final"  => $request["feha_fin"],
					"total_dias"   => $request["total_dias"],
					"codigoLocal"  => $vari['conf_permisos'][0]['codlocal'],
					"id_contacto"  => $vari['id_contacto'],
					"descargo"     => $request["descargo"],
					"desley"       => $request["desley"],
					"situacion"    => $request["situacion"],
					"anadido"      => $request["anadido"]
				);
            
			$validate = $this->getAdminVaca($request["codigoplaza"],$request["dni"],$request["anio"]);
            $suma = $request["total_dias"] + $validate;

			if($suma <= 90){
				$validation = Vacaciones_administrador::insertGetId($data);
				if($validation){
	        		$datos[] = array('message'=>'Vacación guardada con Exito');
	          		header('Content-type: application/json');
	          		echo json_encode($datos);
	        	}
	        	else{
	        		$datos[] = array('message'=>'No se pudo guardar Vacación');
	          		header('Content-type: application/json');
	          		echo json_encode($datos);
	        	}
			}
			else
			{
				$datos[] = array('message'=>'No puede pasar de los 90 dias');
	          	header('Content-type: application/json');
	          	echo json_encode($datos);
			}
    }

    public function eliminarVacacionadmin(Request $request){
        Vacaciones_administrador::where('id_vacaciones',$request['codigo'])->update(['estado'=>0]);
        $datos[] = array('message'=>'Vacación eliminada con Exito');
        header('Content-type: application/json');
        echo json_encode($datos);
    }

    function getAdminVaca($codplaza,$dni,$anio){
        $query = DB::select("SELECT sum(total_dias) as sumadias from vacaciones_administrador where estado=1 and codigo_plaza='$codplaza' and dni='$dni' and DATE_FORMAT(fecha_inicio,'%Y')='$anio'");
        return ($query)?(($query[0]->sumadias)?$query[0]->sumadias:0):0;
	}
	
	function getDirVaca($codplaza,$dni,$anio){
        $query = DB::select("SELECT sum(total_dias) as sumadias from vacaciones_director where estado=1 and codigo_plaza='$codplaza' and dni='$dni' and DATE_FORMAT(fecha_inicio,'%Y')='$anio'");
        return ($query)?(($query[0]->sumadias)?$query[0]->sumadias:0):0;
	}

    function getPersonal2($codmods, $codeplaza){
        $query = DB::select("SELECT*FROM nexus WHERE codmodce='$codmods' and codplaza<>'$codeplaza' and desctipotrab IN('ADMINISTRATIVO', 'DOCENTE', 'PROFESIONAL DE LA SALUD') and situacion<>'VACANTE' and descargo NOT IN('AUXILIAR DE EDUCACION') ");
        $result = array();
        foreach ($query as $key){
            $result[] = (Array)$key;
        }
        return $result;
    }
    
    function getVacacionesAdmin($local){
        $anio = date('Y').",".(date('Y')+1);
        $query = DB::select("SELECT v.id_vacaciones,DATE_FORMAT(fecha_inicio,'%d/%m/%Y') as t_fecha_inicio,DATE_FORMAT(fecha_final,'%d/%m/%Y') as t_fecha_final, v.codigo_plaza, v.dni, v.descripcion, v.fecha_inicio, v.fecha_final, v.total_dias, IFNULL(n.descargo,v.descargo) as descargo, IFNULL(n.situacion,v.situacion) as situacion  
        from vacaciones_administrador as v 
        left join nexus as n on v.codigo_plaza=n.codplaza and n.situacion not in('VACANTE') and n.tiporegistro in('ORGANICA','EVENTUAL')
        where v.estado=1 and v.codigoLocal='".$local."' and DATE_FORMAT(v.fecha_inicio,'%Y') IN(".$anio.")");
        $result = array();
        foreach ($query as $key){
            $result[] = (Array)$key;
        }
        return $result;
	}

    /*
    $vari = $this->session->userdata("siic01");


			$codmods = $this->ModelEspecialista->getcodmod($vari->id_contacto);

	        for ($i=0; $i < count($codmods) ; $i++) { 
	        	$validation["personals2"][$i] = $this->ModelEspecialista->getPersonal2($codmods[$i]["esc_codmod"], $vari->codplaza);
	        }

	        $validation["codemode"] = $codmods;
	        $validation["vacacion"] = $this->ModelEspecialista->getVacacionesAdmin($vari->conf_permisos[0]->codlocal);

	        $this->load->view("admin/administrativo", $validation);
    */

}
