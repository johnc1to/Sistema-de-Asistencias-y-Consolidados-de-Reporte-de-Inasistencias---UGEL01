<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Especialistas;
use myPHPnotes\Microsoft\Auth;
use myPHPnotes\Microsoft\Handlers\Session;
use myPHPnotes\Microsoft\Models\User;
use Illuminate\Support\Facades\Http;
use App\Models\Encargatura_tba_usuario;
use DB;

class Office365 extends Controller{
    
    //APLICACION
    public function office365_aplicacion(){
        session_start();
        $tenant = "common";
        $cliente_id = "d230b15d-cab6-403b-84ea-86f91eb975d6";
        $cliente_secret = "bBn8Q~3bEzXhfE4BtiSBuUK9IJRc8-FO.gxn_brD";
        $callback = "https://aplicacion.ugel01.gob.pe/public/callback_aplicacion";
        $scropes = ["User.Read"];
        $microsoft = new Auth($tenant, $cliente_id, $cliente_secret, $callback, $scropes);
        header("location: ".$microsoft->getAuthUrl());
        
    }
    
    public function callback_aplicacion(Request $request){
        session_start();
        $auth = new Auth(Session::get("tenant_id"), Session::get("client_id"),Session::get("client_secret"),Session::get("redirect_uri"),Session::get("scopes"));
        //dd($code);
        $tokens = $auth->getToken($request->input('code'));
        $accessToken = $tokens->access_token;
        $auth->setAccessToken($accessToken);
        $user = new User;
        echo "Name: ".$user->data->getDisplayName()."<br>";
        echo "Email: ".$user->data->getUserPrincipalName()."<br>";
    }
    
    //ADJUDICACION
    public function office365_adjudicacion(){
        session_start();
        $tenant = "common";
        $cliente_id = "4f54fd7d-714c-4d3a-a308-49a463bfb848";
        $cliente_secret = "NYk8Q~spYtY~sNjGXAoHMdgKRptQUf4Pbl-eAasJ";
        $callback = "https://aplicacion.ugel01.gob.pe/public/callback_adjudicacion";
        $scropes = ["User.Read"];
        $microsoft = new Auth($tenant, $cliente_id, $cliente_secret, $callback, $scropes);
        header("location: ".$microsoft->getAuthUrl());
    }
    
    public function callback_adjudicacion(Request $request){
        date_default_timezone_set('America/Lima');
        session_start();
        $auth = new Auth(Session::get("tenant_id"), Session::get("client_id"),Session::get("client_secret"),Session::get("redirect_uri"),Session::get("scopes"));
        $tokens = $auth->getToken($request->input('code'));
        $accessToken = $tokens->access_token;
        $auth->setAccessToken($accessToken);
        $user = new User;
        $correo = $user->data->getUserPrincipalName();
        
        $siguhs = $this->consultaUser($correo,54);
        if($siguhs->estado=='0'){ header("location: sin_permisos");exit; }
        
        $dni = $siguhs->dni;
        $user     = Especialistas::where(['estado'=>1,'ddni'=>$dni])->select('idespecialista')->get()->toArray();
        if(count($user)==0){ header("location: sin_permisos_modulo");exit; }
        
        $idespecialista = $user[0]['idespecialista'];
        $permisos = DB::select("SELECT * FROM app_modulos WHERE estado=1 and tipo='ADJEVA' and CONCAT(',',idespecialista,',') LIKE '%,$idespecialista,%'");
        if(count($permisos)==0){ header("location: sin_permisos_modulo");exit; }
        
        header("location: http://adjudicacion.ugel01.gob.pe/adjudicacion/cpanel/login_adj?code=".MD5($idespecialista.date('Ymd')));
    }
    
    //ENCARGATURA
    public function office365_encargatura(){
        session_start();
        $tenant = "common";
        $cliente_id = "3d83ee0c-58c5-4513-848a-a5655faa85a3";
        $cliente_secret = "Wo78Q~-CyWPQxHdpuwseelCl06u.S.zJRr8J1aAG";
        $callback = "https://aplicacion.ugel01.gob.pe/public/callback_encargatura";
        $scropes = ["User.Read"];
        $microsoft = new Auth($tenant, $cliente_id, $cliente_secret, $callback, $scropes);
        header("location: ".$microsoft->getAuthUrl());
    }
    
    public function callback_encargatura(Request $request){
        date_default_timezone_set('America/Lima');
        session_start();
        $auth = new Auth(Session::get("tenant_id"), Session::get("client_id"),Session::get("client_secret"),Session::get("redirect_uri"),Session::get("scopes"));
        $tokens = $auth->getToken($request->input('code'));
        $accessToken = $tokens->access_token;
        $auth->setAccessToken($accessToken);
        $user = new User;
        $correo = $user->data->getUserPrincipalName();
        $user = $this->consultaUser($correo,71);
        if($user->estado=='1'){
            $dni = $user->dni;
            $data = DB::connection('encargatura')->select("SELECT id_usuario FROM tba_usuario WHERE estado=1 and u_dni LIKE '$dni'");
            if($data){
                $id_usuario = $data[0]->id_usuario;
                header("location: http://adjudicacion.ugel01.gob.pe/encargatura/cpanel/login_adj?code=".MD5($id_usuario.date('Ymd')));
            }else{
                $id_usuario = Encargatura_tba_usuario::insertGetId(['u_dni'=>$user->dni,'user'=>$user->dni,'apellidos'=>$user->apellido_paterno.' '.$user->apellido_materno,'nombre'=>$user->nombre]);
                header("location: http://adjudicacion.ugel01.gob.pe/encargatura/cpanel/login_adj?code=".MD5($id_usuario.date('Ymd')));
            }
        }else{
            header("location: sin_permisos");
        }
    }
    
    
    //SICAB
    public function office365_sicab(){
        session_start();
        $tenant = "common";
        $cliente_id = "94faa67d-dd15-4c16-8f92-83073321baf7";
        $cliente_secret = "rF~8Q~zUBgDIPJOiIgrsVUzQcOk4pwwpkafgvbry";
        $callback = "https://aplicacion.ugel01.gob.pe/public/callback_sicab";
        $scropes = ["User.Read"];
        $microsoft = new Auth($tenant, $cliente_id, $cliente_secret, $callback, $scropes);
        header("location: ".$microsoft->getAuthUrl());
    }
    
    public function callback_sicab(Request $request){
        date_default_timezone_set('America/Lima');
        session_start();
        $auth = new Auth(Session::get("tenant_id"), Session::get("client_id"),Session::get("client_secret"),Session::get("redirect_uri"),Session::get("scopes"));
        $tokens = $auth->getToken($request->input('code'));
        $accessToken = $tokens->access_token;
        $auth->setAccessToken($accessToken);
        $user = new User;
        $correo = $user->data->getUserPrincipalName();
        $user = $this->consultaUser($correo,8);
        if($user->estado=='1'){
            $dni = $user->dni;
            $data = DB::connection('sicab')->select("select b.idUsuarioBoletex from wts_usuarios as u, wts_usuarios_boletex as b where u.dni=b.dni and b.estado=1 and b.dni='$dni'");
            if(count($data)){
                header("location: https://sicab.ugel01.gob.pe/index.php/Principal/LoginMe_userid?code=".MD5($data[0]->idUsuarioBoletex.date('Ymd')));
            }else{
                header("location: sin_permisos");
            }
        }else{
            header("location: sin_permisos");
        }
    }
    
    public function consultaUser($correo,$ide){
        $variable = base64_encode('{"cor":"'.$correo.'","ide":'.$ide.'}');
        $response = Http::get('https://siguhs.ugel01.gob.pe/api/consultaUser?CodPer='.$variable.'');
        $jsonData = $response->json();
        return json_decode(base64_decode($jsonData['data']));
    }
    
    /*
    public function callback_sicab(Request $request){
        date_default_timezone_set('America/Lima');
        session_start();
        $auth = new Auth(Session::get("tenant_id"), Session::get("client_id"),Session::get("client_secret"),Session::get("redirect_uri"),Session::get("scopes"));
        $tokens = $auth->getToken($request->input('code'));
        $accessToken = $tokens->access_token;
        $auth->setAccessToken($accessToken);
        $user = new User;
        //echo "Name: ".$user->data->getDisplayName()."<br>";
        //echo "Email: ".$user->data->getUserPrincipalName()."<br>";
        $correo = $user->data->getUserPrincipalName();
        $user = Especialistas::where(['estado'=>1,'correo1'=>$correo])->select('ddni')->get()->toArray();
        if(count($user)){
            $dni = $user[0]['ddni'];
            $data = DB::connection('sicab')->select("select b.idUsuarioBoletex from wts_usuarios as u, wts_usuarios_boletex as b where u.dni=b.dni and b.estado=1 and b.dni='$dni'");
            if(count($data)){
                header("location: https://sicab.ugel01.gob.pe/index.php/Principal/LoginMe_userid?code=".MD5($data[0]->idUsuarioBoletex.date('Ymd')));
            }else{
                header("location: sin_permisos");
            }
        }else{
            header("location: sin_permisos");
        }
    }
    */
    
}
