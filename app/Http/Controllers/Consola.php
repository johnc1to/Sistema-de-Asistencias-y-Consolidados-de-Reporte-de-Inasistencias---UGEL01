<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Variablesgobales;
use App\Models\Ventanilla;
use DB;

class Consola extends Controller{
    
 public function consola(){
     $info['generar_sinad']      = Variablesgobales::where('variable','generar_sinad')->get()->toArray();
     $info['varias_ventanillas'] = Variablesgobales::where('variable','varias_ventanillas')->get()->toArray();
     $info['variablesgobales']   = Variablesgobales::where('estado','1')->get()->toArray();
     $info['ventanilla']       = Ventanilla::select('*')->get()->toArray();
     $info['ip'] = $this->get_client_ip();
     //dd($info);
     return view("consola/index",$info);
 }
 
 public function guardarconsola(Request $request){
     $variablesgobales   = Variablesgobales::where('estado','1')->get()->toArray();
     foreach ($variablesgobales as $key){
         $valor = ($request[$key['variable']])?$request[$key['variable']]:0;
         Variablesgobales::where(['estado'=>1,'variable'=>$key['variable']])->update(['valor'=>$valor ]);
     }
     
     $ventanilla = Ventanilla::select('*')->get()->toArray();
     foreach ($ventanilla as $key){
        $valor = ($request['v'.$key['ventanilla']])?$request['v'.$key['ventanilla']]:0;
        Ventanilla::where(['ventanilla'=>$key['ventanilla']])->update(['estado'=>$valor]);

     }
     return redirect()->route('consola');
 }
 
 public function distribucionventanillas(){
     $result = DB::connection('notificacion')->select("SELECT V.ventanilla,COUNT(*) as cantidad FROM 
        ventanilla V 
        LEFT JOIN `session` S ON V.ventanilla = S.ventanilla AND TIMESTAMPDIFF(MINUTE ,S.`ingreso`,NOW())<29 and S.salida IS NULL
        GROUP BY V.ventanilla  
        ORDER BY `ventanilla` ASC");
        
    $menor = DB::connection('notificacion')->select("SELECT V.ventanilla,COUNT(*) as cantidad FROM 
        ventanilla V 
        LEFT JOIN `session` S ON V.ventanilla = S.ventanilla AND TIMESTAMPDIFF(MINUTE ,S.`ingreso`,NOW())<29 and S.salida IS NULL
        WHERE V.estado=1
        GROUP BY V.ventanilla  
        ORDER BY `cantidad` ASC")[0]->ventanilla;

    return view("consola/distribucionventanillas",array('result'=>$result,'menor'=>$menor));
 }
 
 //Obtiene la IP del cliente
  public function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
    
}