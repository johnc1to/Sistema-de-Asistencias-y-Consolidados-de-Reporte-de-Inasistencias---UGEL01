<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doc_documento;
use App\Models\Iiee_a_evaluar_rie;
use DB;//Conexion a BD 

class DocumentosRemitidos extends Controller
{
    public function listar_logosie(){
        //CETPROS
        $data = Iiee_a_evaluar_rie::where(['idmodalidad'=>1])->select('institucion','logo')->get()->toArray();
        //print_r($data);
        foreach ($data as $key) {
            if($key['logo']!=NULL and $key['logo']!=''){
            $origen  ='https://siapcetpro.ugel01.gob.pe/public'.$key['logo'];
            $destino = str_replace('/storage','storage',$key['logo']);
            echo '<br><br>origen=> '.$origen;
            echo '<br>destino=> '.$destino;
            copy($origen,$destino);
            }
        }
        
        //DOCUMENTOS SOLICITADOS
        $data = Doc_documento::where(['etapa'=>'COMPLETO','id_tipo'=>'158'])->select('codlocal','idmodalidad','archivo')->get()->toArray();
        //print_r($data);
        foreach ($data as $key) {
            //copy
            $origen='https://siic01.ugel01.gob.pe/'.$key['archivo'];
            $nombre = str_replace('DocumentosRemitidos2/LOGODELAIE/LOGODELAIE/Todos/','',$key['archivo']);
            //$destino='./storage/app/public/logoie/'.$nombre;
            $destino='storage/logoie/'.$nombre;
            echo '<br><br>origen=> '.$origen;
            echo '<br>destino=> '.$destino;
            copy($origen,$destino);
            Iiee_a_evaluar_rie::where(['codlocal'=>$key['codlocal'],'idmodalidad'=>$key['idmodalidad']])->update(['logo'=>'/'.$destino]);
        }
        //public_html/storage/app/public/logoie
    }
    
    
    
}