<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Receptor;
use App\Models\Nexus;
use App\Models\Fichas;
use App\Models\Preguntas;
use App\Models\Receptores;
use App\Models\Respuestas_adicionales;
use App\Models\Respuestas_detalles;
use App\Models\Variables_adicionales;
use App\Models\Docente;
use App\Models\Receptores_deta;
use App\Models\Iiee_a_evaluar_rie;
use Storage;
use ZipArchive;
use DB;

//insertGetId
//DB::raw()
class Fichamonitoreo extends Controller
{

    //Director
    function director(Request $request){
        if(session()->get('siic01')){
            $info['idFic'] = $request['idFic'];
            $info['session']     = session()->get('siic01');
            $info['anadirficha'] =  DB::connection('ficha')->select("SELECT idFic, REPLACE(nomFic,'FICHA DE','REGISTRAR') as nomFic from fichas where modFic = '".$info['session']['modalidad']."' and estFic = 1 and tipFic = 'DIRECTIVO AL DOCENTE' and NOW() between iniFic and DATE_ADD(finFic,INTERVAL 1 DAY)"); 
            return view('fichamonitoreo/director',$info);
        }else{
            
        }
    }

    function popup_anadirfichadirector(Request $request){
        $info['ficha'] = Fichas::select('idFic','nomFic')->where('idFic',$request['idFic'])->get()->toArray()[0];        
        $info['data']  = false;
        return view('fichamonitoreo/popup_anadirfichadirector',$info);
    }

    function popup_anadirfichaesp(Request $request){
        if(session()->get('siic01_admin')){
        $info['session'] = session()->get('siic01_admin');
        $info['ficha']   = Fichas::select('idFic','nomFic')->where('idFic',$request['idFic'])->get()->toArray()[0];
        $info['iiee']    =  DB::select("SELECT codmod, CONCAT(institucion,' (',nivel,')') as institucion FROM iiee_a_evaluar_RIE WHERE estado=1 and  (gestion LIKE 'Publica%' or gestion_dependencia='Privada - Parroquial' or gestion_dependencia='Privada - Instituciones Benéficas')");
        $info['data']    = false;
        return view('fichamonitoreo/popup_anadirfichaespecialista',$info);
        }else{
            echo 'Sesion expirada. Recarge la pagina';
        }
    }
    

    function docentevalidar_dni(Request $request){
        $dni = $request['dni'];
        $data = Nexus::select('nombres','apellipat','apellimat','descargo','idnivel')->where(['estado'=>1,'numdocum'=>$dni])->whereIn('descsubtipt',['DIRECTIVO','DOCENTE'])->orderBy('descsubtipt', 'ASC')->first();
        if($data){
            $receptor = Receptor::where(['documento'=>$dni,'estado'=>1,'etapa_de_registro'=>2])->first();
            if($receptor){
                $data['celular'] = $receptor['celular'];
                $data['correo']  = $receptor['correo'];
            }
        }else{
            $data = false;
        }
        echo json_encode($data);
        //dd($data);
    }

    function anadirfichadirector(Request $request){
        //dd($request->all());
        $nro = 1;
        $session  = session()->get('siic01');
        $ficha    = Fichas::select("*",DB::raw("IF(NOW() between iniFic and  DATE_ADD(finFic,INTERVAL 1 DAY),1,0) as habilitado"),DB::raw("DATE_FORMAT(finFic,'%d/%m/%Y') as t_fin"))->where('idFic',$request['idficha'])->get()->toArray();
        $ficha    = ($ficha)?$ficha[0]:false;
        $boxmes = $request['boxmes'];
        $r_grado = array('1INI','2INI','3INI','4INI','5INI','1PRI','2PRI','3PRI','4PRI','5PRI','6PRI','1SEC','2SEC','3SEC','4SEC','5SEC','1EBE','2EBE','3EBE','4EBE','5EBE','6EBE','3EBI','4EBI','5EBI');
        $r_ciclo = array('I','I','II','II','II','III','III','IV','IV','V','V','VI','VI','VII','VII','VII','III','III','IV','IV','V','V','II','II','II');
        $request['cicDoc'] = str_replace($r_grado,$r_ciclo,$request['graDoc']);
        unset($request['idficha']);
        unset($request['boxmes']);
        unset($request['_token']);
        $idDoc = Docente::insertGetId($request->all());
        foreach ($boxmes as $key) {
            //-------------------------------------
            $data['fecProRec'] = $key;
            $data['idDoc']     = $idDoc;
            $data['idFic']     = $ficha['idFic'];
            $data['codlocRec'] = $session['codlocal'];
            $data['insRec']    = $session['iiee'];
            $data['gesRec']    = $session['d_gestion'];
            $data['gesDepRec'] = $session['d_ges_dep'];
            $data['nomRec']    = $session['nombres'];
            $data['apePatRec'] = $session['apellipat'];
            $data['apeMatRec'] = $session['apellimat'];
            $data['telRec']    = $session['celular_pers'];
            $data['corRec']    = $session['correo_pers'];
            $data['idConRec']  = $session['id_contacto'];
            $data['dniRec']    = $session['dni'];
            $data['carRec']    = $session['cargo'];
            $data['textModalidadRec'] = $session['modalidad'];
            $data['textNivelesRec']    = $session['niveles'];
            
            if($ficha['modFic']=='CETPRO' or $ficha['modFic']=='EBA' or $ficha['modFic']=='EBE' or $ficha['modFic']=='EBR'){
                $data['disRec'] = $session['conf_permisos'][0]['d_dist'];
                $data['redRec']      = $session['conf_permisos'][0]['red'];
                Receptores::insertGetId($data);
            }else{
                //echo $ficha->modalidad.'<br><br>';
                foreach ($session['conf_permisos'] as $key) {
                    //echo $ficha->modalidad.$key->idnivel.' -> '.$key->esc_codmod.'<br>';
                        if( $request['idNivDoc']== $key['idnivel']){
                            $permiso = $key;
                        }
                        /*switch ($key['idnivel']) {
                            case 'EBR Inicial5':            $permiso = $key; break;
                            case 'EBR Primaria4':           $permiso = $key; break;
                            case 'EBR Secundaria3':         $permiso = $key; break;
                            case 'EBA Inicial Intermedio6': $permiso = $key; break;
                            case 'EBA Avanzado7':           $permiso = $key; break;
                            //default: $codmod = ''; break;
                        }*/
                    }
                 $data['nroVisRec'] = $nro++;
                 $data['idNivRec']  = $request['idNivDoc'];
                 $data['nivRec'   ] = $permiso['d_niv_mod'];
                 $data['codmodRec'] = $permiso['esc_codmod'];
                 $data['disRec']    = $permiso['d_dist'];
                 $data['redRec']    = $permiso['red'];
                 $data['redRec']    = 
                 Receptores::insertGetId($data);
            }
            //-------------------------------------
        }
        echo json_encode(1);        
    }

    function anadirfichaespecialista(Request $request){
        //$iiee = iiee_a_evaluar_rie::select('idnivel','institucion','gestion','gestion_dependencia','distrito','red','codlocal')->where('codmod',$request['codmod'])->first();
        $iiee =  DB::select("SELECT 
        E.codlocal     as codlocRec,
        E.codmod       as codmodRec,
        E.institucion  as insRec,
        E.nivel        as nivRec,
        E.idnivel      as idNivRec,
        E.gestion      as gesRec,
        E.gestion_dependencia as gesDepRec,
        C.nombres      as nomRec,
        C.apellipat    as apePatRec,
        C.apellimat    as apeMatRec,
        C.id_contacto  as idConRec,
        C.dni          as dniRec,
        C.celular_pers as telRec,
        C.correo_pers  as corRec,
        E.distrito     as disRec,
        E.red          as redRec
        FROM iiee_a_evaluar_RIE E 
                LEFT JOIN (
                    select Co.*,Pe.esc_codmod from conf_permisos Pe 
                    inner join contacto Co on Pe.id_contacto = Co.id_contacto
                    where Pe.estado = 1 and Co.estado = 1 and Co.flg=1
                ) C ON E.codmod = C.esc_codmod 
                WHERE E.estado=1 and E.codmod='".$request['codmod']."'");
        
        $iiee = ($iiee)?(Array)$iiee[0]:false;
        
        if($iiee['dniRec']){
        $nexus = Nexus::select('nombres','apellipat','apellimat','descargo','idnivel')->where(['estado'=>1,'numdocum'=>$iiee['dniRec']])->whereIn('descsubtipt',['DIRECTIVO','DOCENTE'])->orderBy('descsubtipt','ASC')->first();
            if($nexus){
            $iiee['nomRec']    = $nexus['nombres'];
            $iiee['apePatRec'] = $nexus['apellipat'];
            $iiee['apeMatRec'] = $nexus['apellimat'];
            $iiee['carRec']    = $nexus['descargo'];
            }
        }

        if($request['boxmes']){
            $nro = 1;
            $idDoc = Docente::insertGetId(['idNivDoc'=>$iiee['idNivRec']]);
            foreach ($request['boxmes'] as $key) {
                $iiee['fecProRec'] = $key;
                $iiee['idDoc']     = $idDoc;
                $iiee['idFic']     = $request['idficha'];
                $iiee['idEspRec']  = $request['idespecialista'];
                $iiee['nroVisRec'] = $nro++;
                Receptores::insertGetId($iiee);
            }
        }
       return 1;
    }

    function ver_ficha_ie(Request $request){
        $session  = session()->get('siic01');
        $where    = "";
        $where   .= " and YEAR(F.iniFic) = '".$request['anio']."' ";
        $l_modalidad = str_replace(array('1','2','3','4'), array('CETPRO','EBE','EBR','EBA'), $session['idmodalidad']);
        $where_separar_por_modalidad = ($request['anio']<2025)?" R.codlocRec IN(".$session['codlocal'].")":" R.codlocRec IN(".$session['codlocal'].") and  R.textModalidadRec='$l_modalidad'";
        $l_nivel     = str_replace(array('5','4','3','6','7'), array('EBR Inicial','EBR Primaria','EBR Secundaria','EBA Inicial Intermedio','EBA Avanzado'), $session['idnivel']);
        $where   .= " and ( F.modFic = 'TODOS' or F.modFic IN( '".str_replace(",","','",$l_modalidad.','.$l_nivel) ."') )";
        $where   .= (strpos($session['d_ges_dep'],'Particular')>-1 or strpos($session['d_gestion'],'Privada')>-1)?" and F.gesFic IN('TODOS','PARTICULAR')":" and F.gesFic IN('TODOS','ESTATAL')";        
        DB::connection('ficha')->select("SET lc_time_names = 'es_ES';");
        $query = DB::connection('ficha')->select(
       "SELECT 
        F.idFic,
        F.nomFic,
        F.desFic,
        F.modFic,
        R.culRec,
        R.idRec,
        F.priFic,
        DATE_FORMAT(F.iniFic,'%d/%m/%Y') as t_inicio,
        DATE_FORMAT(F.finFic,'%d/%m/%Y')    as t_fin,
        IF(NOW() between F.iniFic and  DATE_ADD(F.finFic,INTERVAL 1 DAY),1,0) as habilitado 
        FROM fichas F 
        LEFT JOIN receptores R ON F.idFic = R.idFic and R.estRec=1 and ( R.codlocRec IN(".$session['codlocal'].") or R.codmodRec IN(".$session['codmods'].") )  
        WHERE F.estFic = 1 and F.tipFic='FICHA' $where 
        UNION
        SELECT 
        F.idFic,
        CONCAT(F.nomFic,' <b>(',D.nomDoc,' ',D.apePatDoc,' ',D.apeMatDoc,')</b>') as nomFic,
        F.desFic,
        F.modFic,
        R.culRec,
        R.idRec,
        F.priFic,
        DATE_FORMAT(F.iniFic,'%d/%m/%Y') as t_inicio,
        IF(fecProRec is null,DATE_FORMAT(F.finFic,'%d/%m/%Y'),UPPER(MONTHNAME(R.fecProRec)))    as t_fin,
        IF(NOW() between F.iniFic and  DATE_ADD(F.finFic,INTERVAL 1 DAY),1,0) as habilitado 
        FROM fichas F 
        INNER JOIN receptores R ON F.idFic = R.idFic and R.estRec=1 and ( ($where_separar_por_modalidad) or R.codmodRec IN(".$session['codmods'].") )  
        INNER JOIN docente    D ON R.idDoc = D.idDoc 
        WHERE F.estFic = 1 and F.tipFic<>'FICHA' and F.tipFic<>'AL DIRECTIVO' and  YEAR(F.iniFic) = '".$request['anio']."'
        ORDER BY priFic DESC
        ");
        
        $result = array();
        foreach ($query as $key){
            $result[] = (Array)$key;
        }
        $info['lista'] = $result;
        echo json_encode($info);
    }
    
    function eliminar_ficha(Request $request){
        Receptores::where('idRec',$request['idRec'])->update(['estRec'=>0]);
        return 1;
    }

    function mostrar_ficha(Request $request){
        $idficha    = $request['idficha'];
        $codlocal   = $request['codlocal'];
        $idreceptor = $request['idRec'];
        //Registrar respuesta        
        $ficha    = Fichas::select("*",DB::raw("IF(NOW() between iniFic and  DATE_ADD(finFic,INTERVAL 1 DAY),1,0) as habilitado"),DB::raw("DATE_FORMAT(finFic,'%d/%m/%Y') as t_fin"))->where('idFic',$idficha)->get()->toArray();
        $ficha    = ($ficha)?$ficha[0]:false;
        $info['ficha'] = $ficha;
    if($ficha['habilitado']==0){
        echo '<h2 class="modal-content" style="font-weight:bolder;padding:40px;text-align:center;">HA CONCLUIDO EL REGISTRO DE LA '.$ficha['nomFic'].'<br>EN LA FECHA '.$ficha['t_fin'].'</h2>';
    }else{      
        if(!$idreceptor){
        if(session()->get('siic01')){
            $session  = session()->get('siic01');
            $data['idFic']     = $idficha;
            $data['codlocRec'] = $codlocal;
            $data['insRec']    = $session['iiee'];
            $data['gesRec']    = $session['d_gestion'];
            $data['gesDepRec'] = $session['d_ges_dep'];
            $data['nomRec']    = $session['nombres'];
            $data['apePatRec'] = $session['apellipat'];
            $data['apeMatRec'] = $session['apellimat'];
            $data['telRec']    = $session['celular_pers'];
            $data['corRec']    = $session['correo_pers'];
            $data['idConRec']  = $session['id_contacto'];
            $data['dniRec']    = $session['dni'];
            $data['carRec']    = $session['cargo'];
            $data['textModalidadRec'] = $session['modalidad'];
            $data['textNivelesRec']    = $session['niveles'];

            if($ficha['modFic']=='TODOS' or $ficha['modFic']=='CETPRO' or $ficha['modFic']=='EBA' or $ficha['modFic']=='EBE' or $ficha['modFic']=='EBR'){
                $data['disRec'] = $session['conf_permisos'][0]['d_dist'];
                $data['redRec']      = $session['conf_permisos'][0]['red'];
                $data['codmodRec']    = $session['codmods'];
                $idreceptor = ($idreceptor)?$idreceptor:$this->registro_receptor_codlocal($idficha,$codlocal,$data);
            }else{
                foreach ($session['conf_permisos'] as $key) {
                        switch ($ficha['modFic'].$key['idnivel']) {
                            case 'EBR Inicial5':            $permiso = $key; break;
                            case 'EBR Primaria4':           $permiso = $key; break;
                            case 'EBR Secundaria3':         $permiso = $key; break;
                            case 'EBA Inicial Intermedio6': $permiso = $key; break;
                            case 'EBA Avanzado7':           $permiso = $key; break;
                        }  
                    }
                 $data['codmodRec'] = $permiso['esc_codmod'];
                 $data['disRec']    = $permiso['d_dist'];
                 $data['redRec']    = $permiso['red'];
                $idreceptor = ($idreceptor)?$idreceptor:$this->registro_receptor_codmod($idficha,$data['codmodRec'],$data);
            }
            }
            }

            if($idreceptor){
                $info['registro']  = (Array)DB::connection('ficha')->select("SELECT 
                R.idRec,
                R.idDoc,
                R.idFic,
                insRec,
                codmodRec,
                disRec,
                redRec,
                nomRec,
                apePatRec,
                apeMatRec,
                dniRec,
                telRec,
                corRec,
                carRec,
                nroVisRec,
                fecAplRec,
                AsiTecRec,
                nomDoc,
                apePatDoc,
                apeMatDoc,
                dniDoc,
                graDoc,
                secDoc,
                idNivDoc,
                nroEstRec,
                areDoc,
                nroEstPreRec,
                nroEstAsiRec,
                tipSerRec,
                telDoc,
                corDoc,
                codlocRec,
                cicDoc,
                esp_apellido_paterno,
                esp_apellido_materno,
                esp_nombres,
                telefono1,
                ddni,
                conRec,
                recRec,
                comDirRec,
                impDirRec,
                comDocRec,
                impDocRec,
                comEspRec,
                impEspRec,
                nroEstRec,
                nroEstPreRec,
                nroEstAsiRec,
                tipSerRec,
                textModalidadRec,
                textNivelesRec,
                DATE_FORMAT(R.updated_at,'%d/%m/%Y') as fechaficha,
                DATE_FORMAT(Rd.fecAplRec,'%Y-%m-%d') as fechaaplicacion,
                DATE_FORMAT(Rd.fecIniAplRec,'%H:%i:%s') as horainicioaplicacion,
                DATE_FORMAT(Rd.fecAplRec,'%H:%i:%s') as horaaplicacion
                FROM receptores R 
                LEFT JOIN receptores_deta Rd ON R.idRec=Rd.idRec
                LEFT JOIN docente D ON R.idDoc=D.idDoc and D.estDoc=1 
                LEFT JOIN siic01ugel01gob_directores.especialistas E ON R.idEspRec = E.idespecialista 
                WHERE R.estRec=1 and R.idRec=$idreceptor")[0];
                $info['ficha_respondida'] = $this->ficha_respondida($idficha,$idreceptor);
                $info['editarficha'] = true;
                $info['grupo']   = $this->grupo_ficha_respondida($idficha,$idreceptor);
                if($info['grupo']){
                for ($i=0; $i < count($info['grupo']); $i++) {
                $info['grupo'][$i]['detalle'] = $this->ver_ficha_respondida($idficha,$idreceptor,$info['grupo'][$i]['gruPre']);
                }
                }
                return view('fichamonitoreo/ficha',$info);
            }else{
                echo 'No se ha generado la ficha. Vuelva a ingresar a la pagina web.';
            }
        
    }
    }
    //Director

    public function mostrar_modelo_ficha(Request $request){
        $idreceptor  = ($request['idreceptor'])?$request['idreceptor']:0;
        $info['ficha']    = Fichas::where('idFic',$request['idficha'])->get()->toArray()[0];
        $info['registro'] = ($idreceptor)?Receptores::where(['estRec'=>1,'idRec'=>$idreceptor])->get()->first():false;
        $info['ficha_respondida'] = false;
        $info['editarficha'] = false;
        $info['grupo']    = $this->grupo_ficha_respondida($request['idficha'],$idreceptor);
        for ($i=0; $i < count($info['grupo']); $i++) {
            $info['grupo'][$i]['detalle']   = $this->ver_ficha_respondida($request['idficha'],$idreceptor,$info['grupo'][$i]['gruPre']);
        }
        return view('fichamonitoreo/ficha',$info);
    }
    
    public function completar_nro_recurso(Request $request){
        $idFic  = $request['idFic'];
        $nroPre = $request['nroPre'];
        $data = DB::connection('ficha')->select("SELECT REPLACE(gruPre,'<br>','') as gruPre,REPLACE(textPre,'<br>','') as textPre,obsPre FROM preguntas WHERE idFic=$idFic and nroPre=$nroPre and tipPre='INICIO/PROCESO/LOGRADO'");
        echo json_encode($data);
    }
    
    public function mostrar_pdf_ficha(Request $request){
        ini_set('memory_limit','256M');
        $idreceptor  = ($request['idreceptor'])?$request['idreceptor']:0;
        $ficha = Fichas::where('idFic',$request['idficha'])->get()->toArray()[0];
        $registro = ($idreceptor)?(Array)DB::connection('ficha')->select(
            "SELECT 
            insRec,
            codmodRec,
            disRec,
            redRec,
            nomRec,
            apePatRec,
            apeMatRec,
            dniRec,
            telRec,
            corRec,
            carRec,
            nroVisRec,
            fecAplRec,
            AsiTecRec,
            nomDoc,
            apePatDoc,
            apeMatDoc,
            dniDoc,
            graDoc,
            secDoc,
            idNivDoc,
            nroEstRec,
            areDoc,
            nroEstPreRec,
            nroEstAsiRec,
            tipSerRec,
            telDoc,
            corDoc,
            codlocRec,
            cicDoc,
            esp_apellido_paterno,
            esp_apellido_materno,
            esp_nombres,
            telefono1,
            ddni,
            conRec,
            recRec,
            comDirRec,
            impDirRec,
            comDocRec,
            impDocRec,
            comEspRec,
            impEspRec,
            textModalidadRec,
            textNivelesRec,
            DATE_FORMAT(Rd.fecAplRec,'%Y-%m-%d') as fechaaplicacion,
            DATE_FORMAT(Rd.fecIniAplRec,'%H:%i:%s') as horainicioaplicacion,
            DATE_FORMAT(Rd.fecAplRec,'%H:%i:%s') as horaaplicacion,
            DATE_FORMAT(R.updated_at,'%d/%m/%Y') as fechaficha FROM receptores R 
            LEFT JOIN receptores_deta Rd ON R.idRec=Rd.idRec
            LEFT JOIN docente D ON R.idDoc=D.idDoc and D.estDoc=1 
            LEFT JOIN siic01ugel01gob_directores.especialistas E ON R.idEspRec = E.idespecialista 
            WHERE R.estRec=1 and R.idRec=$idreceptor")[0]:false;
        $ficha_respondida = false;
        $grupo    = $this->grupo_ficha_respondida($request['idficha'],$idreceptor);
        for ($i=0; $i < count($grupo); $i++) {
            $grupo[$i]['detalle']   = $this->ver_ficha_respondida($request['idficha'],$idreceptor,$grupo[$i]['gruPre']);
        }
        
        if($ficha['htmlDatGenFic'] and $registro){
        $ficha['htmlDatGenFic'] = str_replace('disRec',$registro['disRec'],$ficha['htmlDatGenFic']);
        $ficha['htmlDatGenFic'] = str_replace('redRec',$registro['redRec'],$ficha['htmlDatGenFic']);
        $ficha['htmlDatGenFic'] = str_replace('codlocRec',$registro['codlocRec'],$ficha['htmlDatGenFic']);
        $ficha['htmlDatGenFic'] = str_replace('insRec',$registro['insRec'],$ficha['htmlDatGenFic']);
        $ficha['htmlDatGenFic'] = str_replace('textModalidadRec',$registro['textModalidadRec'],$ficha['htmlDatGenFic']);
        $ficha['htmlDatGenFic'] = str_replace('textNivelesRec',$registro['textNivelesRec'],$ficha['htmlDatGenFic']);
        

        }
        
        $resumenipl = $this->resumen_inicio_proceso_logrado($idreceptor);
        $totalipl   = $this->totales_resumen_inicio_proceso_logrado($idreceptor);
        $pdf = \PDF::loadView('fichamonitoreo/pdf_ficha', compact('ficha','registro','ficha_respondida','grupo','resumenipl','totalipl'));
        return $pdf->setPaper('a4','')->stream('pdf_ficha.pdf');
    }
    
    public function generar_pdf_ficha($idficha,$idreceptor=0,$carpeta='carpetatemporal'){
        ini_set('memory_limit','256M');
        date_default_timezone_set('America/Lima');
        if (!file_exists($carpeta)) { mkdir($carpeta, 0777, true); }
        $idreceptor  = ($idreceptor)?$idreceptor:0;
        $ficha = Fichas::where('idFic',$idficha)->get()->toArray()[0];
        $registro = ($idreceptor)?(Array)DB::connection('ficha')->select(
            "SELECT 
            insRec,
            codmodRec,
            disRec,
            redRec,
            nomRec,
            apePatRec,
            apeMatRec,
            dniRec,
            telRec,
            corRec,
            carRec,
            nroVisRec,
            fecAplRec,
            AsiTecRec,
            nomDoc,
            apePatDoc,
            apeMatDoc,
            dniDoc,
            graDoc,
            secDoc,
            idNivDoc,
            nroEstRec,
            areDoc,
            nroEstPreRec,
            nroEstAsiRec,
            tipSerRec,
            telDoc,
            corDoc,
            codlocRec,
            cicDoc,
            esp_apellido_paterno,
            esp_apellido_materno,
            esp_nombres,
            telefono1,
            ddni,
            conRec,
            recRec,
            comDirRec,
            impDirRec,
            comDocRec,
            impDocRec,
            comEspRec,
            impEspRec,
            DATE_FORMAT(Rd.fecAplRec,'%Y-%m-%d') as fechaaplicacion,
            DATE_FORMAT(Rd.fecIniAplRec,'%H:%i:%s') as horainicioaplicacion,
            DATE_FORMAT(Rd.fecAplRec,'%H:%i:%s') as horaaplicacion,
            DATE_FORMAT(R.updated_at,'%d/%m/%Y') as fechaficha FROM receptores R 
            LEFT JOIN receptores_deta Rd ON R.idRec=Rd.idRec
            LEFT JOIN docente D ON R.idDoc=D.idDoc and D.estDoc=1 
            LEFT JOIN siic01ugel01gob_directores.especialistas E ON R.idEspRec = E.idespecialista 
            WHERE R.estRec=1 and R.idRec=$idreceptor")[0]:false;
        $ficha_respondida = false;
        $grupo    = $this->grupo_ficha_respondida($idficha,$idreceptor);
        for ($i=0; $i < count($grupo); $i++) {
            $grupo[$i]['detalle']   = $this->ver_ficha_respondida($idficha,$idreceptor,$grupo[$i]['gruPre']);
        }
        $resumenipl = $this->resumen_inicio_proceso_logrado($idreceptor);
        $totalipl   = $this->totales_resumen_inicio_proceso_logrado($idreceptor);
        $pdf = \PDF::loadView('fichamonitoreo/pdf_ficha', compact('ficha','registro','ficha_respondida','grupo','resumenipl','totalipl'));
        $output = $pdf->output();
        $nomarchivo = $carpeta.'/'.'rec'.$idreceptor.'_'.date('Ymd_His').'.pdf';
        file_put_contents($nomarchivo, $output);
        return $nomarchivo;
    }
    
    /*public function generar_pdf_ficha($idficha,$idreceptor=0){
        $idreceptor  = ($idreceptor)?$idreceptor:0;
        $ficha = Fichas::where('idFic',$idficha)->get()->toArray()[0];
        $registro = ($idreceptor)?Receptores::select("*",DB::raw("DATE_FORMAT(updated_at,'%d/%m/%Y') as fechaficha"))->where(['estRec'=>1,'idRec'=>$idreceptor])->get()->first():false;
        $ficha_respondida = false;
        $grupo    = $this->grupo_ficha_respondida($idficha,$idreceptor);
        for ($i=0; $i < count($grupo); $i++) {
            $grupo[$i]['detalle']   = $this->ver_ficha_respondida($idficha,$idreceptor,$grupo[$i]['gruPre']);
        }
        $pdf = \PDF::loadView('fichamonitoreo/pdf_ficha', compact('ficha','registro','ficha_respondida','grupo'));
        $output = $pdf->output();
        $nomarchivo = 'carpetatemporal/'.rand().'.pdf';
        file_put_contents($nomarchivo, $output);
        return $nomarchivo;
    }*/

    public function resumen_inicio_proceso_logrado($idRec){
       $tabla = DB::connection('ficha')->select("SELECT 
       REPLACE(P.gruPre,'<br>',' ') as gruPre,
       COUNT(*) as cantidad,
       SUM(IF(D.resRdd=1,1,0)) as inicio,
       SUM(IF(D.resRdd=2,1,0)) as proceso,
       SUM(IF(D.resRdd=3,1,0)) as logrado,
       SUM(IF(D.resRdd IN(1,2,3),1,0)) as cantidadipl
       FROM respuestas_detalles D 
       INNER JOIN receptores R ON D.idRec=R.idRec
       INNER JOIN preguntas  P ON D.idPre=P.idPre
       WHERE D.estRdd=1 and R.estRec=1 and P.estPre=1 and P.tipPre IN('INICIO/PROCESO/LOGRADO','NOAPLICA/INICIO/PROCESO/LOGRADO','INICIO/LOGRADO') and R.idRec = $idRec
       GROUP BY P.gruPre");
       return $tabla;
    }

    public function totales_resumen_inicio_proceso_logrado($idRec){
        $tabla = DB::connection('ficha')->select("SELECT 
        '' as gruPre,
        COUNT(*) as cantidad,
        SUM(IF(D.resRdd=1,1,0)) as inicio,
        SUM(IF(D.resRdd=2,1,0)) as proceso,
        SUM(IF(D.resRdd=3,1,0)) as logrado,
        SUM(IF(D.resRdd IN(1,2,3),1,0)) as cantidadipl
        FROM respuestas_detalles D 
        INNER JOIN receptores R ON D.idRec=R.idRec
        INNER JOIN preguntas  P ON D.idPre=P.idPre
        WHERE D.estRdd=1 and R.estRec=1 and P.estPre=1 and P.tipPre IN('INICIO/PROCESO/LOGRADO','NOAPLICA/INICIO/PROCESO/LOGRADO','INICIO/LOGRADO') and R.idRec=$idRec");
        return ($tabla)?(Array)$tabla[0]:false;
     }
    
    public function crearficha(Request $request){
        $info['anio'] = ($request['anio']=='TODO')?'':(($request['anio'])?:date('Y'));
        $where = ($info['anio'])?" and YEAR(iniFic)='".$info['anio']."'":"";
        $info['anio'] = $request['anio'];
        
        //$where = ($request['anio'])?" and YEAR(iniFic)='".$request['anio']."'":"";
        if(session()->get('siic01_admin')){
            $info['session'] = session()->get('siic01_admin');
            //$info['catalogo'] = Fichas::where(['estFic'=>'1',''=>])->get()->toArray();
            $info['listaarea'] = DB::connection('ficha')->select("SELECT areaFic FROM `fichas` WHERE estFic=1 $where GROUP BY areaFic");
            $where .= ($request['area'])?" and areaFic='".$request['area']."'":"";
            $info['area'] = $request['area'];
            $info['catalogo'] = DB::connection('ficha')->select("SELECT * FROM fichas WHERE estFic IN(1,2)".$where);
            $info['anadirficha'] =  DB::connection('ficha')->select("SELECT idFic, REPLACE(nomFic,'FICHA DE','PROGRAMAR') as nomFic from fichas where estFic = 1 and tipFic <> 'FICHA' and NOW() between iniFic and DATE_ADD(finFic,INTERVAL 1 DAY)"); 
            return view('fichamonitoreo/crearficha',$info);
        }
    }

    public function listar_ficha(Request $request){
        $where = ($request['anio']=='TODO')?"":" and YEAR(iniFic)='".$request['anio']."'";
        $where .= ($request['area'])?" and areaFic='".$request['area']."'":"";
        $info['catalogo'] = DB::connection('ficha')->select("SELECT *,DATE_FORMAT(iniFic,'%d/%m/%Y') as t_inicio,DATE_FORMAT(finFic,'%d/%m/%Y') as t_fin, IF(NOW() between iniFic and DATE_ADD(finFic,INTERVAL 1 DAY),1,0) as habilitado FROM fichas WHERE estFic IN(1,2)".$where);
        //where('estFic','1')->
        //$info['catalogo'] = Fichas::select('*',DB::raw("DATE_FORMAT(iniFic,'%d/%m/%Y') as t_inicio"),DB::raw("DATE_FORMAT(finFic,'%d/%m/%Y') as t_fin"),DB::raw("IF(NOW() between iniFic and DATE_ADD(finFic,INTERVAL 1 DAY),1,0) as habilitado"))->get()->toArray();
        echo json_encode($info);
    }

    public function guardar_ficha(Request $request){
        date_default_timezone_set('America/Lima');
        $r_datos = array();
        if($request['datos']){
            $datos = explode("&&",$request['datos']);
            if($datos){
                for ($i=0; $i < count($datos); $i++) { 
                        $r_datos[] = explode("||",$datos[$i]);
                    }
            }
            if($r_datos){
                    foreach ($r_datos as $key) {
                        $idficha             = $key[0];
                        if($key[1]=='ELIMINAR') $fila['estFic'] = 0;
                        if($key[1]=='REGISTRADO') $fila['estFic'] = 1;
                        if($key[1]=='BORRADOR') $fila['estFic'] = 2;
                        $fila['nomFic'] = $key[2];
                        $fila['desFic'] = $key[3];
                        $fila['totRecFic'] = $key[4];
                        $fila['areaFic'] = $key[5];
                        $fila['iniFic'] = $key[6];
                        $fila['finFic'] = $key[7];
                        $fila['modFic'] = $key[8];
                        $fila['gesFic'] = $key[9];
                        $fila['tipFic'] = $key[10];
                        $fila['decFic'] = ($key[11]=='true')?1:0;
                        $fila['DatGenFic'] = ($key[12]=='true')?1:0;
                        $fila['DocMonFic'] = ($key[13]=='true')?1:0;
                        $fila['genPdfFic'] = ($key[14]=='true')?1:0;
                        $fila['pbiFic']    = $key[15];
                        if(Fichas::where('idFic',$idficha)->get()->toArray()){
                           Fichas::where('idFic',$idficha)->update($fila);
                        }else{
                           Fichas::insert($fila);
                        }
                    }
            }
        }
    }

    public function listar_pregunta(Request $request){
        $info['competencia'] = Preguntas::where(['estPre'=>1,'idFic'=>$request['idficha']])->orderBy('ordPre','ASC')->orderBy('nroPre','ASC')->get()->toArray();
        echo json_encode($info);
    }

    public function exportar_sustento_ficha(Request $request){
        ini_set('memory_limit','256M');
        ini_set('max_execution_time', 0);
        if($request['idReceptor']){
            $fichas = Receptores::select("idFic","idRec","insRec","fichapdf")->whereIn('receptores.idRec', explode(',',$request['idReceptor']))->get()->toArray();
            $reseptores = Receptores::select("insRec","arcRdd","obsPre")
            ->join("respuestas_detalles","receptores.idRec","respuestas_detalles.idRec")
            ->join("preguntas","respuestas_detalles.idPre","preguntas.idPre")
            ->where(['receptores.culRec'=>1,'receptores.idFic'=>$request['idficha'],'receptores.estRec'=>1,'respuestas_detalles.estRdd'=>1,'preguntas.estPre'=>1,'preguntas.adjArcPre'=>1,'respuestas_detalles.resRdd'=>'SI'])
            ->whereIn('receptores.idRec', explode(',',$request['idReceptor']))
            ->get()->toArray();
        }else{
            $fichas = Receptores::select("idFic","idRec","insRec","fichapdf")->where(['estRec'=>1])->where(['culRec'=>1,'estRec'=>1,'idFic'=>$request['idficha']])->get()->toArray();
            $reseptores = Receptores::select("insRec","arcRdd","obsPre")
            ->join("respuestas_detalles","receptores.idRec","respuestas_detalles.idRec")
            ->join("preguntas","respuestas_detalles.idPre","preguntas.idPre")
            ->where(['receptores.culRec'=>1,'receptores.idFic'=>$request['idficha'],'receptores.estRec'=>1,'respuestas_detalles.estRdd'=>1,'preguntas.estPre'=>1,'preguntas.adjArcPre'=>1,'respuestas_detalles.resRdd'=>'SI'])
            ->get()->toArray();
        }
        //dd($reseptores);
        $archivostemporales = array();
        $zip = new ZipArchive();
        $zip->open("miarchivo.zip",ZipArchive::CREATE);
        foreach ($fichas as $key) {
            //$nomarchivo = $this->generar_pdf_ficha($key['idFic'],$key['idRec']);
            $nomarchivo = ($key['fichapdf'])?$key['fichapdf']:$this->generar_pdf_ficha($key['idFic'],$key['idRec']);
            $zip->addFile($nomarchivo,$key['insRec'].'/ficha.pdf');
            $archivostemporales[] = $nomarchivo;
        }
        for ($i=0; $i < count($reseptores); $i++) {
            if(file_exists('.'.$reseptores[$i]['arcRdd'])){
                $zip->addFile('.'.$reseptores[$i]['arcRdd'],$reseptores[$i]['insRec'].'/anexo'.($i+1).'.pdf');
            }
        }
        $zip->close();
        header("Content-type: application/octet-stream");
        header("Content-disposition: attachment; filename=miarchivo.zip");
        // leemos el archivo creado
        readfile('./miarchivo.zip');
        unlink('miarchivo.zip');//Destruye el archivo temporal
        //foreach ($archivostemporales as $key) { unlink($key); }
        
    }
    
    function generar_masa_pdf_ficha(Request $request){
        ini_set('memory_limit', '256M');
        set_time_limit(0);
        if($request['idReceptor']){
            $reseptores = Receptores::select("idFic","idRec",DB::raw("DATE_FORMAT(updated_at,'%Y-%m-%d %H:%i:%s') as fecha"))->where(['culRec'=>1,'estRec'=>1,'idFic'=>$request['idficha']])->whereIn('idRec', explode(',',$request['idReceptor']))->get()->toArray();
        }else{
            $reseptores = Receptores::select("idFic","idRec",DB::raw("DATE_FORMAT(updated_at,'%Y-%m-%d %H:%i:%s') as fecha"))->where(['culRec'=>1,'estRec'=>1,'idFic'=>$request['idficha']])->get()->toArray();
        }
        
        if($reseptores){
            foreach ($reseptores as $key) {
            $fichapdf = $this->generar_pdf_ficha($key['idFic'],$key['idRec'],'storage/fichamonitoreo/ficha'.$key['idFic']);
            Receptores::where(['idRec'=>$key['idRec']])->update(['updated_at'=>$key['fecha'],'fichapdf'=>$fichapdf]);
            }
        }
    }
    

    public function guardar_pregunta(Request $request){
        date_default_timezone_set('America/Lima');
        $r_datos = array();    
        $idficha = $request['idficha'];    
        if($request['datos']){
            $datos = explode("&&",$request['datos']);
            if($datos){
                for ($i=0; $i < count($datos); $i++) {
                        $r_datos[] = explode("||",$datos[$i]);
                    }
            }
            if($r_datos){
                    foreach ($r_datos as $key) {
                        $fila = array();
                        $idpregunta        = $key[0];
                        $fila['idFic']     = $request['idficha'];
                        if($key[1]=='ELIMINAR') $fila['estPre'] = 0;
                        $fila['ordPre']    = ($key[2])?$key[2]:0;
                        $fila['SalLinPre'] = ($key[3]=='true')?1:0;
                        $fila['gruPre']    = ($key[4])?$key[4]:NULL;
                        $fila['nroPre']    = ($key[5])?$key[5]:0;
                        $fila['textPre']   = ($key[6])?$key[6]:NULL;
                        $fila['tipPre']    = ($key[7])?$key[7]:NULL;
                        $fila['altPre']    = ($key[8])?$key[8]:NULL;
                        $fila['adjArcPre'] = ($key[9]=='true')?1:0;
                        $fila['camOblPre'] = ($key[10]=='true')?1:0;
                        $fila['obsPre']    = ($key[11])?$key[11]:NULL;
                        if(Preguntas::where('idPre',$idpregunta)->get()->toArray()){
                            Preguntas::where('idPre',$idpregunta)->update($fila);
                         }else{
                            Preguntas::insert($fila);
                         }

                    }
            }
        }
    }

    public function listar_ie_respuesta(Request $request){
        $info['ficha']       = Fichas::where('idFic',$request['idficha'])->get()->toArray()[0];
        $info['resumensino'] = $this->ver_resumen_si_no($request['idficha']);
        $info['resumenipl']  = $this->ver_resumen_inicio_proceso_logrado($request['idficha']);
        DB::connection('ficha')->select("SET lc_time_names = 'es_ES';");
       
        //$info['institucion'] = Fichas::select("-*",DB::raw("IF(receptores.updated_at,DATE_FORMAT(receptores.updated_at,'%d/%m/%Y'),UPPER(MONTHNAME(receptores.fecProRec))) as fechaficha"),DB::raw("IF(NOW() between iniFic and  DATE_ADD(finFic,INTERVAL 1 DAY),1,0) as habilitado"))
        //->join('receptores','fichas.idFic','receptores.idFic')
        //->where(['receptores.estRec'=>1,'fichas.estFic'=>1,'fichas.idFic'=>$request['idficha']])->get()->toArray();
        //IF(R.updated_at,DATE_FORMAT(R.updated_at,'%d/%m/%Y'),UPPER(MONTHNAME(R.fecProRec))) as fechaficha, 
        $info['institucion'] =  DB::connection('ficha')->select("SELECT 
        R.idRec,
        R.nomRec,
        R.apePatRec,
        R.apeMatRec,
        R.culRec,
        F.idFic,
        R.codlocRec,
        R.redRec,
        R.insRec,
        F.modFic,
        R.disRec,
        R.dniRec,
        R.telRec,
        R.corRec,
        R.nroVisRec,
        R.nivRec,
        R.fichapdf,
        R.textModalidadRec,
        CONCAT(R.nomRec,IF(R.apePatRec,CONCAT(' ',R.apePatRec),''),IF(R.apeMatRec,CONCAT(' ',R.apeMatRec),'')) as dirRec,
        IF(R.idEspRec,CONCAT(esp_nombres,' ',esp_apellido_paterno,' ',esp_apellido_materno),'') as especialista,
        
        DATE_FORMAT(IFNULL(Rd.fecIniAplRec,R.updated_at),'%d/%m/%Y') as fechaficha,
        IF(NOW() between iniFic and DATE_ADD(F.finFic,INTERVAL 1 DAY),1,0) as habilitado 
        FROM fichas F 
        INNER JOIN receptores R on F.idFic = R.idFic 
        LEFT JOIN receptores_deta Rd ON R.idRec=Rd.idRec
        LEFT JOIN siic01ugel01gob_directores.especialistas E ON R.idEspRec = E.idespecialista 
        WHERE R.estRec = 1 and F.estFic = 1 and F.idFic = ".$request['idficha']);

        echo json_encode($info);
    }
    
    public function ver_resumen_si_no($idficha){
        $query = DB::connection('ficha')->select("SELECT P.ordPre,P.gruPre,P.nroPre,P.textPre,
        SUM(IF(R.resRdd='SI',1,0)) as res_si,
        SUM(IF(R.resRdd='NO',1,0)) as res_no,
        CONCAT(ROUND((SUM(IF(R.resRdd='SI',1,0))/COUNT(*))*100,2),'%') as porc_si,
        CONCAT(ROUND((SUM(IF(R.resRdd='NO',1,0))/COUNT(*))*100,2),'%') as porc_no
        FROM preguntas P 
        LEFT JOIN respuestas_detalles R ON P.idPre=R.idPre
        INNER JOIN receptores         U ON R.idRec = U.idRec and U.estRec=1 and U.culRec=1
        WHERE P.estPre=1 and R.estRdd=1 and P.tipPre='SI/NO' and  P.idFic='$idficha'
        GROUP BY P.ordPre,P.gruPre,P.nroPre,P.textPre
        ORDER BY P.ordPre ASC,P.nroPre ASC");
        $result = array();
        foreach ($query as $key){
            $result[] = (Array)$key;
        }
        return $result;
    }

    public function ver_resumen_inicio_proceso_logrado($idficha){
        $query = DB::connection('ficha')->select("SELECT 
        REPLACE(P.gruPre,'<br>',' ') as gruPre,
        COUNT(distinct(P.idPre)) as cantidad,
        COUNT(P.idPre) as total,
        SUM(IF(D.resRdd=1,1,0)) as inicio,
        SUM(IF(D.resRdd=2,1,0)) as proceso,
        SUM(IF(D.resRdd=3,1,0)) as logrado
        FROM respuestas_detalles D 
        INNER JOIN receptores R ON D.idRec=R.idRec
        INNER JOIN preguntas  P ON D.idPre=P.idPre
        WHERE D.estRdd=1 and R.estRec=1 and P.estPre=1 and P.tipPre='INICIO/PROCESO/LOGRADO' and R.idFic = $idficha
        GROUP BY P.gruPre");
        $result = array();
        foreach ($query as $key){
            $result[] = (Array)$key;
        }
        return $result;
     }
     
    public function listar_html_adicional(Request $request){
        echo json_encode(Preguntas::where('idPre',$request['idpregunta'])->get()->first());
    }

    public function guardar_html_adicional(Request $request){
        $idpregunta         = $request['idpregunta'];
        $data['htmlPre']    = $request['text_adicional'];
        $data['varHtmlPre'] = $request['var_html'];
        $data['varTitPre']  = $request['var_titulo'];
        $data['varInpPre']  = $request['var_input'];
        Preguntas::where('idPre',$idpregunta)->update($data);

        if($request['var_html']){
            $var_html   = explode(',',$request['var_html']);
            $var_titulo = explode(',',$request['var_titulo']);
            $var_input  = explode('***',$request['var_input']);
            //Variables_adicionales::where('idPre',$idpregunta)->delete();
            for ($i=0; $i < count($var_html); $i++) {
                $adi['varVaa']    = $var_html[$i];
                $adi['idPre']     = $idpregunta;
                $adi['varTitVaa'] = $var_titulo[$i];
                $adi['varInpVaa'] = $var_input[$i];
                $adi['idFic']     = $request['idficha'];
                
                if(Variables_adicionales::where('varVaa',$adi['varVaa'])->get()->toArray()){
                    Variables_adicionales::where('varVaa',$adi['varVaa'])->update($adi);
                }else{
                    Variables_adicionales::insert($adi);
                }
                
            }            
        }
        echo json_encode(1);
    }

    public function guardar_docente(Request $request){
        $idDoc = $request['idDoc'];
        $r_grado = array('1INI','2INI','3INI','4INI','5INI','1PRI','2PRI','3PRI','4PRI','5PRI','6PRI','1SEC','2SEC','3SEC','4SEC','5SEC','1EBE','2EBE','3EBE','4EBE','5EBE','6EBE','3EBI','4EBI','5EBI');
        $r_ciclo = array('I','I','II','II','II','III','III','IV','IV','V','V','VI','VI','VII','VII','VII','III','III','IV','IV','V','V','II','II','II');
        $doc['cicDoc'] = str_replace($r_grado,$r_ciclo,$request['graDoc']);
        $doc['nomDoc']    = $request['nomDoc'];
        $doc['apePatDoc'] = $request['apePatDoc'];
        $doc['apeMatDoc'] = $request['apeMatDoc'];
        $doc['dniDoc']    = $request['dniDoc'];
        $doc['graDoc']    = $request['graDoc'];
        $doc['secDoc']    = $request['secDoc'];
        $doc['areDoc']    = $request['areDoc'];
        $doc['telDoc']    = $request['telDoc'];
        $doc['corDoc']    = $request['corDoc'];        
        Docente::where('idDoc',$idDoc)->update($doc);

        $idRec = $request['idRec'];
        $rec['nroEstRec']    = $request['nroEstRec'];
        $rec['nroEstPreRec'] = $request['nroEstPreRec'];
        $rec['nroEstAsiRec'] = $request['nroEstAsiRec'];
        $rec['tipSerRec']    = $request['tipSerRec'];
        if($request['horainicioaplicacion']) $rec['fecIniAplRec'] = $request['fechaaplicacion'].' '.$request['horainicioaplicacion'];
        if($request['horaaplicacion'])       $rec['fecAplRec']    = $request['fechaaplicacion'].' '.$request['horaaplicacion'];
        if($rec){
        $rec['idRec'] = $idRec;
        if(Receptores_deta::where('idRec',$idRec)->get()->toArray()){
           Receptores_deta::where('idRec',$idRec)->update($rec);
        }else{
           Receptores_deta::insert($rec);
        }
        }
        
        $data = array();
        if($request['nroVisRec']) $data['nroVisRec'] = $request['nroVisRec'];
        if($data){
        Receptores::where('idRec',$idRec)->update($data);
        }
        
        return 1;
    }

    public function guardar_receptores(Request $request){
        $idRec = $request['idRec'];
        $data = array();
        if($request['nomRec']) $data['nomRec'] = $request['nomRec'];
        if($request['apePatRec']) $data['apePatRec'] = $request['apePatRec'];
        if($request['apeMatRec']) $data['apeMatRec'] = $request['apeMatRec'];
        if($request['dniRec']) $data['dniRec'] = $request['dniRec'];
        if($request['telRec']) $data['telRec'] = $request['telRec'];
        if($request['corRec']) $data['corRec'] = $request['corRec'];
        if($request['carRec']) $data['carRec'] = $request['carRec'];
        if($request['nroVisRec']) $data['nroVisRec'] = $request['nroVisRec'];
        if($data){
        Receptores::where('idRec',$idRec)->update($data);
        }
        
        $data = array();
        if($request['horainicioaplicacion']) $data['fecIniAplRec'] = $request['fechaaplicacion'].' '.$request['horainicioaplicacion'];
        if($request['horaaplicacion'])       $data['fecAplRec']    = $request['fechaaplicacion'].' '.$request['horaaplicacion'];
        if($request['AsiTecRec']) $data['AsiTecRec'] = $request['AsiTecRec'];
        if($request['conRec']) $data['conRec'] = $request['conRec'];
        if($request['recRec']) $data['recRec'] = $request['recRec'];
        if($request['comDirRec']) $data['comDirRec'] = $request['comDirRec'];
        if($request['impDirRec']) $data['impDirRec'] = $request['impDirRec'];
        if($request['comDocRec']) $data['comDocRec'] = $request['comDocRec'];
        if($request['impDocRec']) $data['impDocRec'] = $request['impDocRec'];
        if($request['comEspRec']) $data['comEspRec'] = $request['comEspRec'];
        if($request['impEspRec']) $data['impEspRec'] = $request['impEspRec'];
        if($data){
        $data['idRec'] = $idRec;
        if(Receptores_deta::where('idRec',$idRec)->get()->toArray()){
           Receptores_deta::where('idRec',$idRec)->update($data);
        }else{
           Receptores_deta::insert($data);
        }
        }
        return 1;
    }

    public function guardar_respuesta(Request $request){
        //$query = $this->db_b->query("SELECT *,IF(texto='-',grupo,texto) as texto FROM pregunta P WHERE P.estado=1 and P.idficha = $idficha ORDER BY orden ASC");
        $preg = Preguntas::where(['estPre'=>1,'idFic'=>$request['idficha']])->select("*",DB::raw("IF(textPre='-',gruPre,textPre) as texto"))->get()->toArray();
        //print_r($preg);
        foreach ($preg as $key) {
            if($request['p'.$key['idPre']] or $request['p'.$key['idPre']]=='0'){
                $reg = array();
                $reg['idRec']  = $request['idreceptor'];
                $reg['idPre']  = $key['idPre'];
                $reg['idFic']  = $request['idficha'];
                $reg['resRdd'] = (is_array($request['p'.$key['idPre']]))?implode(",",$request['p'.$key['idPre']]):$request['p'.$key['idPre']];
                $reg['obsRdd'] = (is_array($request['obs'.$key['idPre']]))?implode(",",$request['obs'.$key['idPre']]):$request['obs'.$key['idPre']];
                //Anexo
                if($request->hasfile('anexo'.$key['idPre'])){
                    $archivo = $request->file('anexo'.$key['idPre'])->store('public/fichamonitoreo/ficha'.$request['idficha'].'/receptor'.$request['idreceptor']);
                    $reg['arcRdd'] = Storage::url($archivo);
                }
                $detalle = Respuestas_detalles::where(['estRdd'=>1,'idRec'=>$reg['idRec'],'idPre'=>$reg['idPre']])->get()->toArray();
                if($detalle){
                    $idRdd = $detalle[0]['idRdd'];
                    Respuestas_detalles::where('idRdd',$idRdd)->update($reg);
                }else{
                    $idRdd = Respuestas_detalles::insertGetId($reg);
                }
                //Anexo

                //Variables
                if($key['varHtmlPre']){
                    //echo 'adicional';
                    foreach ( explode(',',$key['varHtmlPre']) as $var) {
                        $adi = array();
                        $adi['idRdd']  = $idRdd;
                        $adi['varVaa'] = $var;
                        $adi['valRaa'] = $request[$var];
                        $adi['idFic']  = $request['idficha'];
                        $adi['idRec']  = $request['idreceptor'];
                        $adi['idPre']  = $key['idPre'];
                        $adicional = Respuestas_adicionales::where(['estRaa'=>1,'idRdd'=>$adi['idRdd'],'varVaa'=>$adi['varVaa']])->get()->toArray();
                            if($adicional){
                                $idRaa = $adicional[0]['idRaa'];
                                Respuestas_adicionales::where('idRaa',$idRaa)->update($adi);
                            }else{
                                $idRaa = Respuestas_adicionales::insertGetId($adi);
                            }
                    }
                }
                //Variables

                //echo '<br>';
                //print_r($reg);

            }
        }
        $info['ficha_respondida'] = $this->ficha_respondida($request['idficha'],$request['idreceptor']);
        if($info['ficha_respondida']['grupo_respondido']==1){ 
            $key = Receptores::select("idFic","idRec","fichapdf",DB::raw("DATE_FORMAT(updated_at,'%Y-%m-%d %H:%i:%s') as fecha"))->where('idRec',$request['idreceptor'])->get()->toArray()[0];
            if($key['fichapdf']){
                $fichapdf = $this->generar_pdf_ficha($key['idFic'],$key['idRec'],'storage/fichamonitoreo/ficha'.$key['idFic']);
                Receptores::where(['idRec'=>$key['idRec']])->update(['updated_at'=>$key['fecha'],'fichapdf'=>$fichapdf]);
            }
            Receptores::where('idRec',$request['idreceptor'])->update(['culRec'=>1]); 
            
        }
        echo json_encode($info);
    }

    

    public function enviar_ficha_ugel01(Request $request){
        $key = Receptores::select("idFic","idRec","fichapdf",DB::raw("DATE_FORMAT(updated_at,'%Y-%m-%d %H:%i:%s') as fecha"))->where('idRec',$request['idreceptor'])->get()->toArray()[0];
        if($key['fichapdf']){
            $fichapdf = $this->generar_pdf_ficha($key['idFic'],$key['idRec'],'storage/fichamonitoreo/ficha'.$key['idFic']);
            Receptores::where(['idRec'=>$key['idRec']])->update(['updated_at'=>$key['fecha'],'fichapdf'=>$fichapdf]);
        }
        echo json_encode(Receptores::where('idRec',$request['idreceptor'])->update(['culRec'=>1]));
    }

    /*function ficha_respondida($idficha,$idreceptor){
        $query = DB::connection('ficha')->select("SELECT 
        count(*) as cantidad_preguntas, 
        SUM(IF(D.idrespuestadetalle,1,0)) as cantidad_respondidas,
        IF(count(*) = SUM(IF(D.idrespuestadetalle,1,0)),1,0) as grupo_respondido
        FROM pregunta P 
        LEFT JOIN respuesta_detalle D ON P.idpregunta = D.idpregunta and D.estado=1 and D.idreceptor = $idreceptor
        WHERE P.estado=1 and P.idficha = $idficha");
        return ($query)?(Array)$query[0]:false;
	}*/

    function grupo_ficha_respondida($idficha,$idreceptor){
        $query = DB::connection('ficha')->select("SELECT 
        P.gruPre,
        SUM(P.SalLinPre) as SalLinPre,
        count(*) as cantidad_preguntas, 
        SUM(IF(D.idRdd,1,0)) as cantidad_respondidas,
        IF(count(*) = SUM(IF(D.idRdd,1,0)),1,0) as grupo_respondido
        FROM preguntas P 
        LEFT JOIN respuestas_detalles D ON P.idPre = D.idPre and D.estRdd=1 and D.idRec = $idreceptor
        WHERE P.estPre=1 and P.idFic = $idficha 
        GROUP BY P.gruPre
        ORDER BY MAX(P.ordPre) ASC");
        $result = array();
        foreach ($query as $key){
            $result[] = (Array)$key;
        }
        return $result;
	}
    
    function ver_ficha_respondida($idficha,$idreceptor,$grupo=false){
        $where = ($grupo)?"and P.gruPre='$grupo'":"";
        $query = DB::connection('ficha')->select("SELECT *,P.idPre FROM preguntas P 
        LEFT JOIN respuestas_detalles D ON P.idPre = D.idPre and D.estRdd=1 and D.idRec = $idreceptor
        WHERE P.estPre=1 $where and P.idFic = $idficha 
        ORDER BY P.ordPre ASC,P.nroPre ASC");
        $result = array();
        foreach ($query as $key){
            for ($i=0; $i < count($query); $i++) {
                if($query[$i]->varHtmlPre and $query[$i]->idRdd){
                   $query[$i]->adicional = $this->object_a_array(DB::connection('ficha')->select("SELECT*FROM respuestas_adicionales WHERE estRaa=1 and idRdd=".$query[$i]->idRdd." and varVaa IN('".str_replace(",","','",$query[$i]->varHtmlPre)."')"));
                }else{
                   $query[$i]->adicional = false;
                }
            }
            $result[] = (Array)$key;
        }
        return $result;
	}

    function object_a_array($query){
        $result = array();
        if($query){
            foreach ($query as $key){
                $result[] = (Array)$key;
            }
        }else{
            $result = false;
        }
        return $result;
    }

    function ficha_respondida($idficha,$idreceptor){
        $query = DB::connection('ficha')->select("SELECT 
        count(*) as cantidad_preguntas, 
        SUM(IF(D.idRdd,1,0)) as cantidad_respondidas,
        IF(count(*) = SUM(IF(D.idRdd,1,0)),1,0) as grupo_respondido
        FROM preguntas P 
        LEFT JOIN respuestas_detalles D ON P.idPre = D.idPre and D.estRdd=1 and D.idRec = $idreceptor
        WHERE P.estPre=1 and P.idFic = $idficha");        
        return ($query)?(Array)$query[0]:false;
    }

    function registro_receptor_codlocal($idficha,$codlocal,$data){
        $result = Receptores::where(['idFic'=>$idficha,'codlocRec'=>$codlocal,'estRec'=>1])->get()->toArray();
        $result = ($result)?$result[0]:false;
        if($result){
            return $result['idRec'];
        }else{
            return Receptores::insertGetId($data);
        }
    }

    function registro_receptor_codmod($idficha,$codmod,$data){
        $result = Receptores::where(['idFic'=>$idficha,'codmodRec'=>$codmod,'estRec'=>1])->get()->toArray();
        $result = ($result)?$result[0]:false;
        if($result){
            return $result['idRec'];
        }else{
            return Receptores::insertGetId($data);
        }
    }

    function actualizar_receptores($idRec,$data){
        Receptores::where('idRec',$idRec)->update($data);
        return $idRec;
    }

}
