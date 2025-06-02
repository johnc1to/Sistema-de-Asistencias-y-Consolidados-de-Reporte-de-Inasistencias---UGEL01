<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class Racio extends Controller{
    
    public function index(){
        $info['session'] = session()->get('siic01_admin');
        if($info['session']){
            return view("racio/index",$info);
        }
    }

    public function verreqseccincrementosecciones(Request $request){
        $where = '';
        $where .= ($request['idnivel'])?' and idnivel='.$request['idnivel']:'';
        $data=DB::connection('notificacion')->select("SELECT 
            I.codmod,
            I.codlocal,
            I.institucion,
            I.distrito,
            I.tecnico,
            I.jornada,
            I.horas,
            I.nivel,
            I.gestion,
            I.turno,
            S.grado,
            S.seccincremento,
            I.horas*S.seccincremento as bolsahoras,
            S.aulafisica,
            S.mobiliario,
            R.cod_reclamo,
            R.idreclamo,
            MAX(IF(A.file_tipo_tramite='Oficio simple de solicitud de requerimiento de incremento de seccion',A.archivo,NULL)) as adj1,
            MAX(IF(A.file_tipo_tramite='Copia de la ficha de resumen extraida del aplicativo SIAGIE y PAP aprobado 2022',A.archivo,NULL)) as adj2
            FROM siic01ugel01gob_directores.iiee_a_evaluar_RIE I 
            INNER JOIN reclamos_detallerequerimientoseccion  S ON I.codmod = S.codmod
            INNER JOIN reclamos R ON S.idreclamo = R.idreclamo
            INNER JOIN reclamos_adjunto A ON R.idreclamo = A.idreclamo
            WHERE R.estado=1 $where
            GROUP BY 
            I.codmod,
            I.codlocal,
            I.institucion,
            I.distrito,
            I.tecnico,
            I.jornada,
            I.horas,
            I.nivel,
            I.gestion,
            I.turno,
            S.grado,
            S.seccincremento,
            S.seccincremento,
            S.aulafisica,
            S.mobiliario,
            R.cod_reclamo,
            R.idreclamo
            ORDER BY I.institucion ASC,I.idnivel ASC");
        
        echo json_encode($data);
    }

    public function verreqseccincrementoie(Request $request){
        $where = '';
        $where .= ($request['idnivel'])?' and idnivel='.$request['idnivel']:'';
        $data=DB::connection('notificacion')->select(
            "SELECT 
            I.codmod,
            I.codlocal,
            I.institucion,
            I.distrito,
            I.tecnico,
            I.jornada,
            I.nivel,
            I.gestion,
            I.turno,
            R.cod_reclamo,
            R.idreclamo,
            MAX(IF(A.file_tipo_tramite='Oficio simple de solicitud de requerimiento de incremento de seccion',A.archivo,NULL)) as adj1,
            MAX(IF(A.file_tipo_tramite='Copia de la ficha de resumen extraida del aplicativo SIAGIE y PAP aprobado 2022',A.archivo,NULL)) as adj2
            FROM siic01ugel01gob_directores.iiee_a_evaluar_RIE I 
            INNER JOIN reclamos_detallerequerimientoseccion  S ON I.codmod = S.codmod
            INNER JOIN reclamos R ON S.idreclamo = R.idreclamo
            INNER JOIN reclamos_adjunto A ON R.idreclamo = A.idreclamo
            WHERE R.estado=1 $where
            GROUP BY 
            I.codmod,
            I.codlocal,
            I.institucion,
            I.distrito,
            I.tecnico,
            I.jornada,
            I.nivel,
            I.gestion,
            I.turno,
            R.cod_reclamo,
            R.idreclamo
            ORDER BY I.institucion ASC,I.idnivel ASC");
            echo json_encode($data);
    }

}