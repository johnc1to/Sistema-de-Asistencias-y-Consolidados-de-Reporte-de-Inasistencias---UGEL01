<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Restablecer_correo_director;
use App\Models\Iiee_a_evaluar_rie;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use DB;

class Restablecer_correo extends Controller{
    
    
    function index(){
        echo 'pedir';
;    }
    
    //ESPECIALISTA
    
    function reportecorreos(){
        if(!session()->get('siic01_admin')){ return redirect()->to('https://siic01.ugel01.gob.pe/'); }
        $info['session'] = session()->get('siic01_admin');
        return view('Restablecer_correo/reportecorreos',$info);
    }
    
    function tabla_reportecorreos(Request $request){
       $where  = "";
       $where .= ($request['situacion_alerta'])?" and R.flg='".$request['situacion_alerta']."'":"";
       $where .= ($request['institucion'])?" and I.institucion LIKE '%".$request['institucion']."%'":"";
       $where .= ($request['fecha_exp'])?" and R.creado_at='".$request['fecha_exp']."'":"";
       $where .= ($request['anio'])?" and YEAR(R.creado_at)='".$request['anio']."'":"";
       $where .= ($request['aniomes'])?" and DATE_FORMAT(R.creado_at,'%Y-%m')='".$request['aniomes']."'":"";
       $limite = ($request['limite'])?'LIMIT '.$request['limite']:'';
       //$where .= ($request['fatencion'])?" and P.fatencion='%".$request['fatencion']."%'":"";
       $sql = DB::select("SELECT 
            R.*,
            DATE_FORMAT(R.creado_at,'%d/%m/%Y %H:%i:%s') as creado_at,
            DATE_FORMAT(R.updated_at,'%d/%m/%Y %H:%i:%s') as updated_at,
            I.correo_inst,
            I.correo,
            I.codlocal,
            CONCAT(IFNULL(C.nombres,''),' ',IFNULL(C.apellipat,''),' ',IFNULL(C.apellimat,'')) as director,
            I.institucion,
            I.distrito
            FROM restablecer_correo_director R 
            INNER JOIN contacto C ON R.id_contacto=C.id_contacto
            INNER JOIN iiee_a_evaluar_RIE I ON R.codmod=I.codmod
            WHERE R.estRes=1 $where
        $limite");
       echo json_encode($sql);
    }
    
    function guardar_reportecorreos(Request $request){
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
                $idRes = $key[0];
                $data['flg'] = $key[1];
                //$data['resRes'] = $key[2];
                Restablecer_correo_director::where('idRes',$idRes)->update($data);
                if($key[2] and $key[1]=='ATENDIDO'){ Iiee_a_evaluar_rie::where('correo_inst',$key[2])->update(['correo_pass_inst'=>'IIee'.date('Y')]); }
                
                $sql = DB::select("SELECT 
                R.*,
                C.usuario,
                I.correo_inst,
                I.correo,
                I.codlocal,
                CONCAT(IFNULL(C.nombres,''),' ',IFNULL(C.apellipat,''),' ',IFNULL(C.apellimat,'')) as director,
                I.institucion,
                I.distrito,
                I.correo_pass_inst
                FROM restablecer_correo_director R 
                INNER JOIN contacto C ON R.id_contacto=C.id_contacto
                INNER JOIN iiee_a_evaluar_RIE I ON R.codmod=I.codmod
                WHERE R.estRes=1 and R.flg='ATENDIDO' and R.idRes = ".$idRes);
                //print_r($sql);
                if(count($sql)){
                    $key = (array)$sql[0];
                    //Restablecer correo
                    $asunto = 'Correo institucional '.$key['correo_inst'].' restablecido';
                    $correo = array('jacostaf@ugel01.gob.pe','mosoresm@ugel01.gob.pe','IE.RESTABLECERCORREO@ugel01.gob.pe',$key['correo']);
                    //$correo = array('jacostaf@ugel01.gob.pe');
                    $body   = view('Restablecer_correo/correo',$key);
                    $this->sendMail($asunto,$correo,$body);
                    //Acceso SIIC01
                    if($key['nuevo_dir']){
                    $asunto = 'Acceso al SIIC01 '.$key['institucion'];
                    //$correo = array('jacostaf@ugel01.gob.pe');
                    $correo = array('jacostaf@ugel01.gob.pe','mosoresm@ugel01.gob.pe','IE.RESTABLECERCORREO@ugel01.gob.pe',$key['correo']);
                    $body   = view('Restablecer_correo/correo_siic01',$key);
                    $this->sendMail($asunto,$correo,$body);
                    }
                }
                
            }
        }
        
        //Enviar correo

        //Enviar correos
        
        return 1;
    }
    
        
        public function correo(){
            $asunto = 'PRUEBA';
            $correo = 'jacostaf@ugel01.gob.pe';
            $info = [];
            $body   = view('Restablecer_correo/correo',$info);
            $this->sendMail($asunto,$correo,$body);
        }
    
	public function sendMail($asunto,$correo,$body){

		//Validar si es Array:
		//------------------------------------
			if (is_array($correo)) { $correo_a    = $correo; } 
			else {                   $correo_a[0] = $correo; }
		//------------------------------------

			$From="IE.RESTABLECERCORREO@ugel01.gob.pe";
			$FromName="IE RESTABLECER CORREO";


			//$this->load->library("My_PHPMailer");
			$FromName = $FromName;
			$form_email=$From;
			$mail=new PHPMailer();
			$mail->IsSMTP();
			$mail->SMTPAuth=true;
			$mail->Host="smtp.office365.com";
			$mail->Username="IE.RESTABLECERCORREO@ugel01.gob.pe";
			$mail->Password="L/532359154838az";
            $mail->CharSet="UTF-8";
			$mail->Port=587;
			$mail->SMTPSecure="STARTTLS";
			$mail->IsHTML(true);
			$mail->From=$form_email;
			$mail->FromName=$FromName;
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
            
			foreach ($correo_a as $email) {
				$mail->AddAddress($email);
			}
			$mail->Subject=$asunto;
			$mail->Body=$body;
			if($mail->send()){
			 return true;
			 }else{
			 return false;
			}
			$mail->CharSet="UTF-8";
	}
    
    
    
}