<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Boletacesantearchivo;
use App\Models\Boletacesantelista;
use App\Models\Documento;
use App\Models\Documentoprueba;
use App\Models\Especialistas;
use Storage;
use ZipArchive;
use ftp_connect;
use DB;

class Boletacesante extends Controller
{
    function index(){
        if(session()->get('siic01_admin')){
        $info['session'] = session()->get('siic01_admin');
        return view('boletacesante/index',$info);
        }else{
            echo 'Sesion terminada';
        }
    }    

    function subirarchivoboleta(Request $request){
        ini_set('memory_limit','556M');
        ini_set('max_execution_time', 0);
        ini_set('upload_max_filesize','556M');
        ini_set('post_max_size','556M');
        if($request->hasfile('archivo')){
        //if(true){
            $archivo = $request->file('archivo')->store('public/archivolist');
            $ruta = Storage::url($archivo);
            //$ruta='/storage/archivolist/1921_rem0010B.txt';
            $data['anoBca']   = $request['anio'];
            $data['idmesBca'] = $request['mes'];
            $data['mesBca']   = $request['textmes'];
            $data['tipBca']   = $request['tipo'];
            $data['rutBca']   = $ruta;
            $idBca = Boletacesantearchivo::insertGetId($data);
            $texto = '';
            $fp = fopen('.'.$ruta, "r");
            while (!feof($fp)){
                $linea = fgets($fp);
                if(strpos($linea,'MINISTERIO DE EDUCACION')>-1){
                    if(strlen($texto)>20){
                    //echo $texto;
                    $lista['idBca']   = $idBca;
                    $lista['textBcl'] = utf8_encode($texto);
                    Boletacesantelista::insert($lista);
                    $lista=array();
                    //echo '<br>----------FIN----------'.strlen($texto);
                    //echo '<br>----------INICIO----------';
                    }
                    $texto = $linea;
                }else{
                    if(strpos($linea,'Apellidos')>-1)              $lista['apeBcl']  = utf8_encode( trim(str_replace(':','',str_replace('Apellidos','',$linea))) );
                    if(strpos($linea,'Nombres')>-1)                $lista['nomBcl']  = utf8_encode( trim(str_replace(':','',str_replace('Nombres','',$linea))) );
                    if(strpos($linea,'Fecha de Nacimiento')>-1)    $lista['fecBcl']  = utf8_encode( trim(str_replace(':','',str_replace('Fecha de Nacimiento','',$linea))) );
                    if(strpos($linea,'Documento de Identidad')>-1) $lista['docBcl']  = utf8_encode( trim(str_replace(':','',str_replace('Documento de Identidad','',str_replace('(DNI o LE)','',str_replace('(DNI o L.E.)','',$linea))))));
                    if(strpos($linea,'Cargo')>-1)                         $lista['carBcl']     = utf8_encode( trim(str_replace(':','',str_replace('Cargo','',$linea))) );
                    if(strpos($linea,'Tipo de Pensionista')>-1)           $lista['tipBcl']     = utf8_encode( trim(str_replace(':','',str_replace('Tipo de Pensionista','',$linea))) );
                    if(strpos($linea,'Tipo de Pension')>-1)               $lista['tipoPenBcl'] = utf8_encode( trim(str_replace(':','',str_replace('Tipo de Pension','',$linea))) );
                    if(strpos($linea,'Niv.Mag./G.Ocup./Horas/HrsAdd')>-1) $lista['nivMagBcl']  = utf8_encode( trim(str_replace(':','',str_replace('Niv.Mag./G.Ocup./Horas/HrsAdd','',$linea))) );
                    if(strpos($linea,'Tiempo de Servicio (AA-MM-DD)')>-1) $lista['tieSerBcl']  = utf8_encode( trim(str_replace(':','',str_replace('Tiempo de Servicio (AA-MM-DD)','',$linea))) );
                    if(strpos($linea,'Fecha de Registro')>-1)             $lista['fecRegBcl']  = utf8_encode( trim(str_replace(':','',str_replace('Fecha de Registro','',$linea))) );
                    if(strpos($linea,'Cta. TeleAhorro o Nro. Cheque')>-1) $lista['ctaBcl']     = utf8_encode( trim(str_replace(':','',str_replace('Cta. TeleAhorro o Nro. Cheque','',$linea))) );
                    if(strpos($linea,'Leyenda Permanente')>-1)            $lista['leyPerBcl']  = utf8_encode( trim(str_replace(':','',str_replace('Leyenda Permanente','',$linea))) );

                    if(strpos($linea,'T-REMUN')>-1 and strpos($linea,'T-DSCTO')>-1) $lista['tremBcl']  = utf8_encode( trim( str_replace(':','',str_replace('T-REMUN','',substr($linea,0,strpos($linea,'T-DSCTO'))   ))  ) );
                    if(strpos($linea,'T-DSCTO')>-1 and strpos($linea,'T-LIQUI')>-1) $lista['tdscBcl']  = utf8_encode( trim( str_replace(':','',str_replace('T-DSCTO','',substr($linea,strpos($linea,'T-DSCTO'),strpos($linea,'T-LIQUI')-strpos($linea,'T-DSCTO'))   ))  ) );
                    if(strpos($linea,'T-LIQUI')>-1)                                 $lista['tliquBcl'] = utf8_encode( trim( str_replace(':','',str_replace('T-LIQUI','',substr($linea,strpos($linea,'T-LIQUI'),strlen($linea)-strpos($linea,'T-LIQUI'))   ))  ) );
                    if(strpos($linea,'MImponible')>-1)            $lista['mimpBcl']  = utf8_encode( trim(str_replace(':','',str_replace('MImponible','',$linea))) );

                    $texto .= $linea;
                }
            }
            //echo $texto;
            $lista['idBca']   = $idBca;
            $lista['textBcl'] = utf8_encode($texto);
            Boletacesantelista::insert($lista);
            DB::connection('notificacion')->select("UPDATE boletacesantelista SET tipoPenBcl = REPLACE(REPLACE(tipoPenBcl,'INFORMACI¾N','INFORMACIÓN'),'PENSI¾N','PENSIÓN')");
            DB::connection('notificacion')->select("UPDATE boletacesantelista SET textBcl = REPLACE(REPLACE(textBcl,'INFORMACI¾N','INFORMACIÓN'),'PENSI¾N','PENSIÓN')");
            //echo '<br>----------FIN----------';
            fclose($fp);
            echo json_encode(1);
            //$data['textAbc'] = utf8_encode($texto);
            //print_r($data);
            //Archivoboletacesante::insertGetId($data);
        }        
    }

    function listararchivosboleta(Request $request){
        $where = array();
        if($request['anoBca'])   $where['anoBca']   = $request['anoBca'];
        if($request['idmesBca']) $where['idmesBca'] = $request['idmesBca'];
        if($request['tipBca'])   $where['tipBca']   = $request['tipBca'];
        $where['estBca'] = 1;
        echo json_encode(Boletacesantearchivo::where($where)->orderBy('idmesBca')->get()->toArray());
    }

    function eliminarboletas(Request $request){
        Boletacesantearchivo::where('idBca',$request['idBca'])->update(['estBca'=>0]);
        echo json_encode(1);
    }

    function listarboletas(Request $request){
        $where  = '';
        $where .= ($request['idBca'])?' and A.idBca='.$request['idBca']:'';
        $info['boletas'] = DB::connection('notificacion')->select(
        "SELECT 
        L.idBcl,
        A.anoBca,
        A.mesBca,
        A.tipBca,
        L.apeBcl,
        L.nomBcl,
        L.fecBcl,
        L.docBcl,
        L.carBcl,
        L.tipBcl,
        L.tipoPenBcl,
        L.tliquBcl,
        L.FirBcl,
        L.arcBcl,
        COUNT(Log.idBcl) as cantidad,
        IFNULL(NULL,'Sin correo')   as correo,
        IFNULL(NULL,'Sin celular') as celular
        from boletacesantearchivo A
        inner join boletacesantelista L on A.idBca = L.idBca
        left join boletacesalog Log on L.idBcl = Log.idBcl 
        where estBca = 1 and (textBcl like '%".$request['txtbuscar']."%' or CONCAT(nomBcl,' ',apeBcl) like '%".$request['txtbuscar']."%' or CONCAT(apeBcl,' ',nomBcl) like '%".$request['txtbuscar']."%')".$where
        ." GROUP BY 
        L.idBcl,
        A.anoBca,
        A.mesBca,
        A.tipBca,
        L.apeBcl,
        L.nomBcl,
        L.fecBcl,
        L.docBcl,
        L.carBcl,
        L.tipBcl,
        L.tipoPenBcl,
        L.tliquBcl,
        L.FirBcl,
        L.arcBcl 
        ORDER BY A.anoBca,A.idmesBca");
        //IFNULL(R.correo,'Sin correo')   as correo,
        //IFNULL(R.celular,'Sin celular') as celular
        //left join receptor R ON L.docBcl = R.documento and R.estado=1 and R.etapa_de_registro=2
        echo json_encode($info);        
    }

    function descargarboletasfirmadas(Request $request){
        $lista = Boletacesantelista::wherein('idBcl',explode(',',$request['idBcls']))->get()->toArray();
        $zip = new ZipArchive();
        $zip->open("miarchivo.zip",ZipArchive::CREATE);
        for ($i=0; $i < count($lista); $i++) {
            if(file_exists(''.$lista[$i]['arcBcl'])){
                $zip->addFile(''.$lista[$i]['arcBcl'],$lista[$i]['apeBcl'].$lista[$i]['nomBcl'].'.pdf');
            }else{
            }
        }
        $zip->close();
        header("Content-type: application/octet-stream");
        header("Content-disposition: attachment; filename=miarchivo.zip");
        // leemos el archivo creado
        readfile('miarchivo.zip');
        unlink('miarchivo.zip');//Destruye el archivo temporal
        //dd($lista);
    }
    
    function generarboletas(Request $request){
        ini_set('memory_limit','556M');
        ini_set('max_execution_time', 0);
        $lista = Boletacesantearchivo::select("*")
        ->join('boletacesantelista','boletacesantearchivo.idBca','boletacesantelista.idBca')
        ->where(['boletacesantearchivo.estBca'=>1])
        ->wherein('boletacesantelista.idBcl',explode(',',$request['idBcls']))
        ->get()->toArray();

        foreach ($lista as $boleta) {
            $esp = Especialistas::where('idespecialista',$request['idespecialista'])->get()->toArray();
            $esp = ($esp)?$esp[0]:false;
            $pdf = \PDF::loadView('boletacesante/pdf_boleta', compact('boleta','esp'));
            //$output = $pdf->setPaper('a5','landscape')->output();
            $output = $pdf->setPaper('a4','landscape')->output();
            $micarpeta = 'storage/boletacesante/archivo'.$boleta['idBca'].'/cargados';
            if (!file_exists($micarpeta)) { mkdir($micarpeta, 0777, true); }
            $nomarchivo = $micarpeta.'/'.rand().date('YmdHis').'.pdf';
            file_put_contents($nomarchivo, $output);
            Boletacesantelista::where('idBcl',$boleta['idBcl'])->update(['arcBcl'=>$nomarchivo]);
        }

        echo json_encode(1);    
    }

    function pdf_boleta(Request $request){
        $boleta = Boletacesantearchivo::select("*")
        ->join('boletacesantelista','boletacesantearchivo.idBca','boletacesantelista.idBca')
        ->where(['boletacesantearchivo.estBca'=>1,'boletacesantelista.idBcl'=>$request['idBcl']])
        ->get()->toArray();
        $boleta = ($boleta)?$boleta[0]:false;
        $esp = false;
        //if($boleta['FirBcl']){
        if($boleta['arcBcl']){
            return redirect()->to($boleta['arcBcl']);
            //return Redirect::to($boleta['arcBcl']);
        }else{
            $pdf = \PDF::loadView('boletacesante/pdf_boleta', compact('boleta','esp'));
            return $pdf->setPaper('a4','landscape')->stream($boleta['apeBcl'].' '.$boleta['nomBcl'].' '.$boleta['mesBca'].'-'.$boleta['anoBca'].'.pdf');
        }
        
    }

    function popup_anadiraexp(Request $request){
        $info['idBcl'] = $request['idBcl'];
        return view('boletacesante/popup_anadiraexp',$info);
    }

    function adjuntaraexp(Request $request){
        $lista = Boletacesantearchivo::select("*")
        ->join('boletacesantelista','boletacesantearchivo.idBca','boletacesantelista.idBca')
        ->where(['boletacesantearchivo.estBca'=>1])
        ->wherein('boletacesantelista.idBcl',explode(',',$request['idBcls']))
        ->get()->toArray();
        //foreach ($lista as $boleta) {
        for ($i=0; $i < count($lista); $i++) {
            $boleta = $lista[$i];
            $esp = false;
            $pdf = \PDF::loadView('boletacesante/pdf_boleta', compact('boleta','esp'));
            $output = $pdf->setPaper('a4','landscape')->output();
            $micarpeta = 'storage/boletacesante/archivo'.$boleta['idBca'].'/cargados';
            if (!file_exists($micarpeta)) { mkdir($micarpeta, 0777, true); }
            $nomarchivo = $micarpeta.'/'.rand().date('YmdHis').'.pdf';
            file_put_contents($nomarchivo, $output);
            Boletacesantelista::where('idBcl',$boleta['idBcl'])->update(['FirBcl'=>1,'arcBcl'=>$nomarchivo]);
            $lista[$i]['arcBcl'] = $nomarchivo;
        }
        $expnoencontrado = array();
        $anio   = $request['expanio'];
        $expexp = explode(',',$request['expexp']);
        foreach ($lista as $boleta) {
            $nuevaruta = $this->envioftp('siic01/cargados/','./'.$boleta['arcBcl']);
            foreach ($expexp as $exp) {
                $reclamos = DB::connection('notificacion')->select("SELECT idreclamo,expediente FROM reclamos WHERE estado=1 and DATE_FORMAT(fecha_expediente,'%Y')='$anio' and expediente=$exp");
                $reclamos = ($reclamos)?(Array)$reclamos[0]:(($exp=='0')?array('idreclamo'=>0):false);
                //print_r($reclamos);
                if($reclamos){
                    $session = session()->get('siic01_admin');
                    //print_r($reclamos);
                    //echo '<br><br>';                    
                        $doc['adj_idreclamo']    = $reclamos['idreclamo'];
                        $doc['adj_equipo']       = $session['equipo'];
                        $doc['idespecialista']   = $session['idespecialista'];
                        $doc['nombre']           = explode('/',$boleta['arcBcl'])[count(explode('/',$boleta['arcBcl']))-1];
                        $doc['nombre_documento'] = 'Boleta-'.$boleta['mesBca'].'-'.$boleta['anoBca'].'.pdf';
                        $doc['peso']             = '';
                        $doc['tipo']             = 'PDF';
                        $doc['ruta']             = $nuevaruta;
                        $doc['archivo']          = 'http://pruebas.ugel01.gob.pe/'.$nuevaruta;
                        $doc['tipo_documento']   = '16';
                        $doc['anio']             = $anio;
                        //$doc['numero']           = $reclamos['expediente'];
                        $doc['adj_especialista'] = $session['esp_nombres'].' '.$session['esp_apellido_paterno'].' '.$session['esp_apellido_materno'];
                        $doc['adj_dni']          = $session['ddni'];
                        $doc['adj_area']         = $session['area'];
                        $doc['adj_minarea']      = $session['areacorta'];
                        $doc['adj_minequipo']    = $session['equipocorta'];
                        $doc['idBcl']            = $boleta['idBcl'];
                        Documento::insert($doc);
                        Documentoprueba::insert($doc);
                        echo '<br><br>';
                        print_r($doc);
                    
                    //Documentos::insert($doc);
                }else{
                    $expnoencontrado[] = $exp;
                }
            }
        }
        echo '<br>Exp no encontrado:<br>:';
        print_r($expnoencontrado);
        echo json_encode(1);    
    }
                                                      //185774183520210817211009.pdf
                                                      //prueba.txt
    function envioftp($dest='siic01/cargados/185774183520210817211009.pdf',$source='185774183520210817211009.pdf'){
        $server = 'pruebas.ugel01.gob.pe';
        $ftp_user_name = 'pruebas';
        $ftp_user_pass = '{#2Xd(ywX%~h';
        $dest   = $dest.explode('/',$source)[count(explode('/',$source))-1];
        //$dest   = $dest.'jorge.pdf';
        $connection = ftp_connect($server);
        $login = ftp_login($connection, $ftp_user_name, $ftp_user_pass);
        if (!$connection || !$login) { die('Parece que no se puede conectar'); }
        $upload = ftp_put($connection, 'public_html/'.$dest, $source, FTP_BINARY);
        if (!$upload) { echo 'Fallo la subida al FTP'; }
        ftp_close($connection);
        return $dest;
    }
}
