<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Form_consolidadotrabajo;
use App\Models\Especialistas;
use App\Models\Form_consolidadotrabajo_dias;
use App\Models\Form_consolidadotrabajo_firmado;
use App\Models\Wts_log_asistencia;
use DB;
use Storage;
//use Maatwebsite\Excel\Facades\Excel;
//use App\Exports\Form_consolidadotrabajoExport;
//use Maatwebsite\Excel\Facades\Excel;

class Trabajoremoto extends Controller{
    
    //REPORTE DE ASISTENCIA
    public function asistenciaoficina(){
        $info['todo'] = 0;
        $info['session'] = session()->get('siic01_admin');
        if($info['session']){
            $info['equipo'] = ($info['session']['id_area']==$info['session']['id_oficina'])?$this->lista_oficinas($info['session']['id_area']):false;
            return view('trabajoremoto/asistenciaoficina',$info);
        }else{
            echo 'Sin session';
        }
    }
    
    public function asistenciaoficina_area(){
        $info['todo'] = 1;
        $info['session'] = session()->get('siic01_admin');
        if($info['session']){
            $info['equipo'] = $this->lista_areas();
            return view('trabajoremoto/asistenciaoficina',$info);
        }else{
            echo 'Sin session';
        }
    }
    
    public function ver_asistenciaoficina(Request $request){
        //dd($request->all());
        $info['verhoras'] = $request['verhoras'];
        $info['todo'] = $request['todo'];
        $where  = '';
        $where .= ($request['idarea'])?" and E.id_area=".$request['idarea']:"";
        $where .= ($request['idequipo'])?" and E.id_oficina=".$request['idequipo']:"";
        
        $verhoras = $request['verhoras'];
        $anio  = $request['anio'];
        $idmes = $request['idmes'];
        $anio  = $request['anio'];
        
        
        $lista = DB::select("SELECT 
        E.idespecialista,
        E.ddni,
        E.cargo,
        E.esp_nombres,
        E.esp_apellido_paterno,
        E.esp_apellido_materno,
        E.regimen_laboral,
        E.especialista_creo,
        E.id_oficina,
        E.id_area,
        Ar.Descripcion as area, 
        Ar.DescripcionCorta as areacorta,
        Eq.Descripcion as equipo,
        Eq.DescripcionCorta as equipocorta
        FROM especialistas E 
        INNER JOIN t_SedeOficina Ar ON E.id_area    = Ar.SedeOficinaId
		INNER JOIN t_SedeOficina Eq ON E.id_oficina = Eq.SedeOficinaId
        WHERE (E.regimen_laboral IS NULL or E.regimen_laboral IN('CAP decreto legislativo 276','CAS decreto legislativo 1057','Gerentes públicos Decreto legislativo 1024','Persona bajo la ley 29944') ) 
        and E.cargo<>'TERCERO' and E.cargo<>'COMITE' and E.estado=1 $where");
        
        for ($i = 0; $i < count($lista); $i++) {
            $ddni = $lista[$i]->ddni;
            $lista[$i]->asistencia =  DB::connection('sicab')->select("SELECT 
            A.fecha, 
            SUM(A.status) as status,
            DATE_FORMAT(MIN(A.fechadate),'%H:%i:%s') entrada, 
            DATE_FORMAT(MAX(A.fechadate),'%H:%i:%s') as salida,".
            (($verhoras)?"TRUNCATE((TIMESTAMPDIFF(MINUTE,IF(DATE_FORMAT(MIN(A.fechadate),'%H%i%s')<80000,DATE_FORMAT(MIN(A.fechadate),'%Y-%m-%d 08:00:00'),MIN(A.fechadate)),MAX(A.fechadate))/60)-IF(DAYOFWEEK(A.fecha) IN(1,7),0,0.75),0) as horas,":"").
            (($verhoras)?"(TIMESTAMPDIFF(MINUTE,IF(DATE_FORMAT(MIN(A.fechadate),'%H%i%s')<80000,DATE_FORMAT(MIN(A.fechadate),'%Y-%m-%d 08:00:00'),MIN(A.fechadate)),MAX(A.fechadate))/60)-IF(DAYOFWEEK(A.fecha) IN(1,7),0,0.75) as min,":"").
            "DAYOFWEEK(A.fecha) as sem
            FROM wts_log_asistencia A 
            INNER JOIN wts_usuarios U ON A.dni = U.pin
            WHERE U.dni='$ddni' and YEAR(A.fecha)=$anio and MONTH(A.fecha)=$idmes and A.estado=1 
            GROUP BY A.fecha");
            //TRUNCATE(TIMESTAMPDIFF(MINUTE,MIN(A.fechadate),MAX(A.fechadate))/60,0) as horassss,
            //TRUNCATE((TIMESTAMPDIFF(MINUTE,IF(DATE_FORMAT(MIN(A.fechadate),'%H%i%s')<80000,DATE_FORMAT(MIN(A.fechadate),'%Y-%m-%d 08:00:00'),MIN(A.fechadate)),MAX(A.fechadate))/60)-IF(DAYOFWEEK(A.fecha) IN(1,7),0,0.75),0) as horas
            
        }
        
        $info['lista'] = $lista;
        
        $info['fechas'] = DB::select(
            "SELECT C.fecha,F.estado as feriado,date_format(C.fecha,'%d') as dia, date_format(C.fecha,'%d/%m/%Y') as textodia, WEEKDAY(C.fecha) as diasemana, CONCAT(ELT(WEEKDAY(C.fecha)+1,'Lunes','Martes','Miercoles','Jueves','Viernes','Sabado','Domingo')) as t_diasemana 
             FROM siic01ugel01gob_formularios.form_licencia_calendario C 
             LEFT JOIN feriados F ON C.fecha = F.fecha and F.estado = 1
             WHERE date_format(C.fecha,'%Y') = '".$request['anio']."' and date_format(C.fecha,'%m') = '".$request['idmes']."'
             ORDER BY C.fecha ASC");
        
        return view('trabajoremoto/ver_asistenciaoficina',$info);
        
    }
    
    
    public function ver_consolidado_trabajo_prueba(Request $request){
        $info['mantenimiento'] = $this->get_consolidadotrabajo($request['anio'],$request['mes'],$request['idarea'],$request['idequipo']);
        if(!$info['mantenimiento']){
            $data = $request->all();
            unset($data['registrar']);
            unset($data['alt']);
            Form_consolidadotrabajo::insert($data);
            $info['mantenimiento'] = $this->get_consolidadotrabajo($request['anio'],$request['mes'],$request['idarea'],$request['idequipo']);
        }
        $info['idtrabajo'] = $info['mantenimiento'][0]['idtrabajo'];

        if($request['registrar']==1){
            $asitencia = $this->consultar_asistencia($request['anio'],$request['idmes'],$request['idarea'],$request['idequipo'],$info['idtrabajo']);
            $info['fechas']   = $asitencia['fechas'];
            $info['registro'] = $asitencia['registro'];
            $info['oficinaspresentaron'] = ($request['idequipo']==$request['idarea'])?Form_consolidadotrabajo::where(['estado'=>1,'culminado'=>1,'anio'=>$request['anio'],'mes'=>$request['mes'],'idarea'=>$request['idarea']])->select("*",DB::raw("DATE_FORMAT(modificado,'%d/%m/%Y') as t_modificado"))->get()->toArray():array();            
        }else{
            if($request['idequipo']<0 or $request['idequipo']==$request['idarea']){$info['mantenimiento'] = $this->get_consolidadotrabajo($request['anio'],$request['mes'],$request['idarea']);}
            if($request['idequipo']==70){$info['mantenimiento'] = $this->get_consolidadotrabajo($request['anio'],$request['mes']);}
        }
        echo json_encode($info);        
    }
    
    //REPORTE DE ASISTENCIA
    
    //CONSOLIDADO TRABAJO
    public function consolidado_trabajo(Request $request){
        $info['session'] = session()->get('siic01_admin');
        if($info['session']){
            $info['equipo'] = ($info['session']['id_area']==$info['session']['id_oficina'])?$this->lista_oficinas($info['session']['id_area']):false;
            return view('trabajoremoto/consolidado_trabajo',$info);
        }else{
            echo 'Sin session';
        }
    }
    
    public function firmar_descargar(Request $request){
        $info['session'] = session()->get('siic01_admin');
        $info['trabajo'] = Form_consolidadotrabajo::where('idtrabajo',$request['idtrabajo'])->get()->toArray()[0];
        return view('trabajoremoto/firmar_descargar',$info);
    }
    
    public function pdfreporteasistencia(Request $request){
        ini_set('memory_limit','256M');
        $trabajo = Form_consolidadotrabajo::where('idtrabajo',$request['idtrabajo'])->get()->toArray();
        $session = false;
        if($trabajo){
            $trabajo = $trabajo[0];
            $mantenimiento   = $this->consultar_asistencia($trabajo['anio'],$trabajo['idmes'],$trabajo['idarea'],$trabajo['idequipo'],$trabajo['idtrabajo'],0,0);
            $pdf = \PDF::loadView('trabajoremoto/reporteequipo', compact('mantenimiento','trabajo','session'));
            return $pdf->setPaper('a4','landscape')->stream('ejemplo.pdf');
        }
   }
   
   public function prueba(){
       $pdf = \PDF::loadView('trabajoremoto/reporteprueba');
       return $pdf->setPaper('a4','landscape')->stream('ejemplo.pdf');
   }
   
    public function pdfreporteasistenciamensual(Request $request){
        date_default_timezone_set('America/Lima');
        ini_set('memory_limit','256M');
        $data = array('estado'=>1,'anio'=>$request['anio'],'idmes'=>$request['idmes'],'idarea'=>$request['idarea'],'regimen_laboral'=>$request['regimen_laboral']);
        $session = false;
        $titulo = $this->textomes($request['idmes']).' '.$request['anio'].' '.(($request['regimen_laboral'])?$request['regimen_laboral']:'');
        $reg = Form_consolidadotrabajo_firmado::where($data)->select('*',DB::raw("DATE_FORMAT(creado_at,'%d/%m/%Y') as creado"))->get()->first();
        $mantenimiento   = $this->consultar_asistencia($request['anio'],$request['idmes'],$request['idarea'],0,0,$request['regimen_laboral'],0);
        $esp1 = false;
        $esp2 = false;
        if($request['firmar']==1){
                $session = session()->get('siic01_admin');
                $dir_subida = 'storage/consolidado_trabajo/'.$request['anio'];
                if (!file_exists($dir_subida)) mkdir($dir_subida, 0777, true);
                $dir_subida = 'storage/consolidado_trabajo/'.$request['anio'].'/'.$this->textomes($request['idmes']);
                if (!file_exists($dir_subida)) mkdir($dir_subida, 0777, true);
                $archivo = $dir_subida."/".rand().date('YmdHis').".pdf";
                $data['docfirmado']  = '/'.$archivo;
                $idfirmado = 0;
                    if($reg){
                        Form_consolidadotrabajo_firmado::where('idfirmado',$reg['idfirmado'])->update(['firmaidesp2'=>$session['idespecialista'],'docfirmado'=>$archivo]);
                        $idfirmado = $reg['idfirmado'];
                    }else{
                        $data['firmaidesp1'] = $session['idespecialista'];
                        $idfirmado = Form_consolidadotrabajo_firmado::insertGetId($data);
                    }
                    //Obtener firmas
                    $reg = Form_consolidadotrabajo_firmado::where('idfirmado',$idfirmado)->select('*',DB::raw("DATE_FORMAT(creado_at,'%d/%m/%Y') as creado"))->get()->first();
                    $esp1 = Especialistas::where('idespecialista',$reg['firmaidesp1'])->select('firma','visto')->get()->first();
                    $esp2 = Especialistas::where('idespecialista',$reg['firmaidesp2'])->select('firma','visto')->get()->first();
                    //Obtener firmas
                $pdf = \PDF::loadView('trabajoremoto/reporteequipomensual', compact('mantenimiento','session','titulo','esp1','esp2'));
                $pdf->setPaper('a4','landscape')->save($archivo);
                return redirect($archivo);
            }else{
                if($reg){
                    return redirect($reg['docfirmado']);
                }else{
                $pdf = \PDF::loadView('trabajoremoto/reporteequipomensual', compact('mantenimiento','session','titulo','esp1','esp2'));
                return $pdf->setPaper('a4','landscape')->stream('ejemplo.pdf');
                }
            }
    }
    
    public function textomes($mes){
       $texto = '';
       switch ($mes) {
            case '01':$texto = 'ENERO';break;
            case '02':$texto = 'FEBRERO';break;
            case '03':$texto = 'MARZO';break;
            case '04':$texto = 'ABRIL';break;
            case '05':$texto = 'MAYO';break;
            case '06':$texto = 'JUNIO';break;
            case '07':$texto = 'JULIO';break;
            case '08':$texto = 'AGOSTO';break;
            case '09':$texto = 'SEPTIEMBRE';break;
            case '10':$texto = 'OCTUBRE';break;
            case '11':$texto = 'NOVIEMBRE';break;
            case '12':$texto = 'DICIEMBRE';break;
            default:break;
        }
        return $texto;
   }
   
   public function subirfirma(Request $request){
       $session = session()->get('siic01_admin');
       if($request->hasfile('txtfirma')){
            $archivo = $request->file('txtfirma')->store('public/firmas/');
            if ($archivo) {
                Especialistas::where('idespecialista',$session['idespecialista'])->update(['firma'=>Storage::url($archivo)]);
                $session['firma'] = Storage::url($archivo);
                $request->session()->put('siic01_admin',$session);
                echo json_encode(Storage::url($archivo));
            }else{
                echo json_encode(0);
            }
        }
        
        if($request->hasfile('txtvisto')){
            $archivo = $request->file('txtvisto')->store('public/vistos/');
            if ($archivo) {
                Especialistas::where('idespecialista',$session['idespecialista'])->update(['visto'=>Storage::url($archivo)]);
                $session['visto'] = Storage::url($archivo);
                $request->session()->put('siic01_admin',$session);
                echo json_encode(Storage::url($archivo));
            }else{
                echo json_encode(0);
            }
        }
   }
   
   public function firmarreporteasistencia(Request $request){
       date_default_timezone_set('America/Lima');
       ini_set('memory_limit','256M');
       $session = session()->get('siic01_admin');
       $trabajo = Form_consolidadotrabajo::where('idtrabajo',$request['idtrabajo'])->get()->toArray();
        if($trabajo){
            $trabajo = $trabajo[0];
            $mantenimiento   = $this->consultar_asistencia($trabajo['anio'],$trabajo['idmes'],$trabajo['idarea'],$trabajo['idequipo'],$trabajo['idtrabajo'],0,0);
            $pdf = \PDF::loadView('trabajoremoto/reporteequipo', compact('mantenimiento','trabajo','session'));
            
            $dir_subida = 'storage/consolidado_trabajo/'.$request['txt_anio'];
            if (!file_exists($dir_subida)) mkdir($dir_subida, 0777, true);
            
            $dir_subida = 'storage/consolidado_trabajo/'.$request['txt_anio'].'/'.$request['txt_mes'];
            if (!file_exists($dir_subida)) mkdir($dir_subida, 0777, true);
            
            $dir_subida = 'storage/consolidado_trabajo/'.$request['txt_anio'].'/'.$request['txt_mes'].'/'.$request['txt_areacorta'];
            if (!file_exists($dir_subida)) mkdir($dir_subida, 0777, true);
            
            $archivo = $dir_subida."/".rand().date('YmdHis').".pdf";
            
            Form_consolidadotrabajo::where('idtrabajo',$request['idtrabajo'])->update(['docfirmado'=>'/'.$archivo]);
            
            $pdf->setPaper('a4','landscape')->save($archivo);
            
        }
        echo json_encode(1);
   }
    
    public function excel_consolidado_trabajo(){
        //echo 'excel';
        $export = new Form_consolidadotrabajoExport;
        $export->where = array('estado'=>1);
        return Excel::download($export,'user-list.xlsx');
        /*Excel::create('Laravel Excel', function($excel) {
            $excel->sheet('Excel sheet', function($sheet) {
                $mantenimiento = Form_consolidadotrabajo::where(['estado'=>1])->get()->toArray();
                $sheet->formArray();
            });
        })->export('xls');
        */
    }

    public function ver_consolidado_trabajo(Request $request){
        $info['mantenimiento'] = $this->get_consolidadotrabajo($request['anio'],$request['mes'],$request['idarea'],$request['idequipo']);
        if(!$info['mantenimiento']){
            $data = $request->all();
            unset($data['registrar']);
            unset($data['alt']);
            Form_consolidadotrabajo::insert($data);
            $info['mantenimiento'] = $this->get_consolidadotrabajo($request['anio'],$request['mes'],$request['idarea'],$request['idequipo']);
        }
        $info['idtrabajo'] = $info['mantenimiento'][0]['idtrabajo'];

        if($request['registrar']==1){
            $asitencia = $this->consultar_asistencia($request['anio'],$request['idmes'],$request['idarea'],$request['idequipo'],$info['idtrabajo']);
            $info['fechas']   = $asitencia['fechas'];
            $info['registro'] = $asitencia['registro'];
            $info['oficinaspresentaron'] = ($request['idequipo']==$request['idarea'])?Form_consolidadotrabajo::where(['estado'=>1,'culminado'=>1,'anio'=>$request['anio'],'mes'=>$request['mes'],'idarea'=>$request['idarea']])->select("*",DB::raw("DATE_FORMAT(modificado,'%d/%m/%Y') as t_modificado"))->get()->toArray():array();            
        }else{
            if($request['idequipo']<0 or $request['idequipo']==$request['idarea']){$info['mantenimiento'] = $this->get_consolidadotrabajo($request['anio'],$request['mes'],$request['idarea']);}
            if($request['idequipo']==70){$info['mantenimiento'] = $this->get_consolidadotrabajo($request['anio'],$request['mes']);}
        }
        echo json_encode($info);        
    }
    
    public function consultar_asistencia($anio,$idmes,$idarea=false,$idequipo=false,$idtrabajo=false,$regimen_laboral=false,$left=1){
        $info['fechas'] = DB::select(
            "SELECT C.fecha,F.estado as feriado,date_format(C.fecha,'%d') as dia, date_format(C.fecha,'%d/%m/%Y') as textodia, WEEKDAY(C.fecha) as diasemana, CONCAT(ELT(WEEKDAY(C.fecha)+1,'Lunes','Martes','Miercoles','Jueves','Viernes','Sabado','Domingo')) as t_diasemana 
             FROM siic01ugel01gob_formularios.form_licencia_calendario C 
             LEFT JOIN feriados F ON C.fecha = F.fecha and F.estado = 1
             WHERE date_format(C.fecha,'%Y') = '".$anio."' and date_format(C.fecha,'%m') = '".$idmes."'
             ORDER BY C.fecha ASC");
        $select = "";
        if($info['fechas']){
            for ($i=0; $i < count($info['fechas']); $i++) {
                $key = $info['fechas'][$i];
                $select .= "MAX(IF(D.fecha='".$key->fecha."',D.reg,null)) as fila".$i.",";
            }
        }
        $where  = "";
        $where .= ($left)?" and E.estado=1 ":"";
        $where .= ($idarea)?" and E.id_area=$idarea ":"";
        $where .= ($idequipo)?" and E.id_oficina=$idequipo ":"";
        $where .= ($regimen_laboral)?" and E.regimen_laboral='$regimen_laboral' ":"";
        //IF(  SUM(IF(D.reg='R' OR D.reg='P',1,0)) = SUM(IF(D.reg='P',1,0)),'PRESENCIAL', IF(  SUM(IF(D.reg='R' OR D.reg='P',1,0)) = SUM(IF(D.reg='R',1,0)),'REMOTO', GROUP_CONCAT(DISTINCT(IF(D.reg='S' or D.reg='D' or D.reg='-',NULL,D.reg)))   )    )   as t_trabajoremoto
        //SUM(IF(D.reg='R' OR D.reg='P' OR D.reg='M' OR D.reg='V' OR D.reg='O',1,0)) as total,
        //SUM(IF(D.reg='R' OR D.reg='P' OR D.reg='M' OR D.reg='V' OR D.reg='O',1,0))*8 as totalhoras,
        //IF(SUM(IF( D.reg IN('LSG','N','-','') OR D.reg IS NULL,0,1)) >30,30,SUM(IF( D.reg IN('LSG','N','-','') OR D.reg IS NULL,0,1))) as total,
        //IF(SUM(IF( D.reg IN('LSG','N','-','') OR D.reg IS NULL,0,1)) >30,30,SUM(IF( D.reg IN('LSG','N','-','') OR D.reg IS NULL,0,1)))*8 as totalhoras,
        $info['registro'] = DB::select("
        SELECT 
        E.idespecialista,
        E.ddni,
        E.cargo,
        E.esp_nombres,
        E.esp_apellido_paterno,
        E.esp_apellido_materno,
        E.regimen_laboral,
        E.especialista_creo,
        E.id_oficina,
        E.id_area,
        Ar.Descripcion as area, 
        Ar.DescripcionCorta as areacorta,
        Eq.Descripcion as equipo,
        Eq.DescripcionCorta as equipocorta,
        $select 
        (30 - SUM(IF( D.reg IN('LSG','N','','-') OR D.reg IS NULL,1,0)) )   as total,
        (30 - SUM(IF( D.reg IN('LSG','N','','-') OR D.reg IS NULL,1,0)) )*8 as totalhoras,
        SUM(IF( D.reg IN('LSG','N','') OR D.reg IS NULL,1,0)) as descuento,
        SUM(IF(D.reg='R',1,0)) as remoto,
        SUM(IF(D.reg='P',1,0)) as presencia,
        SUM(IF(D.reg='M',1,0)) as mixto,
        GROUP_CONCAT(DISTINCT(IF(D.reg='S' or D.reg='D' or D.reg='-',NULL,D.reg)))   as t_trabajoremoto
        FROM especialistas E 
        INNER JOIN t_SedeOficina Ar ON E.id_area    = Ar.SedeOficinaId
		INNER JOIN t_SedeOficina Eq ON E.id_oficina = Eq.SedeOficinaId
        ".(($left)?"LEFT":"INNER")." JOIN siic01ugel01gob_formularios.form_consolidadotrabajo_dias D ON E.idespecialista = D.idespecialista and D.estado=1 ".(($idtrabajo)?"and D.idtrabajo=$idtrabajo":"").(($idmes)?" and date_format(D.fecha,'%m')='$idmes'":"").(($anio)?" and date_format(D.fecha,'%Y')='$anio'":"")."
        WHERE (E.regimen_laboral IS NULL or E.regimen_laboral <> 'PERMISO' and E.regimen_laboral <> 'LOCADOR' and E.regimen_laboral <> 'DIRECTOR') and E.cargo<>'TERCERO' and E.cargo<>'COMITE' $where 
        GROUP BY E.idespecialista,E.ddni,E.cargo,E.esp_nombres,E.esp_apellido_paterno,E.esp_apellido_materno,E.regimen_laboral,E.especialista_creo,E.id_oficina,E.id_area,Ar.Descripcion,Ar.DescripcionCorta,Eq.Descripcion,Eq.DescripcionCorta");
        return $info;
    }

    public function reporte_consolidado_trabajo(Request $request){
        if(session()->get('siic01_admin')){
        $info = $this->consultar_asistencia($request['anio'],$request['idmes'],0,0,0,0,0);
        $info['session'] = session()->get('siic01_admin');
        $info['anio']  = $request['anio'];
        $info['idmes'] = $request['idmes'];
        $info['areas'] = DB::connection('formularios')->select("SELECT idarea,area FROM form_consolidadotrabajo WHERE anio='".$request['anio']."' and idmes='".$request['idmes']."' and estado=1 and culminado=1 GROUP BY idarea,area");
        $info['culminado'] = DB::connection('formularios')->select("SELECT idtrabajo,docfirmado,idarea,areacorta,area,equipo FROM form_consolidadotrabajo WHERE anio='".$request['anio']."' and idmes='".$request['idmes']."' and estado=1 and culminado=1 GROUP BY idtrabajo,docfirmado,idarea,areacorta,area,equipo");
        return view('trabajoremoto/reporte',$info);
        }
    }

    public function guardar_consolidadotrabajo_dias(Request $request){
        date_default_timezone_set('America/Lima');
        $request['idespecialista'] = explode(',',$request['idespecialista']);
        $request['fecha']          = explode(',',$request['fecha']);
        $request['reg']            = explode(',',$request['reg']);
        Form_consolidadotrabajo::where('idtrabajo',$request['idtrabajo'])->update(['culminado'=>1]);
        for ($i=0; $i < count($request['fecha']); $i++) {
            $data = array();       
            $data['idtrabajo']      = $request['idtrabajo'];
            $data['idespecialista'] = $request['idespecialista'][$i];
            $data['fecha']          = $request['fecha'][$i];
            $data['reg']            = $request['reg'][$i];
            $reg = Form_consolidadotrabajo_dias::where(['estado'=>1,'idespecialista'=>$data['idespecialista'],'fecha'=>$data['fecha']])->get()->first();
            if($reg){
                Form_consolidadotrabajo_dias::where('idtrabajodias',$reg->idtrabajodias)->update($data);
            }else{
                Form_consolidadotrabajo_dias::insert($data);
            }
        }
        echo json_encode(1);
    }


    public function guardar_consolidado_trabajo(Request $request){
        date_default_timezone_set('America/Lima');
        if($request->hasfile('archivopdf')){
            $archivo = $request->file('archivopdf')->store('public/consolidado_trabajo/'.$request['txt_anio'].'/'.$request['txt_mes']);
            if ($archivo) {
                $doc = Form_consolidadotrabajo::where(['idtrabajo'=>$request['idmantenimiento']])->get()->first();                
                if($doc[$request['campo']]){                    
                    rename(str_replace('/storage','storage',$doc[$request['campo']]),str_replace('/storage','storage',$doc[$request['campo']]).str_replace(' ','-','--id-'.$doc['idtrabajo'].'-'.$request['campo'].'-Eliminado por '.$request['especialista']));
                }
                Form_consolidadotrabajo::where(['idtrabajo'=>$request['idmantenimiento']])->update([$request['campo']=>Storage::url($archivo)]);
                echo json_encode(array('tipo'=>1,'msj'=>'Archivo subido','cantidad'=>'' ,'url'=>Storage::url($archivo)));
            }else{
                echo json_encode(array('tipo'=>2,'msj'=>'No se ha podido subir, recargue la página e inténtelo de nuevo','url'=>''));
            }
        }else{
            echo json_encode(array('tipo'=>2,'msj'=>'No se ha podido subir, recargue la página e inténtelo de nuevo','url'=>''));
        }
    }   

    public function get_consolidadotrabajo($anio=false,$mes='',$idarea=false,$idequipo=false){
        $where['estado']=1;
        if($anio)     $where['anio']     = $anio;
        if($mes)      $where['mes']      = $mes;
        if($idarea)   $where['idarea']   = $idarea;
        if($idequipo) $where['idequipo'] = $idequipo;
        $mantenimiento = Form_consolidadotrabajo::where($where)->select("*",DB::raw("DATE_FORMAT(modificado,'%d/%m/%Y') as t_modificado"))->get()->toArray();
        if($mantenimiento){
            return $mantenimiento;
        }else{
            return false;
        }
    }

    public function lista_oficinas($id = 62){
		$query = DB::select("SELECT Nivel,SedeOficinaId,Descripcion,DescripcionCorta,PadreSedeOficinaId from t_SedeOficina where SedeOficinaId = $id or (Estado = 1 and Descripcion LIKE '%EQUIPO%' and PadreSedeOficinaId = $id)");
		return $query;
	}
    
    public function lista_areas($id = 62){
		$query = DB::select("SELECT Nivel,SedeOficinaId,Descripcion,DescripcionCorta,PadreSedeOficinaId from t_SedeOficina where SedeOficinaId = $id or (Estado = 1 and PadreSedeOficinaId = $id)");
		return $query;
	}
	
    public function listardir(){
        $texto = '/storage/app/public/consolidado_trabajo/2021/NOVIEMBRE/sMzYYvaqiOp6O4M7f4W55GwZzzbD8ooAUUwzAOZ1.pdf';
        echo substr($texto,1,strlen($texto));
    }
//CONSOLIDADO TRABAJO
}
