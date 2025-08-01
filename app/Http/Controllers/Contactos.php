<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contacto;
use DB;//Conexion a BD 
use Storage;

class Contactos extends Controller
{
    public function permiso_modulo($session,$id_modulo){
        $idespecialista = $session['idespecialista'];
        $id_oficina     = $session['id_oficina'];
        return count(DB::select("SELECT * FROM app_modulos WHERE estado=1 and id_modulo=$id_modulo and tipo='NOTIFICACION' and ( CONCAT(',',idespecialista,',') LIKE '%,$idespecialista,%' OR CONCAT(',',id_oficina,',') LIKE '%,$id_oficina,%' )"));
    }
    
    public function listar_Contactos(){
        $info['areas'] = $this->lista_oficinas();
        $info['session'] = session()->get('siic01_admin');
        $info['ingresar_como_director'] = $this->permiso_modulo($info['session'],251);
        $info['restablecer_pass'] = $this->permiso_modulo($info['session'],252);
        for ($i=0; $i < count($info['areas']); $i++) { 
            $info['areas'][$i]->oficina = $this->lista_oficinas($info['areas'][$i]->SedeOficinaId);
          }
          $info['areas'][] = $this->lista_oficinas(2)[0];
       return view('Contactos/listar_Contactos',$info);
   }
   public function tabla_Contactos(Request $request){
    $where   = '';
    //$gestion = $request['gestion'];
    //AND E.gestion LIKE'$gestion%'
    $where   = ($request['gestion']=='Publica')?" and cant_plazas_nexus>0":" and cant_plazas_nexus=0";
       $sql = DB::select("SELECT 
       E.codmod,
       E.cant_plazas_nexus,
       E.descgestie,
       GROUP_CONCAT(DISTINCT(E.institucion)) as institucion,
       IF(E.idmodalidad=1,'ETP',IF(E.idmodalidad=2,'EBE',IF(E.idmodalidad=3,'EBR',IF(E.idmodalidad=4,'EBA','---')))) as modalidad,
       E.nivel,
       E.distrito,
       REPLACE(GROUP_CONCAT(DISTINCT( CONCAT(C.nombres,' ',C.apellipat,' ',C.apellimat) )),',',' / ')    as director,
       REPLACE(GROUP_CONCAT(DISTINCT( C.celular_pers )),',',' / ')    as celular_pers,
       REPLACE(GROUP_CONCAT(DISTINCT( C.correo_pers  )),',',' / ')    as correo_pers,
       
        REPLACE(GROUP_CONCAT(DISTINCT( C.usuario  )),',',' / ') as usuario,
        MAX(C.id_contacto) as id_contacto,
        C.nombres,
        C.apellipat,
        C.apellimat,
       E.red,
       E.codlocal,
       
       E.gestion,
       E.gestion_dependencia,
       E.correo_inst,
       REPLACE(GROUP_CONCAT(DISTINCT(E.nivel )),',',' / ')  as nivel,
       REPLACE(GROUP_CONCAT(DISTINCT(E.codmod)),',',' / ')  as codmod,
       
       REPLACE(GROUP_CONCAT(DISTINCT( DATE_FORMAT(C.modificado,'%d/%m/%Y')  )),',',' , ')    as l_fecha_creacion,
       
       REPLACE(GROUP_CONCAT(DISTINCT( IFNULL(C.id_contacto,'') )),',','/') as acceso_director,
        MAX(DATE_FORMAT(C.modificado,'%Y-%m-%d')) as t_modificado
       FROM iiee_a_evaluar_RIE E 
       LEFT JOIN (
           select Co.*,Pe.esc_codmod from conf_permisos Pe 
           inner join contacto Co on Pe.id_contacto = Co.id_contacto
           where Pe.estado = 1 and Co.estado = 1 and Co.flg=1
       ) C ON E.codmod = C.esc_codmod 
       
       WHERE E.estado = 1   $where
       GROUP BY E.codmod,E.idmodalidad,E.nivel,E.distrito,C.nombres,C.apellipat,C.apellimat,E.red,E.codlocal,E.gestion,E.gestion_dependencia, E.correo_inst

       ORDER BY E.distrito ASC, E.institucion ASC");

       echo json_encode($sql);
    }
    
    //REPLACE(GROUP_CONCAT(DISTINCT( CONCAT(IFNULL(C.nombres,''),' ',IFNULL(C.apellipat,''),' ',IFNULL(C.apellimat,''),' (de baja el ',DATE_FORMAT(fecha_elimino,'%d/%m/%Y'),')') )),',',' / ')    as director,
    public function tabla_Contactos_elimados(Request $request){
        $where   = '';
        $gestion = $request['gestion'];
           $sql = DB::select("SELECT 
           E.codmod,
           GROUP_CONCAT(DISTINCT(E.institucion)) as institucion,
           IF(E.idmodalidad=1,'ETP',IF(E.idmodalidad=2,'EBE',IF(E.idmodalidad=3,'EBR',IF(E.idmodalidad=4,'EBA','---')))) as modalidad,
           E.nivel,
           E.distrito,
           REPLACE(GROUP_CONCAT(DISTINCT( CONCAT(IFNULL(C.nombres,''),' ',IFNULL(C.apellipat,''),' ',IFNULL(C.apellimat,''),' (de baja el ',DATE_FORMAT(IFNULL(C.fecha_elimino,C.updated_at),'%d/%m/%Y'),')') )),',',' / ')    as director,
           REPLACE(GROUP_CONCAT(DISTINCT( C.celular_pers )),',',' / ')    as celular_pers,
           REPLACE(GROUP_CONCAT(DISTINCT( C.correo_pers  )),',',' / ')    as correo_pers,
           
            REPLACE(GROUP_CONCAT(DISTINCT( C.usuario  )),',',' / ') as usuario,
            MAX(C.id_contacto) as id_contacto,
            C.nombres,
            C.apellipat,
            C.apellimat,
           E.red,
           E.codlocal,
           
           E.gestion,
           E.gestion_dependencia,
           E.correo_inst,
           REPLACE(GROUP_CONCAT(DISTINCT(E.nivel )),',',' / ')  as nivel,
           REPLACE(GROUP_CONCAT(DISTINCT(E.codmod)),',',' / ')  as codmod,
           
           REPLACE(GROUP_CONCAT(DISTINCT( DATE_FORMAT(C.modificado,'%d/%m/%Y')  )),',',' , ')    as l_fecha_creacion,
           
           REPLACE(GROUP_CONCAT(DISTINCT( IFNULL(C.id_contacto,'') )),',','/') as acceso_director
           
           FROM iiee_a_evaluar_RIE E 
           LEFT JOIN (
               select Co.*,Pe.esc_codmod from conf_permisos Pe 
               inner join contacto Co on Pe.id_contacto = Co.id_contacto
               where Pe.estado = 1 and Co.estado = 0 and Co.flg=1
           ) C ON E.codmod = C.esc_codmod 
           
           WHERE E.estado = 1  AND E.gestion LIKE'$gestion%' $where
           GROUP BY E.codmod,E.idmodalidad,E.nivel,E.distrito,C.nombres,C.apellipat,C.apellimat,E.red,E.codlocal,E.gestion,E.gestion_dependencia, E.correo_inst
    
           ORDER BY E.distrito ASC, E.institucion ASC");
    
           echo json_encode($sql);
        }

   public function guardar_Contactos(Request $request){
       $id_contacto = $request['id_contacto'];
       $ins['usuario'] = $request['usuario'];
       $ins['dni'] = $request['usuario'];
       $ins['nombres'] = $request['nombres'];
       $ins['apellipat'] = $request['apellipat'];
       $ins['apellimat'] = $request['apellimat'];
       $ins['celular_pers'] = $request['celular_pers'];
       $ins['correo_pers'] = $request['correo_pers'];

       if($id_contacto){
        Contacto::where('id_contacto',$id_contacto)->update($ins);
           $ins['id_contacto'] = $id_contacto;
       }else{
           $ins['id_contacto'] = Contacto::insertGetId($ins);
       }       
       return $ins;
   }
 
   public function eliminar_Contactos(Request $request){
       date_default_timezone_set('America/Lima');
       $id_contacto=$request['id_contacto'];
       $especialista_elimino=$request['especialista_elimino'];
       Contacto::where('id_contacto',$id_contacto)->update(['fecha_elimino'=>date('Y-m-d H:i:s'),'especialista_elimino'=>$especialista_elimino,'estado'=>0]); 
       return 1;
   }

   public function contrasena_Contactos(Request $request){
    $id_contacto=$request['id_contacto'];
    $contacto_establece=$request['contacto_establece'];
    DB::select("UPDATE contacto SET cambiar_clave = 1, pass = MD5('IIee".date('Y')."'), especialista_restablece = $contacto_establece, fecha_restablece = NOW() WHERE id_contacto = $id_contacto");
    return 1;
}

   public function lista_oficinas($id = 62,$where=''){
    return DB::select("SELECT Nivel,SedeOficinaId,Descripcion,DescripcionCorta,PadreSedeOficinaId from t_SedeOficina where (SedeOficinaId=8 or SedeOficinaId = $id or (Estado = 1 and PadreSedeOficinaId = $id)) $where");
}
}
