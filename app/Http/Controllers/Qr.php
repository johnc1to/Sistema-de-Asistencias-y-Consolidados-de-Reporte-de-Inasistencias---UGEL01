<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Codigo_qr;
use DB;

class Qr extends Controller
{
    function Codigo(){
        echo "Codigo Qr";
    }

    public function listar_qr(){
        $info['base_url'] = 'http://qr.ugel01.gob.pe/?qr=';
        return view('qr/listar_qr',$info);
    }

    public function tabla_qr(){
        $sql = DB::connection('Qr')->select("SELECT C.idQr,C.nomQr,C.urlQr,C.urlCorQr,C.urlCorQr as codQr,COUNT(V.idQr) AS cantidad FROM codigo_qr C 
        LEFT JOIN visitas V ON C.idQr=V.idQr
        WHERE C.estQr=1
        GROUP BY C.idQr,C.nomQr,C.urlQr,C.urlCorQr");
        echo json_encode($sql);
    }

    public function guardar_qr(Request $request){
        $idQr=$request['idQr'];
        $ins['nomQr'] = $request['nombre'];
        $ins['urlQr'] = $request['url'];
        if($request['urlCorQr']){ $ins['urlCorQr'] = $request['urlCorQr']; };
        if($idQr){
            Codigo_qr::where('idQr',$idQr)->update($ins);
            $ins['id'] = $idQr;
        }else{
            $ins['id'] = Codigo_qr::insertGetId($ins);
            Codigo_qr::where('idQr',$ins['id'])->update(['urlCorQr'=>dechex(11111+$ins['id']*11)]);
            $ins['urlCorQr'] = dechex(11111+$ins['id']*11);
        }       
        return $ins;
    }

    public function eliminar_qr(Request $request){
        $idQr=$request['idQr'];
        Codigo_qr::where('idQr',$idQr)->update(['estQr'=>0]);
        return 1;
    }

    public function reporte_qr(){
        $info['base_url'] = 'http://qr.ugel01.gob.pe/?qr=';
        $info['data'] = DB::connection('Qr')->select("SELECT idQr,nomQr,urlQr,urlCorQr FROM codigo_qr WHERE estQr=1");
        return view('qr/reporte_qr',$info);
    }

    public function grafico_qr(Request $request){
        $idQr=$request['idQr'];
        $info['base_url'] = 'http://qr.ugel01.gob.pe/?qr=';
        $info['data']     = DB::connection('Qr')->select("SELECT idQr,nomQr,urlQr,urlCorQr FROM codigo_qr WHERE idQr=$idQr");
        $info['resumen']  = DB::connection('Qr')->select("SELECT COUNT(ipVis) AS total, COUNT(DISTINCT ipVis) AS unicos FROM visitas WHERE idQr=$idQr");
        $info['grafico1'] = DB::connection('Qr')->select("SELECT YEAR(fecQr) AS año, MONTH(fecQr) AS mes, DAY(fecQr) AS dia, COUNT(ipVis) AS total, COUNT(DISTINCT ipVis) AS unicos FROM visitas WHERE idQr=$idQr GROUP BY YEAR(fecQr), MONTH(fecQr), DAY(fecQr)");
        return view('qr/grafico_qr',$info);
    }

    public function acceso_directo(){
        $info['data'] = DB::connection('Qr')->select("SELECT idQr,nomQr,urlQr FROM codigo_qr WHERE estQr=1");
        return view('qr/acceso_directo',$info);
    }


    
}
