<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reclamos;
use App\Models\Casilla;
use App\Models\Casilladetalle;
use App\Models\Correlativo;
use App\Models\Documento;
use App\Models\Mantenimiento;
use Storage;
use DB;

use App\Http\Controllers\MailerController;

use App\Mail\Notificarcorreo;
use Illuminate\Support\Facades\Mail;

class Visacion extends Controller{

    public function certificadodeestudio(){
        $info['session'] = session()->get('siic01_admin');
        if($info['session']){
            return view("visacion/certificadodeestudio",$info);
        }
    }

    public function ver_certificadodeestudio(Request $request){
        $where = '';
        switch ($request['box']) {
            case '1':$where .= " and etapa IN('REGISTRADO','RESPONDIDO','NOTIFICADO','COMUNICADO','REPROGRAMAR')"; break;
            case '2':$where .= " and etapa IN('SUBSANAR')"; break;
            case '3':$where .= " and etapa IN('CITADO','CONFIRMADO')"; break;
            case '4':$where .= " and etapa IN('REPROGRAMAR')"; break;
            case '5':$where .= " and etapa IN('RECEPCIONADO')"; break;
            case '6':$where .= " and etapa IN('ARCHIVADO')"; break;
            default :$where .= ""; break;
        }

        $fecha = $request['fecha'];
        if($fecha){
            if( strpos($fecha,'to')>-1 ){
                $r_fecha = explode(' to ',$fecha);
                $where .= " AND DATE_FORMAT(R.fecha_expediente,'%Y-%m-%d') BETWEEN '".$r_fecha[0]."' AND  '".$r_fecha[1]."'";
            }else{
                $where .= " AND DATE_FORMAT(R.fecha_expediente,'%Y-%m-%d') = '".$fecha."'";
            }
        }

        $where .= ($request['buscar'])?" and ( R.cod_reclamo like '%".$request['buscar']."%' or U.documento like '%".$request['buscar']."%' or CONCAT(U.nombres,' ',U.apellido_paterno,' ',U.apellido_materno) like '%".$request['buscar']."%' or  R.resumen_pedido like '%".$request['buscar']."%' or U.celular like '%".$request['buscar']."%' or U.correo like '%".$request['buscar']."%')":"";

        $data = DB::connection('notificacion')->select("SELECT 
        R.idreclamo,
        R.cod_reclamo,
        R.fecha_expediente,
        DATE_FORMAT(R.fecha_expediente,'%d/%m/%Y') as t_fecha_expediente,
        U.documento,
        CONCAT(U.nombres,' ',U.apellido_paterno,' ',U.apellido_materno) as ciudadano,
        R.resumen_pedido,
        R.etapa,
        U.celular,
        U.correo,
        MIN(A.archivo) as archivo,
        DATE_FORMAT(MAX(D.fechacita),'%d/%m/%Y %H:%i:%s') as fechacita,
        COUNT(DISTINCT(IF(D.observacion_exp='CITADO',D.id_casilla_detalle,NULL))) as cantidad_citas,
        COUNT(DISTINCT(IF(S.observacion_exp='SUBSANAR',S.id_casilla_detalle,NULL))) as cantidad_subsanar
        FROM reclamos R 
        INNER JOIN receptor U ON R.idreceptor=U.idreceptor
        INNER JOIN reclamos_adjunto A ON R.idreclamo=A.idreclamo
        LEFT  JOIN casilla_detalle  D ON R.idreclamo=D.idreclamo AND D.estado=1 AND D.fechacita IS NOT NULL
        LEFT  JOIN casilla_detalle  S ON R.idreclamo=S.idreclamo AND S.estado=1 AND S.observacion_exp='SUBSANAR'
        WHERE R.estado=1 and U.estado=1 and A.estado=1 and id_tipo_tramite IN (441,442,976)".$where.
        " GROUP BY R.idreclamo,R.cod_reclamo,R.fecha_expediente,U.documento,U.nombres,U.apellido_paterno,U.apellido_materno,R.resumen_pedido,R.etapa,U.celular,U.correo 
        ORDER BY A.idadjunto DESC");
        echo json_encode($data);
    }

    public function solicitarsubsanarcertificado(Request $request){

        $data['exp'] = (Array)DB::connection('notificacion')->select("SELECT 
        R.idreclamo,
        R.cod_reclamo,
        R.fecha_expediente,
        U.documento,
        CONCAT(U.nombres,' ',U.apellido_paterno,' ',U.apellido_materno) as ciudadano,
        R.resumen_pedido,
        U.celular,
        U.correo,
        A.archivo
        FROM reclamos R 
        INNER JOIN receptor U ON R.idreceptor=U.idreceptor
        INNER JOIN reclamos_adjunto A ON R.idreclamo=A.idreclamo
        WHERE R.estado=1 and A.estado=1 and R.idreclamo=".$request['idreclamo']." ORDER BY A.idadjunto DESC")[0];

        $data['obs'] = (Array)DB::connection('notificacion')->select("SELECT 
        D.id_casilla_detalle,
        D.observacion_exp,
        E.nombre,
        C.cuerpo,
        D.confirmarcita,
        DATE_FORMAT(D.acuse_recibido,'%d/%m/%Y %H:%i') as acuse_recibido, 
        DATE_FORMAT(D.acuse_leido,'%d/%m/%Y %H:%i') as acuse_leido 
        FROM casilla C 
        INNER JOIN casilla_detalle D ON C.idcasilla = D.idcasilla and D.estado=1
        INNER JOIN siic01ugel01gob_directores.especialistas E ON C.idespecialista = E.idespecialista
        WHERE C.estado = 1 and D.idreclamo = ".$request['idreclamo']."
         ORDER BY D.acuse_recibido DESC");
         $data['session'] = session()->get('siic01_admin');
         $modelo = Mantenimiento::where(['idmantenimiento'=>1046])->get()->first();
         $data['asunto'] = $modelo['abreviatura'];
         $data['modelo'] = $modelo['descripcion'];

        return view('visacion/popup_solicitarsubsanarcertificado',$data);
    }

    public function guardarsubsanarcertificado(Request $request){
        //dd($request->all());
        $reclamo = Reclamos::where(['estado'=>1,'idreclamo'=>$request['idreclamo']])->select('cod_reclamo','idreclamo','tipo_exp','idreceptor','correo')->get()->first();
        $session = session()->get('siic01_admin');
        $adjuntos = '';
        $iddocumento = 0;
        $ruta=false;
        if($request->hasfile('txt_archivo')){
            $archivo = $request->file('txt_archivo')->store('public');
            $ruta = Storage::url($archivo);
            $casilla['adj_idreclamo']    = $request['idreclamo'];
            $casilla['adj_equipo']       = $session['equipo'];
            $casilla['idespecialista']   = $request['idespecialista'];
            $casilla['nombre']           = 'Certificado con observaciones pendientes';
            $casilla['peso']             = filesize('.'.$ruta);
            $casilla['tipo']             = strtoupper(explode('.',$ruta)[count(explode('.',$ruta))-1]);
            $casilla['ruta']             = $ruta;
            $casilla['archivo']          = 'https://aplicacion.ugel01.gob.pe/public'.$ruta;
            $casilla['tipo_documento']   = '15';
            $casilla['anio']             = date('Y');
            $casilla['adj_especialista'] = $session['esp_nombres'].' '.$session['esp_apellido_paterno'].' '.$session['esp_apellido_materno'];
            $casilla['adj_dni']          = $session['ddni'];
            $casilla['adj_area']         = $session['area'];
            $casilla['adj_minarea']      = $session['areacorta'];
            $casilla['adj_minequipo']    = $session['equipocorta'];
            $casilla['nombre_documento'] = str_replace('/storage/','',$ruta);
            $iddocumento = Documento::insertGetId($casilla);
            $adjuntos = '<b>Adjuntos:</b><br>'.'<a style="color:blue;" target="_blank" href="'.$casilla['archivo'].'">Certificado con observaciones pendientes</a>';
        }

        $casilla = array();
        $cuerpo = str_replace('|ADJUNTOS|',$adjuntos,$request['txt_descripcion']);
        $casilla['asunto']         = str_replace('|EXP|',$reclamo['cod_reclamo'],$request['asunto']);
        $casilla['cuerpo']         = $cuerpo;
        $casilla['idespecialista'] = $request['idespecialista'];
        echo $casilla['cuerpo'];
        $idcasilla = Casilla::insertGetId($casilla);

        $detalle = array();
        $detalle['cod_reclamo']    = $reclamo['cod_reclamo'];
        $detalle['idreclamo']      = $reclamo['idreclamo'];
        $detalle['idcasilla']      = $idcasilla;
        $detalle['idreceptor']     = $reclamo['idreceptor'];
        $detalle['iddocumento']    = $iddocumento;
        //dd($detalle);
        $correlativo = Correlativo::where(['estado'=>1,'tipo'=>'acuse_recibido','anio'=>date('Y')])->select('numero','tipo','anio')->first();
        DB::connection('notificacion')->table('correlativo')->where(['estado'=>1,'tipo'=>'acuse_recibido','anio'=>date('Y')])->increment('numero');
	    $detalle['nro_acuse_recibido'] = $correlativo['numero'].'-'.$correlativo['anio'];
        $detalle['observacion_exp']    = 'SUBSANAR';
        Casilladetalle::insert($detalle);

        Reclamos::where(['estado'=>1,'idreclamo'=>$request['idreclamo']])->update(array('etapa'=>'SUBSANAR'));

        //$this->enviarmail($request['asunto'],'jacostaf@ugel01.gob.pe',$cuerpo);
        echo 'ruta>'.$ruta.'<';
        $send = new MailerController();
        $send->sendMail($casilla['asunto'],array('jacostaf@ugel01.gob.pe',$reclamo['correo']),$cuerpo,$ruta);
    }
    
    public function citarciudadano(Request $request){
        $data['idreclamo']=$request['idreclamo'];
        $data['exp'] = (Array)DB::connection('notificacion')->select("SELECT 
        R.idreclamo,
        R.cod_reclamo,
        R.fecha_expediente,
        U.documento,
        CONCAT(U.nombres,' ',U.apellido_paterno,' ',U.apellido_materno) as ciudadano,
        R.resumen_pedido,
        U.celular,
        U.correo,
        A.archivo
        FROM reclamos R 
        INNER JOIN receptor U ON R.idreceptor=U.idreceptor
        INNER JOIN reclamos_adjunto A ON R.idreclamo=A.idreclamo
        WHERE R.estado=1 and A.estado=1 and R.idreclamo=".$request['idreclamo'])[0];
        $data['session'] = session()->get('siic01_admin');
        $modelo = Mantenimiento::where(['idmantenimiento'=>1047])->get()->first();
        $data['asunto'] = $modelo['abreviatura'];
        $data['modelo'] = $modelo['descripcion'];
        return view('visacion/popup_citarciudadano',$data);
    }

    public function guardarcitarciudadano(Request $request){
        //dd($request->all());
        $reclamo = Reclamos::where(['estado'=>1,'idreclamo'=>$request['idreclamo']])->select('cod_reclamo','idreclamo','tipo_exp','idreceptor','correo')->get()->first();
        $session = session()->get('siic01_admin');

        $casilla = array();
        $cuerpo = $request['txt_descripcion'];
        $casilla['asunto']         = str_replace('|EXP|',$reclamo['cod_reclamo'],$request['asunto']);
        $casilla['cuerpo']         = $cuerpo;
        $casilla['idespecialista'] = $request['idespecialista'];
        echo $casilla['cuerpo'];
        $idcasilla = Casilla::insertGetId($casilla);

        $detalle = array();
        $detalle['cod_reclamo']    = $reclamo['cod_reclamo'];
        $detalle['idreclamo']      = $reclamo['idreclamo'];
        $detalle['idcasilla']      = $idcasilla;
        $detalle['idreceptor']     = $reclamo['idreceptor'];
        $detalle['fechacita']      = $request['txtfecha'].' '.$request['txthora'].':'.$request['txtminutos'].':00';
        
        $correlativo = Correlativo::where(['estado'=>1,'tipo'=>'acuse_recibido','anio'=>date('Y')])->select('numero','tipo','anio')->first();
        DB::connection('notificacion')->table('correlativo')->where(['estado'=>1,'tipo'=>'acuse_recibido','anio'=>date('Y')])->increment('numero');
	    $detalle['nro_acuse_recibido'] = $correlativo['numero'].'-'.$correlativo['anio'];
        $detalle['observacion_exp']    = 'CITADO';
        Casilladetalle::insert($detalle);

        Reclamos::where(['estado'=>1,'idreclamo'=>$request['idreclamo']])->update(array('etapa'=>'CITADO'));

        $send = new MailerController();
        $send->sendMail($casilla['asunto'],array('jacostaf@ugel01.gob.pe',$reclamo['correo']),$cuerpo);
        //$this->enviarmail($request['asunto'],'jacostaf@ugel01.gob.pe',$cuerpo);
    }

    public function recepcionar_certificado(Request $request){
        Reclamos::where(['estado'=>1,'idreclamo'=>$request['idreclamo']])->update(array('etapa'=>'RECEPCIONADO'));
    }

    public function archivar_certificado(Request $request){
        Reclamos::where(['estado'=>1,'idreclamo'=>$request['idreclamo']])->update(array('etapa'=>'ARCHIVADO'));
    }

    

    public function session(){
        dd(session()->get('siic01_admin'));
    }



    public function enviarmailvis($asunto='asunto',$correo='jacostaf@ugel01.gob.pe',$body='dsf sdf s fsd fsd fs f sf  fsdf'){

        $send = new MailerController();
        $send->sendMail($asunto,$correo,$body);

        //$send = new Notificarcorreo;
        //$send->subject = $asunto;
        //$send->body = $body;
        //Mail::to($correo)->send($send);
        return "Mensaje enviado";
        
    }
    
    public function modelocorreo1(){
        //<li>ds</li>
        $html='<b>Estimado (a) Ciudadano (a) |NOMBRE|</b>
        <br>En respuesta al expediente |EXP|
        <br>El certificado a sido observado por:
        <br>|OBS|
        <br>|ADJUNTOS|
        <br><br><b style="background-color:Yellow;">Nota:  Las correcciones lo realiza la Institución Educativa, remitir escaneado el certificado corregido por medio de la <a target="_blank" href="https://ventanillavirtual.ugel01.gob.pe/">ventanilla virtual</a>, para proceder con la visación.</b>';
        return $html;
    }

    public function modelocorreo2(){
        //<li>ds</li>
        $html='<p><b>Estimado (a) Ciudadano (a) |NOMBRE|</b>
        <br>En respuesta al expediente |EXP|
        <br>Se le cita a la Ugel01 de manera presencial para recoger su certificado de estudio:</p>
            <ul>
            <li><b>Fecha: </b>|FECHA|</li>
            <li><b>Hora: </b>|HORA|</li>
            </ul>
            <b>Recuerde:</b>
            <ul>
            <li><b>Respetar el día y hora de la cita</b>, no hay reprogramación ni atención fuera de horario.</li>
            <li>Si el trámite es realizado por un tercero, debe presentar una <b>carta poder simple.</b></li>
            <li>Presente su <b>documento de identidad y su carnet de vacunación con las 2 dosis</b> al momento de asistir a nuestra institución.</li>
            <li><b>Recuerde llevar un lapicero</b> y sus documentos en un sobre o mica transparente.</li>
            </ul>';
        return $html;
    }

    /*
    <p><br><img src="http://octavio.test/assets/images/banner_correo.jpg"></p>
            <p><b>Equipo de Tramite documentario y archivo</b></p>
            <p><b>Área de Administración</b></p>
            <p>Jr. Los Ángeles S/N Pamplona Baja – San Juan de Miraflores – Lima Perú</p>
            <p>Central Telefónica: 017434555 - Consulta WhatsApp 940181723 // 940182207</p>
            <p><a href="http://www.ugel01.gob.pe">http://www.ugel01.gob.pe</a></p>
    */
    /*
    
            <p><br><img src="http://octavio.test/assets/images/banner_correo.jpg"></p>
            <p><b>Equipo de Tramite documentario y archivo</b></p>
            <p><b>Área de Administración</b></p>
            <p>Jr. Los Ángeles S/N Pamplona Baja – San Juan de Miraflores – Lima Perú</p>
            <p>Central Telefónica: 017434555 - Consulta WhatsApp 940181723 // 940182207</p>
            <p><a href="http://www.ugel01.gob.pe">http://www.ugel01.gob.pe</a></p>
    */

}
