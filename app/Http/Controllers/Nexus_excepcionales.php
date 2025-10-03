<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\Models\Nexus_excepcional;
use App\Models\Nexus;
use App\Models\Receptor;
use DB;//Conexion a BD 
use Storage;
use Illuminate\Support\Facades\Session;

class Nexus_excepcionales extends Controller
{
    public function listar_nexus_excepcional(){
        $info['id_contacto'] = session()->get('siic01')['id_contacto'];
        $info['lniveles'] = $session = session()->get('siic01')['conf_permisos'];
        $info['lcargos'] = DB::connection('mysql')->select("SELECT descargo,count(*) as cantidad FROM nexus WHERE estado=1 and idnivel>0 GROUP BY descargo ORDER BY `cantidad` DESC");

        return view('NexusExcepcional/listar_nexus_excepcional',$info);
    }

    public function tabla_nexus_excepcional(Request $request)
    {
        // Acceder al esc_codmod dentro del array de la sesión
        $esc_codmod = session()->get('siic01')['codmodce']; 

        $sql = DB::connection('mysql')->select("
            SELECT *,
                DATE_FORMAT(fecinicio,'%Y-%m-%d') as finicio,
                DATE_FORMAT(fectermino,'%Y-%m-%d') as ftermino
            FROM nexus_excepcional 
            WHERE estado = 1 
            AND codmodce = ?
        ", [$esc_codmod]);

        return response()->json($sql);
    }

    public function guardar_nexus_excepcional(Request $request){
        $nexus_id = $request['nexus_id'];
        $codmodce = $request['codmodce'];
        $ins['codplaza'] = $request['codplaza'];
        //$ins['desctipotrab'] = $request['desctipotrab'];
        //$ins['descsubtipt'] = $request['descsubtipt'];
        $ins['descargo'] = $request['descargo'];
        $ins['situacion'] = $request['situacion'];
        $ins['numdocum'] = $request['numdocum'];
        $ins['apellipat'] = $request['apellipat'];
        $ins['apellimat'] = $request['apellimat'];
        $ins['nombres'] = $request['nombres'];
        $ins['codcatrem'] = $request['codcatrem'];
        $ins['jornlab'] = $request['jornlab'];
        $ins['fecinicio'] = $request['fecinicio'];
        $ins['fectermino'] = $request['fectermino'];
        $ins['jestado'] = $request['id_contacto'];
        if($nexus_id){
            Nexus_excepcional::where('nexus_id',$nexus_id)->update($ins);
            $ins['nexus_id'] = $nexus_id;
            return 1;
        }else{
            
            $nexus = Nexus::where('codmodce',$codmodce)->select('*')->get()->toArray()[0];
            $ins['distrito']    = $nexus['distrito'];
            $ins['desctipoie']  = $nexus['desctipoie'];
            $ins['descgestie']  = $nexus['descgestie'];
            $ins['desczona']    = $nexus['desczona'];
            $ins['codlocal']    = $nexus['codlocal'];
            $ins['codmodce']    = $nexus['codmodce'];
            $ins['clave8']      = $nexus['clave8'];
            $ins['descniveduc'] = $nexus['descniveduc'];
            $ins['nombie']      = $nexus['nombie'];
            $ins['idnivel']     = $nexus['idnivel'];
            $ins['idmodalidad'] = $nexus['idmodalidad'];
            return Nexus_excepcional::insertGetId($ins);
        }
    }

    public function eliminar_nexus_excepcional(Request $request){
        $nexus_id=$request['nexus_id'];
        Nexus_excepcional::where('nexus_id',$nexus_id)->update(['estado'=>0]);
        return 1;
    }
    public function buscar_nexus_excepcional(Request $request){
        $dni = $request['dni'];
        $data = Nexus::where('numdocum',$dni)->select('nombres','apellipat','apellimat')->get()->toArray();
        if (count($data)) {
            return $data[0];
        }else{
            $rec = Receptor::where('documento',$dni)->select('nombres','apellido_paterno','apellido_materno')->get()->toArray();
            if(count($rec)){
                return ['nombres'=>$rec[0]['nombres'],'apellipat'=>$rec[0]['apellido_paterno'],'apellimat'=>$rec[0]['apellido_materno']];
            }else{
                return 0;
            }
            
        }
    }
}
