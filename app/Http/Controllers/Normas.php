<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Form_normas;
use App\Models\Form_normas_temas;
use App\Models\Form_normas_tipos;
use App\Models\Form_normas_situaciones;
use App\Models\Form_normas_logconsultas;
use App\Models\Form_normas_entes;
use Storage;
use DB;

class Normas extends Controller
{
    public function buscarnormas(){
        $info['entidades'] = Form_normas_entes::where('estEnt',1)->get()->toArray();
        $info['temas']     = Form_normas_temas::where('estTem',1)->get()->toArray();
        $info['tipos']     = Form_normas_tipos::where('estTip',1)->get()->toArray();
        $info['situacion'] = Form_normas_situaciones::where('estSit',1)->get()->toArray();
        $info['ip'] = $this->getRealIP();
        $info['anios'] = $this->normasanios();
        return view('normas/buscador',$info);
    }

    public function nuevanorma(){
        $info['entidades'] = Form_normas_entes::where('estEnt',1)->get()->toArray();
        $info['temas']     = Form_normas_temas::where('estTem',1)->get()->toArray();
        $info['tipos']     = Form_normas_tipos::where('estTip',1)->get()->toArray();
        $info['situacion'] = Form_normas_situaciones::where('estSit',1)->get()->toArray();
        $info['anios'] = $this->normasanios();
        
        return view('normas/nuevanorma',$info);
    }   

    public function normasanios(){
        $query = DB::connection('formularios')->select("SELECT DATE_FORMAT(fecFnn,'%Y') anio FROM `form_normas` WHERE estFnn=1 GROUP BY DATE_FORMAT(fecFnn,'%Y')");
        $result = array();
        foreach ($query as $key){
            $result[] = (Array)$key;
        }
        return $result;
    }

    public function cantidadnormastipos(){
        $query = DB::connection('formularios')->select("SELECT T.desTip,count(*) as cantidad FROM form_normas N 
        INNER JOIN form_normas_tipos T ON N.idTip=T.idTip
        WHERE N.estFnn=1 and T.estTip=1
        GROUP BY T.idTip");
        $result = array();
        foreach ($query as $key){
            $result[] = (Array)$key;
        }
        return $result;
    }

    public function popup_anadirnorma(Request $request){
        $info['entidades'] = Form_normas_entes::where('estEnt',1)->get()->toArray();
        $info['temas']     = Form_normas_temas::where('estTem',1)->get()->toArray();
        $info['tipos']     = Form_normas_tipos::where('estTip',1)->get()->toArray();
        $info['situacion'] = Form_normas_situaciones::where('estSit',1)->get()->toArray();
        $info['data']      = Form_normas::where('idFnn',$request['idFnn'])->get()->toArray();
        $info['data']      = ($info['data'])?$info['data'][0]:false;
        return view('normas/popup_anadirnorma',$info);
    }

    public function eliminarnorma(Request $request){
        Form_normas::where('idFnn',$request['idFnn'])->update(['estFnn'=>0]);
        echo json_encode(1);
    }

    public function guardarnorma(Request $request){
        $data['idTem']  = $request['idTem'];
        $data['AsuFnn'] = $request['AsuFnn'];
        $data['nroFnn'] = $request['nroFnn'];
        $data['fecFnn'] = $request['fecFnn'];
        $data['idTip']  = $request['idTip'];
        $data['palClaFnn'] = $request['palClaFnn'];
        $data['idSit'] = $request['idSit'];
        $data['idEnt'] = $request['idEnt'];
        $data['arcLinFnn'] = $request['rtipo'];
        if($request['rtipo']==2){ $data['arcFnn'] = $request['arcFnnLink']; }
        
        if($request->hasfile('arcFnn')){
        $archivo = $request->file('arcFnn')->store('public/normas/'.date('Y').'/tem'.$data['idTem']);
        $data['arcFnn'] = Storage::url($archivo);
        }

        if($request['idFnn']){
            Form_normas::where('idFnn',$request['idFnn'])->update($data);
        }else{
            Form_normas::insert($data);
        }
        echo json_encode(1);
    }

    public function normanbuscarnroFnn(Request $request){
        //'nroFnn'=>$request['nroFnn']
        echo json_encode(Form_normas::where('estFnn',1)->where('nroFnn','like','%'.$request['nroFnn'].'%')->select('nroFnn')->get()->toArray());
    }

    public function normasrepositorio(Request $request){
        //ini_set('memory_limit','800M');
    	//ini_set('max_excution_time',0);
        $where  = "";
        $where .= ($request['buscar'])?"and ( T.desTem like '%".$request['buscar']."%' or N.AsuFnn like '%".$request['buscar']."%' or N.desFnn like '%".$request['buscar']."%' or N.insFnn like '%".$request['buscar']."%' or N.nroFnn like '%".$request['buscar']."%' or I.desTip like '%".$request['buscar']."%' or N.palClaFnn like '%".$request['buscar']."%' or S.desSit like '%".$request['buscar']."%' or E.desEnt like '%".$request['buscar']."%' or fecFnn like '%".$request['buscar']."%' )":"";
        $orwhere = array();
        if($request['idTem'])    { $orwhere[] = " N.idTem = '".$request['idTem']."'"; }
        if($request['AsuFnn'])   { $orwhere[] = "(N.AsuFnn like '%".$request['AsuFnn']."%' or N.desFnn like '%".$request['AsuFnn']."%')"; }
        if($request['nroFnn'])   { $orwhere[] = " N.nroFnn like '%".$request['nroFnn']."%'"; }
        if($request['idEnt'])    { $orwhere[] = " N.idEnt = '".$request['idEnt']."'"; }
        if($request['idTip'])    { $orwhere[] = " N.idTip = '".$request['idTip']."'"; }
        if($request['palClaFnn']){ $orwhere[] = " N.palClaFnn like '%".$request['palClaFnn']."%'"; }
        if($request['idSit'])    { $orwhere[] = " N.idSit = '".$request['idSit']."'"; }
        if($request['fecFnn'])   { $orwhere[] = " fecFnn like '%".$request['fecFnn']."%'"; }
        $where .= ($orwhere)?" and (".implode(" and ",$orwhere).")":"";

        $data = DB::connection('formularios')->select("SELECT 
        E.desEnt, N.idFnn,N.desFnn, T.desTem, N.AsuFnn, N.nroFnn, IF(DATE_FORMAT(N.fecFnn,'%Y')='2019',2019,DATE_FORMAT(N.fecFnn,'%d/%m/%Y')) as fecFnn, I.desTip, S.desSit, N.palClaFnn, N.arcFnn, N.idSit ,N.idTip, N.idTem, N.estFnn,N.arcLinFnn, 0 as cantidad
        FROM form_normas N 
        INNER JOIN form_normas_temas T ON N.idTem = T.idTem
        INNER JOIN form_normas_tipos I ON N.idTip = I.idTip
        INNER JOIN form_normas_situaciones S ON N.idSit = S.idSit
        INNER JOIN form_normas_entes       E ON N.idEnt = E.idEnt
        
        WHERE N.estFnn=1 ".$where."
        GROUP BY E.desEnt, N.idFnn,N.desFnn, T.desTem, N.AsuFnn, N.nroFnn, N.fecFnn, I.desTip, S.desSit, N.palClaFnn, N.arcFnn, N.idSit ,N.idTip, N.idTem, N.estFnn, N.arcLinFnn
        ORDER BY N.fecFnn DESC
        LIMIT 300");
        
        if(!count($data)){ 
            //$data = array('11','333');
            $data = $this->consultaarchivo($request['buscar'],$request['tipo'],$request['nroFnn'],$request['fecFnn']);  
        }
        //LEFT  JOIN 	form_normas_logconsultas L ON N.idFnn = L.idFnn     COUNT(idFnl)
        //$this->consultaarchivo($request['tipo'],$request['nroFnn'],$request['fecFnn']); 
        echo json_encode($data);

    }
    
    public function consultaarchivo($texto='',$tipo='RD',$nro=10498,$anio=2020){
        //echo $tipo.' '.$nro.'-'.$anio;
        //$tipo = 'RD';
        //$nro  = 10498;
        //$anio = 2020;
        //ini_set('memory_limit','800M');
		//ini_set('max_excution_time',1800);
		//API URL
		//Contingencia, si la extranet deja de funcionar o firewall
		$url = "http://extranet.ugel01.gob.pe/rd/consultaarchivo.php";
		//$url = "http://200.123.19.250:8060/rd/consultaarchivo.php";
		//echo $url;
		
		//inicializamos el objeto CUrl
		$ch = curl_init($url);
		//el json simulamos una petici贸n de un login
		$jsonData = array('texto'=>$texto,'tipo'=>$tipo,'nro'=>$nro,'anio'=>$anio);
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
		//echo curl_exec($ch);
		$result = json_decode(curl_exec($ch));
		return $result;
		//print_r($result);
		curl_close($ch);
		//return $result;
    }

    public function normastemas(){
        echo json_encode(DB::connection('formularios')->select("SELECT S.idTem,S.estTem,S.desTem,COUNT(DISTINCT(N.nroFnn)) as cantidad FROM form_normas_temas S LEFT JOIN form_normas N ON S.idTem = N.idTem and N.estFnn=1 WHERE S.estTem=1 GROUP BY S.idTem,S.estTem,S.desTem"));
        //echo json_encode(Form_normas_temas::where('estTem',1)->get()->toArray());
    }

    public function guardarnormastemas(Request $request){
        date_default_timezone_set('America/Lima');
        $r_datos = array();
        if($request['datos']){
            $datos = explode("&&",$request['datos']);
            if($datos){
                for ($i=0; $i < count($datos); $i++) {
                        $key = explode("||",$datos[$i]);
                        $ins = array('desTem'=>$key[2]);
                        if($key[0]){
                            if($key[1]=='ELIMINAR'){$ins['estTem'] = 0;}
                            Form_normas_temas::where('idTem',$key[0])->update($ins);
                        }else{
                            if($ins['desTem']) Form_normas_temas::insert($ins);
                        }
                    }
            }
        }
        echo json_encode(1);
    }

    public function normasentidades(){
        echo json_encode(DB::connection('formularios')->select("SELECT E.idEnt,E.estEnt,E.desEnt,COUNT(DISTINCT(N.nroFnn)) as cantidad FROM form_normas_entes E LEFT JOIN form_normas N ON E.idEnt = N.idEnt and N.estFnn=1 WHERE E.estEnt=1 GROUP BY E.idEnt,E.estEnt,E.desEnt"));
    }

    public function guardarnormasentidades(Request $request){
        date_default_timezone_set('America/Lima');
        $r_datos = array();
        if($request['datos']){
            $datos = explode("&&",$request['datos']);
            if($datos){
                for ($i=0; $i < count($datos); $i++) {
                        $key = explode("||",$datos[$i]);
                        $ins = array('desEnt'=>$key[2]);
                        if($key[0]){
                            if($key[1]=='ELIMINAR'){$ins['estEnt'] = 0;}
                            Form_normas_entes::where('idEnt',$key[0])->update($ins);
                        }else{
                            if($ins['desEnt']) Form_normas_entes::insert($ins);
                        }
                    }
            }
        }
        echo json_encode(1);
    }

    public function normastipos(){
        echo json_encode(DB::connection('formularios')->select("SELECT T.idTip,T.estTip,T.desTip,COUNT(DISTINCT(N.nroFnn)) as cantidad FROM form_normas_tipos T LEFT JOIN form_normas N ON T.idTip = N.idTip and N.estFnn=1 WHERE T.estTip=1 GROUP BY T.idTip,T.estTip,T.desTip"));
        //echo json_encode(Form_normas_tipos::where('estTip',1)->get()->toArray());
    }

    public function guardarnormastipos(Request $request){
        date_default_timezone_set('America/Lima');
        $r_datos = array();
        if($request['datos']){
            $datos = explode("&&",$request['datos']);
            if($datos){
                for ($i=0; $i < count($datos); $i++) {
                        $key = explode("||",$datos[$i]);
                        $ins = array('desTip'=>$key[2]);
                        if($key[0]){
                            if($key[1]=='ELIMINAR'){$ins['estTip'] = 0;}
                            Form_normas_tipos::where('idTip',$key[0])->update($ins);
                        }else{
                            if($ins['desTip']) Form_normas_tipos::insert($ins);
                        }
                    }
            }
        }
        echo json_encode(1);
    }

    public function normassituacion(){
        echo json_encode(DB::connection('formularios')->select("SELECT S.idSit,S.estSit,S.desSit,COUNT(DISTINCT(N.nroFnn)) as cantidad FROM form_normas_situaciones S LEFT JOIN form_normas N ON S.idSit = N.idSit and N.estFnn=1 WHERE S.estSit=1 GROUP BY S.idSit,S.estSit,S.desSit"));
        //echo json_encode(Form_normas_situaciones::where('estSit',1)->get()->toArray());
    }

    public function guardarnormassituacion(Request $request){
        date_default_timezone_set('America/Lima');
        $r_datos = array();
        if($request['datos']){
            $datos = explode("&&",$request['datos']);
            if($datos){
                for ($i=0; $i < count($datos); $i++) {
                        $key = explode("||",$datos[$i]);
                        $ins = array('desSit'=>$key[2]);
                        if($key[0]){
                            if($key[1]=='ELIMINAR'){$ins['estSit'] = 0;}
                            Form_normas_situaciones::where('idSit',$key[0])->update($ins);
                        }else{
                            if($ins['desSit']) Form_normas_situaciones::insert($ins);
                        }
                    }
            }
        }
        echo json_encode(1);
    }

    public function normalink(Request $request){
        $data['idFnn'] = $request['idFnn'];
        $data['ipFnl'] = $this->getRealIP();
        Form_normas_logconsultas::insert($data);
        return Redirect($request['link']);
    }

    function getRealIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
            return $_SERVER['HTTP_CLIENT_IP'];
           
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
       //echo $_SERVER['REMOTE_ADDR'];
        return $_SERVER['REMOTE_ADDR'];
    }
}
