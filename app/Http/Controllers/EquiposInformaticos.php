<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Form_equipos_informaticos;
use App\Models\Tipo_equipos_informaticos;
use DB;

class EquiposInformaticos extends Controller{
    
    public function inv_registro(){
        $sesion = session()->get('siic01');
        if($sesion){
         $info['layout']='layout_director/cuerpo';
         $info['tipo'] = Tipo_equipos_informaticos::where(['estado'=>1])->get()->toArray();
         $info['codlocal'] = $sesion['codlocal'];//0329003  0325498
         $info['id_contacto'] = $sesion['id_contacto'];
         return view("equiposInformaticos/inv_registro",$info);
        }else{
            return redirect('https://siic01.ugel01.gob.pe/');
        }
    }
    
    public function get_inv(Request $request){
        $codlocal = $request['codlocal'];
        $idtipo   = $request['idtipo'];
        echo json_encode(Form_equipos_informaticos::where(['codlocal'=>$codlocal,'idtipo'=>$idtipo,'estado'=>1])->get()->toArray());
    }
    
    public function guardar_inv(Request $request){
        //dd($request->all());
        $data = explode('&&',$request['data']);
        if($data){
            foreach ($data as $fila) {
                $col = explode('||',$fila);
                print_r($col);
                echo '<br>';
                $key = array();
                $idInf = $col['0'];
                
                if($idInf){
                    if($col['1']=='ELIMINAR'){
                        $key['estado'] = 0;
                    }else{
                        $key['codigo_sbn']   = $col['2'];
                        $key['denominacion'] = $col['3'];
                        $key['marca']        = $col['4'];
                        $key['modelo']       = $col['5'];
                        $key['color']        = $col['6'];
                        $key['serie']        = $col['7'];
                        $key['observacion']  = $col['8'];
                        $key['codlocal']     = $request['codlocal'];
                        $key['id_contacto']  = $request['id_contacto'];
                        $key['idtipo']       = $request['idtipo'];
                    }
                        Form_equipos_informaticos::where('idInf',$idInf)->update($key);
                }else{
                        $key['codigo_sbn']   = $col['2'];
                        $key['denominacion'] = $col['3'];
                        $key['marca']        = $col['4'];
                        $key['modelo']       = $col['5'];
                        $key['color']        = $col['6'];
                        $key['serie']        = $col['7'];
                        $key['observacion']  = $col['8'];
                        $key['codlocal']     = $request['codlocal'];
                        $key['id_contacto']  = $request['id_contacto'];
                        $key['idtipo']       = $request['idtipo'];
                        Form_equipos_informaticos::insert($key);
                }
                
                
            }
        }
    }
    
    
    public function inv_resumen(){
         $sesion = session()->get('siic01_admin');
        if($sesion){
        $info['tipo'] = Tipo_equipos_informaticos::where(['estado'=>1])->get()->toArray();
        return view("equiposInformaticos/inv_resumen",$info);
        }else{
            return redirect('https://siic01.ugel01.gob.pe/');
        }
    }
    
    public function get_inv_resumen(Request $request){
        $idtipo   = $request['idtipo'];
        $data=DB::select("
        SELECT R.codlocal,R.institucion,COUNT(DISTINCT(F.idInf)) as total FROM iiee_a_evaluar_RIE R 
        INNER JOIN siic01_formularios.form_equipos_informaticos F ON R.codlocal=F.codlocal
        WHERE F.estado=1 and idtipo = '$idtipo' 
        GROUP BY R.codlocal,R.institucion");
        echo json_encode($data);
    }
    
    
    
}