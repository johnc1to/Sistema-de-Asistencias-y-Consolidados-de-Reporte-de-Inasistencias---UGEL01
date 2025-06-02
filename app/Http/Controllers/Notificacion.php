<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Mantenimiento;
use App\Models\Reclamos;
use App\Models\Documento;
use App\Models\Receptor;
use App\Models\Casilla;
use App\Models\Casilladetalle;
use App\Models\Correlativo;
use App\Models\Contacto;
use DB;

use App\Http\Controllers\MailerController;
use App\Mail\Notificarcorreo;
use Illuminate\Support\Facades\Mail;

class Notificacion extends Controller
{
    public function prueba(){
        //copy('https://siic01.ugel01.gob.pe/firmados/1641958845_expediente.pdf','storage/temporal/1641958845_expediente.pdf');
       //$data = Casilladetalle::where(['iddocumento'=>8])->get()->toArray();
       //dd($data);
    }

    public function buscarMultipleNotificaciones(Request $request){
        header('Access-Control-Allow-Origin: *');
        $idreclamo = explode(',',$request->input('idreclamo'));
        $data=Reclamos::whereIn('idreclamo',$idreclamo)->join('receptor','reclamos.idreceptor','=','receptor.idreceptor')->select('idreclamo','cod_reclamo','resumen_pedido','tipo_exp','nombres','apellido_paterno','apellido_materno','tipodocumento','reclamos.idreceptor')->get()->toArray();
        if($data){
            for ($i=0; $i < count($data); $i++) {
                $doc = Documento::where(['estado'=>1,'adj_idreclamo'=>$data[$i]['idreclamo']])->select('*',DB::raw("IF(estado_firmado=1,concat('https://siic01.ugel01.gob.pe/firmados/',nombre), archivo) as archivo"))->get()->toArray();
                $data[$i]['nrow'] = 0;
                if($doc){
                    for ($k=0; $k < count($doc); $k++) {
                        $iddocumento = $doc[$k]['iddocumento'];
                        $doc[$k]['casilla_detalle'] = Casilladetalle::where(['casilla_detalle.estado'=>1,'casilla_detalle.iddocumento'=>$iddocumento])
                        ->join('receptor','casilla_detalle.idreceptor','=','receptor.idreceptor')
                        ->select('casilla_detalle.*','receptor.nombres','receptor.apellido_paterno','receptor.apellido_materno','receptor.correo','receptor.celular',DB::raw("DATE_FORMAT(casilla_detalle.acuse_recibido,'%d/%m/%Y %H:%i:%s') as t_acuse_recibido"),DB::raw("DATE_FORMAT(casilla_detalle.acuse_leido,'%d/%m/%Y %H:%i:%s') as t_acuse_leido"))->get()->toArray();
                        $doc[$k]['count_casilla_detalle'] = count($doc[$k]['casilla_detalle']);
                        $data[$i]['nrow'] = $data[$i]['nrow'] + (($doc[$k]['casilla_detalle'])?count($doc[$k]['casilla_detalle']):1);
                    }
                }
                $data[$i]['doc'] = $doc;
            }
        }
        $info['idespecialista'] = $request->input('idespecialista');
        $info['data']           = $data;
        return view('notificacion/buscarMultipleNotificaciones',$info);
    }

    public function pstrpos(){
        /*
        $data=Contacto::where('institucion', 'like', '%' . 'SASAKAWA' . '%')
        ->where(['contacto.estado'=>'1','contacto.flg'=>'1','conf_permisos.estado'=>'1','iiee_a_evaluar_rie.estado'=>'1','contacto.dni'=>'09282759'])
        ->join('conf_permisos','contacto.id_contacto','=','conf_permisos.id_contacto')
        ->join('iiee_a_evaluar_rie','conf_permisos.esc_codmod','=','iiee_a_evaluar_rie.codmod')
        ->select('contacto.dni')
        ->get()->toArray();
        */
        $ie = str_replace('IE ','','IE SASAKAWA');
        $data = DB::select("SELECT GROUP_CONCAT(C.dni) as dni FROM contacto C 
        INNER JOIN conf_permisos      P ON C.id_contacto = P.id_contacto
        INNER JOIN iiee_a_evaluar_RIE R ON P.esc_codmod  = R.codmod
        WHERE C.estado=1 and C.flg=1 and P.estado=1 and R.estado=1 and R.institucion LIKE '%$ie%'
        GROUP BY C.id_contacto");
        if($data){
        $lista = DB::connection('notificacion')->select("SELECT idreceptor, CONCAT(nombres,' ',apellido_paterno,' ',apellido_materno) as nombrecompleto FROM receptor WHERE documento IN(".$data[0]->dni.") and estado=1");
        }else{
        $lista=false;
        }
        dd($lista);
    }

    public function buscarciudadano(Request $request){
        header('Access-Control-Allow-Origin: *');
        if(strpos($request->input('valor'), 'IE ').''=='0'){
        $ie = str_replace('IE ','',$request->input('valor'));        
        $lista = DB::select("SELECT U.idreceptor,R.institucion,R.codlocal,R.distrito,GROUP_CONCAT(DISTINCT(CONCAT(R.codlocal,' ',R.institucion,': ',U.nombres,' ',U.apellido_paterno,' ',U.apellido_materno))) as nombrecompleto FROM contacto C 
		INNER JOIN siic01_notificacion.receptor U ON C.dni = U.documento
        INNER JOIN conf_permisos      P ON C.id_contacto = P.id_contacto
        INNER JOIN iiee_a_evaluar_RIE R ON P.esc_codmod  = R.codmod        
        WHERE U.estado=1 and U.etapa_de_registro=2 and C.estado=1 and C.flg=1 and P.estado=1 and R.estado=1 and R.institucion LIKE '%$ie%'
        GROUP BY U.idreceptor,R.institucion,R.codlocal,R.distrito");
        }else{
        $lista = DB::connection('notificacion')->select("SELECT idreceptor, CONCAT(nombres,' ',apellido_paterno,' ',apellido_materno) as nombrecompleto FROM receptor WHERE CONCAT(nombres,' ',apellido_paterno,' ',apellido_materno) LIKE '%".$request->input('valor')."%' and estado=1 and etapa_de_registro=2");
        }
        echo json_encode($lista);
    }

    public function notificarciudadano(Request $request){
        header('Access-Control-Allow-Origin: *');        
        $reclamo = Reclamos::where(['estado'=>1,'idreclamo'=>$request->input('idreclamo')])->select('cod_reclamo','idreclamo','tipo_exp','adj')->get()->first();
        $idmantenimiento = ($request['idmantenimiento'])?$request['idmantenimiento']:(($reclamo['tipo_exp']=='INTERNO')?932:46);//Local: 925    Nube: 932
        $mantenimiento = Mantenimiento::where(['estado'=>1,'idmantenimiento'=>$idmantenimiento])->select('abreviatura','descripcion')->get()->first();
        $casilla['asunto']         = str_replace('|EXP|',$reclamo['cod_reclamo'],$mantenimiento['abreviatura']);
        $casilla['cuerpo']         = str_replace('|EXP|',$reclamo['cod_reclamo'],$mantenimiento['descripcion']);
        $casilla['idespecialista'] = $request->input('idespecialista');
        $idcasilla = Casilla::insertGetId($casilla);
        $doc   = $request->input('doc');
        $recep = $request->input('receptor');
        for ($i=0; $i < count($doc); $i++) {
            $documento = Documento::where(['estado'=>1,'iddocumento'=>$doc[$i]])->select('*',DB::raw("IF(estado_firmado=1,REPLACE(archivo,'/cargados/','/firmados/'), archivo) as archivo"))->get()->first();
            for ($k=0; $k < count($recep); $k++) {
                $receptor = Receptor::where(['estado'=>1,'idreceptor'=>$recep[$k]])->get()->first();
                $detalle['ieinstitucion']     = $request->input('institucion')[$k];
                $detalle['iecodlocal']        = $request->input('codlocal')[$k];
                $detalle['iedistrito']        = $request->input('distrito')[$k];
                $casilla['cuerpo']         = str_replace('|NOMBRE|',$receptor['nombres'].' '.$receptor['apellido_paterno'].' '.$receptor['apellido_materno'],$casilla['cuerpo']);
                $casilla['cuerpo']         = str_replace('|LINK|','<a style="color:red;" href="https://ventanillavirtual.ugel01.gob.pe/">ENLACE</a>',$casilla['cuerpo']);
                $casilla['cuerpo']         = str_replace('|RECOMENDACIONES|','',$casilla['cuerpo']);
                //$casilla['cuerpo']         = str_replace('|ADJUNTO|','<a href="'.$documento['archivo'].'">Descargar Resolución de Contrato</a>',$casilla['cuerpo']);
                //$casilla['cuerpo']         = str_replace('|ADJUNTO|','',$casilla['cuerpo']);
                $detalle['cod_reclamo']        = $reclamo['cod_reclamo'];
                $detalle['idreclamo']          = $reclamo['idreclamo'];
                $detalle['idcasilla']          = $idcasilla;
                $detalle['idreceptor']         = $receptor['idreceptor'];
                $detalle['iddocumento']        = $documento['iddocumento'];
                $correlativo = Correlativo::where(['estado'=>1,'tipo'=>'acuse_recibido','anio'=>date('Y')])->select('numero','tipo','anio')->first();
                DB::connection('notificacion')->table('correlativo')->where(['estado'=>1,'tipo'=>'acuse_recibido','anio'=>date('Y')])->increment('numero');
	            $detalle['nro_acuse_recibido'] = $correlativo['numero'].'-'.$correlativo['anio'];
                $detalle['observacion_exp']    = (($reclamo['tipo_exp']=='INTERNO')?'COMUNICADO':'NOTIFICADO');
                $id_casilla_detalle = Casilladetalle::insertGetId($detalle);
                
                $casilla['cuerpo']         = str_replace('|ADJUNTO|','<a href="https://ventanillavirtual.ugel01.gob.pe/index.php/notificacion/ver_archivo/'.$id_casilla_detalle.'">Descargar Resolución de Contrato</a>',$casilla['cuerpo']);
                //copy($documento['archivo'],'storage/temporal/1641958845_expediente.pdf');
                //$this->siic01_enviarcorreo($casilla['asunto'],$receptor['correo'],$casilla['cuerpo']);
                //$receptor['correo'] = 'jacostaf@ugel01.gob.pe';
                //$this->enviarmail($casilla['asunto'],$receptor['correo'],$casilla['cuerpo']);
                $send = new MailerController();
                $send->sendMail($casilla['asunto'],array($receptor['correo'],'jacostaf@ugel01.gob.pe'),$casilla['cuerpo']);
            }
        }
        //Añadir el campo updated_at en la tabla reclamos, el update en laravel utiliza ese campo por defecto
        if($reclamo['adj']=='9'){
            Reclamos::where(['estado'=>1,'idreclamo'=>$request->input('idreclamo')])->update(['etapa'=>'NOTIFICADO','adj'=>10]);
        }else{
            Reclamos::where(['estado'=>1,'idreclamo'=>$request->input('idreclamo')])->update(['etapa'=>(($reclamo['tipo_exp']=='INTERNO')?'COMUNICADO':'NOTIFICADO')]);
        }
    }

    public function enviarmail($asunto='asunto',$correo='jacostaf@ugel01.gob.pe',$body='dsf sdf s fsd fsd fs f sf  fsdf'){
        $send = new Notificarcorreo;
        $send->subject = $asunto;
        $send->body = $body;
        Mail::to($correo)->send($send);
        return "Mensaje enviado";
    }
    
    public function aaa(){
        $this->enviarmail('asunto','jacostaf@ugel01.gob.pe','cuerpo cuerpo cuerpo cuerpo cuerpo cuerpo cuerpo cuerpo cuerpo cuerpo cuerpo cuerpo cuerpo ');
    }
    
    /*
    public function enviarmail(){
        $data = ['name'=>'Mauricio'];
        Mail::to('jacostaf@ugel01.gob.pe')->send(new EmergencyCallReceived($data));
        //Mail::to('jacostaf@ugel01.gob.pe')->send('hola');
    }*/

    public function siic01_envi(){
        echo 'asasa';
    }
    public function siic01_enviarcorreo($asunto='asunto1',$correo='jacostaf@ugel01.gob.pe',$body='body body body body'){
            $jsonData['asunto'] = $asunto;
            $jsonData['correo'] = $correo;
            $jsonData['body']   = $body;
            //$where_exp->exp;
            ini_set('memory_limit','800M');
    		ini_set('max_excution_time',1800);
    		//API URL
    		$url = "http://siic01.ugel01.gob.pe/index.php/notificacion/siic01_enviarcorreo";
    		//echo $url;
    		//inicializamos el objeto CUrl
    		$ch = curl_init($url);
    		//el json simulamos una petici贸n de un login
    		//$jsonData = array("wer"=>"sasasasas");
    		//creamos el json a partir de nuestro arreglo
    		$jsonDataEncoded = json_encode($jsonData);
    		//Indicamos que nuestra petici贸n sera Post
    		curl_setopt($ch, CURLOPT_POST, 1);
    		 //para que la peticion no imprima el resultado como un echo comun, y podamos manipularlo
    		 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    		//Adjuntamos el json a nuestra petici贸n
    		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
    		//Agregamos los encabezados del contenido
    		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    		//Ejecutamos la petici贸n
    		echo curl_exec($ch);
    		    //$result = json_decode(curl_exec($ch));
    		curl_close($ch);
    		    //echo json_encode($result);
    		//return $result;
    		//print_r($result);
    }

}
