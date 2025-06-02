<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use DB;

class Cetpro extends Controller{
    
    public function verprogramas(){
        return view('cetpro/verprogramas');
    }
    
    public function listar_Auxiliar(){
        $info['data'] = array();
        return view('cetpro/listar_Auxiliar',$info);
    }
    
    public function listarprogramas(){
        $data['programa'] = DB::connection('cetpromin')->select("SELECT 
            P.idPro,
            P.codCatCnn,
            RIE.institucion,
            RIE.distrito,
            RIE.gestion,
            P.secEcoPee,
            P.famProPee,
            P.actEcoPee,
            P.proEstPee,
            P.nivForPee,
            P.credPee,
            P.horPee,
            P.creado_at,
            DATE_FORMAT(P.creado_at,'%d/%m/%Y') as t_creado,
            COUNT(DISTINCT(D.idUdd)) as cantidUdd,
            COUNT(DISTINCT(Ca.idCap)) as cantidCap,
            COUNT(DISTINCT(I.idIll)) as cantidIll
            FROM programas_estudio  P 
            LEFT JOIN programas_estudio_competencias C ON P.idPro=C.idPro and C.estPec=1
            LEFT JOIN unidades_didacticas D ON C.idPec=D.idPec and D.estUdd=1
            LEFT JOIN capacidades Ca ON D.idUdd=Ca.idUdd and Ca.estCap=1
            LEFT JOIN indicadores_logro I ON Ca.idCap=I.idCap and I.estIll=1
            LEFT JOIN siic01ugel01gob_directores.iiee_a_evaluar_RIE RIE ON P.codModPee = RIE.codmod
            WHERE P.estPee=1
            GROUP BY P.idPro,
            P.codCatCnn,
            RIE.institucion,
            RIE.distrito,
            RIE.gestion,
            P.secEcoPee,
            P.famProPee,
            P.actEcoPee,
            P.proEstPee,
            P.nivForPee,
            P.credPee,
            P.horPee,
            P.creado_at 
            ORDER BY RIE.institucion ASC");
        echo json_encode($data);
    }
    
    
    
}
