<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Especialistas;
use DB;

class Rrhh extends Controller
{
    public function popup_especialista(Request $request){
     //$mantenimiento = Mantenimiento::where(['estado'=>1,'idmantenimiento'=>$idmantenimiento])->select('abreviatura','descripcion')->get()->first();
     $info['funcion']           = $request['funcion'];
     $info['especialista_creo'] = $request['especialista_creo'];
     
     if($request['id_area']){
        $info['areas'] = $this->ver_oficina($request['id_area']);
     }else{
        $info['areas'] = $this->lista_oficinas();
        $info['areas'][] = $this->lista_oficinas(2)[0];
     }

     if($request['id_oficina'] and $request['id_oficina'] != $request['id_area'] ){
        $info['areas'][0]->oficina = $this->ver_oficina($request['id_oficina']);
     }else{
        for ($i=0; $i < count($info['areas']); $i++) {
          $info['areas'][$i]->oficina = $this->lista_oficinas($info['areas'][$i]->SedeOficinaId);
        }
     }

     $info['data'] = Especialistas::where(['estado'=>1,'idespecialista'=>$request['idespecialista'] ])->get()->first();
     
        return view('rrhh/popup_especialista',$info);
    }

    public function validar_dni_especialista(Request $request){
        $info = Especialistas::where(['estado'=>1,'ddni'=>$request['dni'] ])->get()->first();
        echo json_encode($info);
    }

    public function guardar_especialista(Request $request){
        
        $id = $request['idespecialista'];
        unset($request['idespecialista']);
        unset($request['_token']);
        $request['usuario'] = $request['ddni'];
        if($id){      
            unset($request['especialista_creo']);
            Especialistas::where('idespecialista',$id)->update($request->all());
        }else{
            Especialistas::insert($request->all());
        }
        echo json_encode(1);
    }

    public function eliminarespecialista(Request $request){
        date_default_timezone_set('America/Lima');
        unset($request['especialista_creo']);
        $data['estado'] = 0;
        $data['especialista_elimino'] = $request['especialista_elimino'];
        $data['fecha_elimino'] = date('Y-m-d H:i:s');        
        Especialistas::where('idespecialista',$request['idespecialista'])->update($data);
    }

    public function lista_oficinas($id = 62){
		$query = DB::select("SELECT Nivel,SedeOficinaId,Descripcion,DescripcionCorta,PadreSedeOficinaId from t_SedeOficina where SedeOficinaId = $id or (Estado = 1 and PadreSedeOficinaId = $id)");
		return $query;
	}

    public function  ver_oficina($id = 62){
		$query = DB::select("SELECT Nivel,SedeOficinaId,Descripcion,DescripcionCorta,PadreSedeOficinaId from t_SedeOficina where SedeOficinaId = $id");
		return $query;
	}
}
