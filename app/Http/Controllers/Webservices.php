<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\App_modulos;
use App\Models\Especialistas;
use App\Models\Contacto;
use App\Models\Conf_permisos;
use App\Models\Restablecer_correo_director;
use DB;

class Webservices extends Controller
{
    
    function url(Request $request){
        return redirect( $request['url']);
        //return view('Webservices/url');
        //http://server-biometrico.ugel01.gob.pe:8020/api_reloj/public/vermarcaciones
        //echo '<body onload="window.open('','_blank');"></body>';
        //echo "<script>window.open('".$request['url']."', '_blank')</script>";
        //echo $request['url'];
        //return redirect('/home/dashboard');
    }
    
    function nexus_dir(){
        date_default_timezone_set('America/Lima');
        //NUEVOS DIRECTORES
        $nexus = DB::select("SELECT codlocal,idmodalidad,numdocum,nombres,apellipat,apellimat,nombie,correo,celular,codmodce,codplaza,descsubtipt,descargo FROM nexus WHERE estado=1 and descargo='DIRECTOR I.E.' and tiporegistro='ORGANICA'");// and numdocum<>'VACANTE'  and codlocal='343847'
        $nro = 1;
        foreach ($nexus as $key) {
            $dir = $this->director_habilitado($key->codlocal,$key->idmodalidad);
            
            if($dir){
            if($key->numdocum!=$dir->dni){
                echo '<br><br><b>'.($nro++).'. Cambio de Director</b>';
                echo '<br><b>IE: '.$key->nombie.' ('.$key->codlocal.')</b>';
                //echo ' '.$dir->id_contacto;
                echo '<br>Antiguo:<br>';
                echo $dir->nombres.' '.$dir->apellipat.' '.$dir->apellimat.' '.$dir->dni;
                //print_r($dir);
                echo '<br>Nuevo:<br>';
                echo $key->nombres.' '.$key->apellipat.' '.$key->apellimat.' '.$key->numdocum;
                //print_r($key);
                //Eliminar Director
                    Contacto::where('id_contacto',$dir->id_contacto)->update(['fecha_elimino'=>date('Y-m-d H:i:s'),'estado'=>6]);
                //Insertar Nuevo Director
                    if($key->numdocum!='VACANTE'){$this->insertar_nuevo_director($key);}
            }
            }else{
            echo '<br><br><b>'.($nro++).'. Nuevo Director</b>';
            echo '<br><b>IE: '.$key->nombie.' ('.$key->codlocal.')</b>'.'<br>';
            echo $key->nombres.' '.$key->apellipat.' '.$key->apellimat.' '.$key->numdocum;
                //Insertar Nuevo Director
                    if($key->numdocum!='VACANTE'){$this->insertar_nuevo_director($key);}
            }
        }
        

        
    }
    
    function director_habilitado($codlocal,$idmodalidad){
        $dir = DB::select("SELECT C.dni,C.nombres,C.apellipat,C.apellimat,C.id_contacto FROM contacto C 
                INNER JOIN conf_permisos P ON C.id_contacto=P.id_contacto
                INNER JOIN 	iiee_a_evaluar_RIE R ON P.esc_codmod=R.codmod
                WHERE C.estado=1 and C.flg=1 and P.estado=1 and R.estado=1 and R.codlocal= '$codlocal' AND R.idmodalidad='$idmodalidad'
                GROUP BY C.dni,C.nombres,C.apellipat,C.apellimat,C.id_contacto
                ORDER BY C.id_contacto ASC");
        return (count($dir))?$dir[0]:false;
    }
    
    function insertar_nuevo_director($key){
        //Insertar contacto
        $ins = array(
            'usuario'=>$key->numdocum,
            'tipodoc'=>'DNI',
            'dni'=>$key->numdocum,
            'apellipat'=>$key->apellipat,
            'apellimat'=>$key->apellimat,
            'nombres'=>$key->nombres,
            'pass'=>MD5($key->numdocum),
            'celular_pers'=>$key->celular,
            'correo_pers'=>$key->correo,
            'codmodce'=>$key->codmodce,
            'codplaza'=>$key->codplaza,
            'fecha_creacion'=>date('Y-m-d'),
            'trabajo'=>$key->descsubtipt,
            'cargo'=>$key->descargo,
            'registrado'=>1
            );
        //Insert contacto
        $id_contacto = Contacto::insertGetId($ins);
        //Insert restablecer_correo_director
        Restablecer_correo_director::insert(['id_contacto'=>$id_contacto,'codmod'=>$key->codmodce,'resRes'=>'','nuevo_dir'=>1]);
        
        //Insertar conf_permisos
            $rie = DB::select("SELECT `codmod`,`idnivel`,`codlocal` FROM `iiee_a_evaluar_RIE` WHERE `codlocal` = '".$key->codlocal."' and idmodalidad='".$key->idmodalidad."'");
            foreach ($rie as $per) {
            $permiso = array();
            $permiso = array(
                'id_contacto'=>$id_contacto,
                'codplaza'=>$key->codplaza,
                'esc_codmod'=>$per->codmod,
                'creado'=>date('Y-m-d'),
                'idnivel'=>$per->idnivel,
                'codlocal'=>$per->codlocal
                );
                Conf_permisos::insert($permiso);
            }
    }
    
    function consulta_app_modulos(Request $request){
        
        $data = json_decode(file_get_contents('php://input'),true);
        $id_modulo =$data['id_modulo'];
        $info = array();
        $id_modulo = $request['id_modulo'];
        $modulo= App_modulos::where(['estado'=>1,'id_modulo'=>$id_modulo])->get()->toArray();
        if(count($modulo)){
            $modulo = $modulo[0];
            $esp = Especialistas::whereIn('idespecialista',explode(',',$modulo['idespecialista']))->get()->toArray();
            //print_r($sql);
            $info['modulo'] = $modulo;
            $info['esp']    = $esp;
        }
        echo json_encode($info);
        //dd($info);
    }
    
    
     function consulta_especialistas(Request $request){
        //echo $this->getRealIP();
        //exit();
        echo json_encode(Especialistas::where(['estado'=>1,'ddni'=>'71336150'])->get()->toArray());
        
        //dd($esp);
     }
     
     function getRealIP(){

        if (isset($_SERVER["HTTP_CLIENT_IP"])){

            return $_SERVER["HTTP_CLIENT_IP"];

        }elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){

            return $_SERVER["HTTP_X_FORWARDED_FOR"];

        }elseif (isset($_SERVER["HTTP_X_FORWARDED"])){

            return $_SERVER["HTTP_X_FORWARDED"];

        }elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])){

            return $_SERVER["HTTP_FORWARDED_FOR"];

        }elseif (isset($_SERVER["HTTP_FORWARDED"])){

            return $_SERVER["HTTP_FORWARDED"];

        }else{

            return $_SERVER["REMOTE_ADDR"];

        }
    }       
    
    
    
    
    
    
}