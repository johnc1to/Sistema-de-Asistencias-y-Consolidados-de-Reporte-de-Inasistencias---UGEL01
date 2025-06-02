<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Form_epps;
use DB;

class Epps extends Controller{
    
    public function epps_coodinador(){
        $sesion = session()->get('siic01_admin');
         if($sesion){
         $info['layout']='layout_especialista/cuerpo';
         $info['idequipo_codlocal'] = $sesion['id_oficina'];
         return view("epps/epps_registro",$info);
         }
    }
    
    public function epps_director(){
        $sesion = session()->get('siic01');
         $info['layout']='layout_director/cuerpo';
         $info['idequipo_codlocal']=$sesion['codlocal'];//0329003  0325498
         return view("epps/epps_registro",$info);
    }
    
    public function get_epps(Request $request){
        $idequipo_codlocal = $request['idequipo_codlocal'];
        echo json_encode(Form_epps::where('idequipo_codlocal',$idequipo_codlocal)->orderBy('apellidos_y_nombres')->get()->toArray());
    }
    
    public function guardar_epps(Request $request){
        $data = explode('&&',$request['data']);
        if($data){
            foreach ($data as $fila) {
                $col = explode('||',$fila);
                print_r($col);
                echo '<br>';
                $key = array();
                $id_epps           = $col['0'];
                //$key['apellidos_y_nombres'] = $col['1'];
                $key['sexo']              = $col['2'];
                $key['lentes']            = $col['3'];
                $key['mascarilla']        = $col['4'];
                $key['filtro']            = $col['5'];
                $key['guantes_jebe']      = $col['6'];
                $key['guantes_banda']     = $col['7'];
                $key['guantes_latex']     = $col['8'];
                $key['guantes_jebe_industrial'] = $col['9'];
                $key['guantes_multiflex'] = $col['10'];
                $key['casco']             = $col['11'];
                $key['barbiquero']        = $col['12'];
                $key['zapato']            = $col['13'];
                $key['zapato_talla']      = $col['14'];
                $key['uniforme']          = $col['15'];
                $key['pantalon_talla']    = $col['16'];
                $key['camisa_talla']      = $col['17'];
                $key['chompa']            = $col['18'];
                $key['chompa_talla']      = $col['19'];
                $key['bloqueador_solar']  = $col['20'];
                $key['botin']             = $col['21'];
                $key['botin_talla']       = $col['22'];
                $key['bota']              = $col['23'];
                $key['bota_talla']        = $col['24'];
                $key['casco_moto']        = $col['25'];
                $key['rodillera_codera']  = $col['26'];
                $key['guardapolvo']       = $col['27'];
                $key['talla_guardapolvo'] = $col['28'];
                $key['cofia']             = $col['29'];
                $key['mandil_plomado']    = $col['30'];
                Form_epps::where('id_epps',$id_epps)->update($key);
            }
        }
    }
    
    
    public function epps_resumen(){
        return view("epps/epps_resumen");
    }
    
    public function get_resumen(){
        $data=DB::connection('formularios')->select("
        SELECT institucion,count(*) as total,
        SUM(IF(modificado_at IS NULL,0,1)) as lleno,
        SUM(IF(modificado_at IS NULL,1,0)) as falta 
        FROM form_epps 
        WHERE codlocal>0
        GROUP BY institucion  
        UNION
        SELECT 
        S.Descripcion as institucion,
        count(*) as total,
        SUM(IF(`modificado_at` IS NULL,0,1)) as lleno,
        SUM(IF(`modificado_at` IS NULL,1,0)) as falta
        FROM form_epps E 
        INNER JOIN siic01ugel01gob_directores.t_SedeOficina S ON E.idequipo_codlocal=S.SedeOficinaId
        WHERE codlocal=0
        GROUP BY S.Descripcion");
        echo json_encode($data);
    }
    
    
    
}