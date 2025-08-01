<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use DB;//Conexion a BD 
use App\Models\App_matrix_evaluacion;
use App\Models\App_matrix_evaluacion_alumno;
use App\Models\Contacto;
use App\Models\CorrelativoDocente;
use App\Models\Iiee_a_evaluar_RIE;


class Siseaprende2025 extends Controller
{
    
    
    public function dar_acceso_a_docente(Request $request){
        $dir = $request->session()->get("siic01");
        //dd($dir);
        $npermiso = $request['npermiso'];
        $npermiso = ($npermiso)?$npermiso:0;
        $permisos = $dir['conf_permisos'];
        //dd($permisos);
        $codmodce = $permisos[$npermiso]['esc_codmod'];
        $idnivel  = $permisos[$npermiso]['idnivel'];

        //$plazas['plazas']     = $this->ver_plazas_iiee($codmodce,$idnivel);
        $plazas['resumen']    = $this->ver_resumen_plazas_iiee($codmodce,$idnivel);
        $info['r_materia']    = $this->get_materias();

        $info['plazas_iiee']  = $plazas;
        $info['npermiso']     = $npermiso;
        $info['permisos']     = $permisos;
        return view('Siseaprende2025/dar_acceso_a_docente',$info);
    }
    function ver_plazas_iiee($codmod,$idnivel){
		$result = DB::connection('registrodocente')->select("SELECT N.*,
					MD5(concat(N.nexus_id,N.nombres,'1')) as nexus_id_md5,
					N.nexus_id as id_nexus_docentes,
					MD5(N.nexus_id) as id_nexus_docentes_md5,
					CONCAT(nombres,' ',apellipat,' ',apellimat)  as nombre_completo,
					IFNULL(DATE_FORMAT(fecnac,'%d/%m/%Y'),'')     as nac,
					IFNULL(DATE_FORMAT(fecinicio,'%d/%m/%Y'),'')  as fecinicio,
					IFNULL(DATE_FORMAT(fectermino,'%d/%m/%Y'),'') as fectermino,
					TIMESTAMPDIFF(YEAR,fecnac,CURDATE())          as edad,
					N.codplaza,
					N.numdocum,
					null as flg_req
					FROM nexus N
					WHERE N.descargo IN('DOCENTE','DIRECTIVO','PROFESOR','PROFESOR (FUNCIONES DE DIRECTOR)','DIRECTOR I.E.','SUB-DIRECTOR I.E.') AND N.esc_codmod = '".$codmod."' AND N.idnivel = ".$idnivel);
                $result = (count($result)>0)? $result:false;
                return $result;             
	}

    public function tabla_reporte_nexus(Request $request){
        //$this->init_dir();
        $dir = $request->session()->get("siic01");
        $permisos = $dir['conf_permisos'];
        $npermiso = $request['id'];
        $codmodce = $permisos[$npermiso]['esc_codmod'];
        $idnivel  = $permisos[$npermiso]['idnivel'];

        $info['plazas']    = $this->ver_plazas_iiee($codmodce,$idnivel);

        if($info['plazas']){
        for ($i=0; $i < count($info['plazas']); $i++) {
        $rec = $this->consultar_receptor_dni($info['plazas'][$i]->numdocum);
        
         if($rec){
                $info['plazas'][$i]->registrado = 1;
                $info['plazas'][$i]->celular = $rec->celular;
                $info['plazas'][$i]->correo  = $rec->correo;
            }else{
                $info['plazas'][$i]->registrado = 0;
                $info['plazas'][$i]->celular = '--';
                $info['plazas'][$i]->correo  = '--';
            }   
        }
        }
        $info['resumen']   = $this->ver_resumen_plazas_iiee($codmodce,$idnivel);
        $info['idnivel']   = $idnivel;
        $info['maleta']   = false;

        echo json_encode($info);
    }

    function get_cantidad_alumnos_importados_x_seccion(Request $request){
        $codmodce = $request['codmodce'];
        $result = DB::connection('registrodocente')->select("SELECT 
                                    E.id_evaluacion,
                                     E.cod_grado,
                                     E.seccion,
                                     COUNT(*) as alumnos,
                                     SUM(IF(A.tiposervicio='PRESENCIAL',1,0)) as cantpresencia,
                                     SUM(IF(A.tiposervicio='SEMIPRESENCIAL',1,0)) as cantsemipresencia,
                                     SUM(IF(A.tiposervicio='DISTANCIA',1,0)) as cantdistancia,
                                     SUM(IF(A.tiposervicio='NO CONTACTADO',1,0)) as cantnocontactado,
                                     SUM(IF(A.tiposervicio='PRESENCIAL' or A.tiposervicio='SEMIPRESENCIAL' or A.tiposervicio='DISTANCIA' or A.tiposervicio='NO CONTACTADO',0,1)) as cantnoregistro
                                     FROM app_matrix_evaluacion E
                                     INNER JOIN app_matrix_evaluacion_alumno A ON E.id_evaluacion = A.id_evaluacion
                                     WHERE E.estado = 1 AND A.estado = 1 AND codmodce = '$codmodce'
                                     GROUP BY E.id_evaluacion,E.cod_grado,E.seccion
                                     ORDER BY E.cod_grado,E.seccion");
       $result = (count($result)>0)? $result:0;
       return $result;        
        }
        public function generar_enlace_nuevo_docente(Request $request){
            $dir = $request->session()->get("siic01");
            $nro = $request['idpermiso'];            
            //$permisos = $dir->conf_permisos[$idpermiso];
            $permisos = $dir['conf_permisos'][$nro];
            //$conf_permisos = $dir['conf_permisos'][$nro];
            $data['nombres']   = $request['nombres'];
            $data['apellipat'] = $request['apellipat'];
            $data['apellimat'] = $request['apellimat'];
            $data['numdocum']  = $request['numdocum'];
            $data['numdocum']  = $request['numdocum'];
            $data['codplaza']  = $request['codplaza'];
            $data['situacion'] = $request['situacion'];
            $data['turno']     = $request['turno'];
            $data['desctipotrab'] = 'DOCENTE';
            $data['descsubtipt']  = 'DOCENTE';
            $data['descargo']     = 'PROFESOR';
            $data['codmodce']   = $permisos['esc_codmod'];
            $data['esc_codmod'] = $permisos['esc_codmod'];
            $data['idnivel']  = $permisos['idnivel'];
            $data['jornlab']  = 30;
            $data['descmovim']  = 'ACTIVO';
            $data['desley']  = 'SIN REGIMEN';
            $nexus = $this->datos_nexus($data['codmodce']);
            
            $data['distrito']    = $permisos['d_dist'];
            //$data['desctipoie']  = $permisos->desctipoie;
            //$data['descgestie']  = $permisos->descgestie;
            //$data['desczona']    = $permisos->desczona;
            //$data['clave8']       = $permisos->clave8;
            $data['descniveduc']  = $permisos['d_niv_mod'];
            $data['nombie']       = $permisos['cen_edu'];
            
            /*$data['distrito']    = $nexus->distrito;
            $data['desctipoie']  = $nexus->desctipoie;
            $data['descgestie']  = $nexus->descgestie;
            $data['desczona']    = $nexus->desczona;
            $data['clave8']       = $nexus->clave8;
            $data['descniveduc']  = $nexus->descniveduc;
            $data['nombie']       = $nexus->nombie;*/
            
            $data['jestado']   =  $dir->id_contacto;
            
            $data['reg_prueba'] = 1;
            $nexus_id = $this->insertar_nexus($data);
            echo json_encode($nexus_id);
          }
          
          
        public function nivel_x_grado(Request $request){
            //$request['codlocal'];
            $id_matrix = $request['id_matrix'];
            $id_nivel = $request['id_nivel'];

            $info['r_nivel']   = $this->get_nivel($id_nivel);
            if($info['r_nivel']){
                for ($i=0; $i < count($info['r_nivel']); $i++) {
                    $info['r_nivel'][$i]->grados = $this->get_grados($info['r_nivel'][$i]->nivel, $id_matrix);
                }
            }
    
            echo json_encode($info);
        }

        function get_nivel($id_nivel){
            $where = ($id_nivel)?' AND id_nivel = '.$id_nivel:'';
            $result = DB::connection('registrodocente')->select("SELECT nivel FROM app_matrix_grado WHERE estado = 1 $where GROUP BY nivel");
            $result = (count($result)>0)? $result:false;
            return $result;

        }

        //$result = (count($result)>0)? $result:false
        //$result = (count($result)>0)? $result:false;

        function get_grados($nivel, $id_matrix){
            $result = DB::connection('registrodocente')->select("SELECT*FROM app_matrix_grado WHERE nivel='$nivel' AND estado = 1");
            $result = (count($result)>0)? $result:false;
            return $result;

        }

    function ver_resumen_plazas_iiee($codmod,$idnivel){
		$result = DB::connection('registrodocente')->select("SELECT desctipotrab,COUNT(nexus_id) as cantidad,GROUP_CONCAT(nexus_id) as plazas FROM nexus 
			WHERE codmodce = '$codmod' AND idnivel = $idnivel 
			GROUP BY desctipotrab");
		$result = (count($result)>0)? $result:false;
        return $result;
	}

    function consultar_receptor_dni($dni){
		$result = DB::connection('notificacion')->select("SELECT * FROM `receptor` WHERE `documento` LIKE '$dni' AND estado = 1 AND etapa_de_registro IN (1,2) ORDER BY etapa_de_registro DESC;
");
		$result = (count($result)>0)? $result[0]:false;
        return $result;
	}
	
    function get_materias($evaluacion=false){
        $where  = ($evaluacion)?" AND evaluacion = '$evaluacion'":"";
        $result = DB::connection('registrodocente')->select("SELECT id_matrix,materia FROM app_matrix WHERE estado = 1 and flg = 1".$where);
        $result = (count($result)>0)? $result:false;
        return $result;

    }

    function subir_archivo_siagie(Request $request){
        if($request->hasfile('archivo')){
            $nro = $request['sel_modalidad'];
            $archivo = $request->file('archivo')->store('public/archivo_siagie/'.date('Y'));
            $ruta = Storage::url($archivo);
            $file_rename = str_replace('/storage/archivo_siagie/'.date('Y').'/','',$ruta);
            echo json_encode(array('ruta'=>'./storage/archivo_siagie/'.date('Y'),'file'=>$file_rename,'nro'=>$nro));
        }
    }  

    function procesar_archivo_siagie(Request $request){
        /* 
            $ruta = $this->input->post('ruta');
            $file = $this->input->post('file');
            $nro  = $this->input->post('nro');
            $conf_permisos = $this->session->userdata('siic01')->conf_permisos[$nro];
            $archivo = $ruta.'/'.$file;
            $archivo = fopen($archivo, "r");
        */
        $ruta = $request['ruta'];
        $file  = $request['file'];
        $nro  = $request['nro'];
        $dir = $request->session()->get("siic01");
        $conf_permisos = $dir['conf_permisos'][$nro];
        $archivo = $ruta.'/'.$file;
        $archivo = fopen($archivo, "r");
        $codmodce_CSV = '';
        $registro_subidos = 0;
        $linea = 0;
        $datos = fgets($archivo);
        while ( ($datos = fgets($archivo)) == true ) {
                      $linea++;
                            $datosPC = explode(';', str_replace('"','', $datos ));
                            $datosC  = explode(',', str_replace('"','', $datos ));
                            $datos   = ( count($datosPC)>count($datosC) )?$datosPC:$datosC;
                    if($linea==0){ $codmodce_CSV = $datos[0];  }
                    if( $linea>=10 ){
                          //Recorremos las columnas de esa linea
                        if( count($datos)>1 ){
                                $eva = array();
                                $eva['codmodce']       = str_replace(" ","", $conf_permisos['esc_codmod']);//ID
                                $eva['cod_grado']      = $this->cal_cod_grado( trim($datos[2]) , $conf_permisos['idnivel']);//ID
                                $eva['seccion']        = trim($datos[3]);//ID                               
                                $eva['nombre_docente'] = 'SIAGIE';
                                $eva['dni_doc']        = 'SIAGIE';
                                $eva['nombie']         = $conf_permisos['cen_edu'];
                                $eva['codplaza']       = $conf_permisos['codplaza'];
                                $eva['id_contacto']    = $dir['id_contacto'];
                                $eva['archivo_subido'] = $file;
                                $eva['ruta']           = $ruta;
                            if( $eva['codmodce'] && $eva['cod_grado'] && $eva['seccion'] ){
                                $id_evaluacion = $this->update_app_matrix_evaluacion_siagie($eva);
                                $alum = array();
                                $alum['id_evaluacion'] = $id_evaluacion;
                                $alum['dni']           = trim($datos[5] );
                                if( trim($datos[5]) ){ $alum['usuario'] = trim($datos[5]); }
                                $alum['sexo']          = strtoupper(trim(  substr($datos[11],0,1)   ));// HOMBRE => H
                                //$alum['sexo']          = strtoupper(  trim(substr($datos[11],0,1) )  );
                                $alum['codestudiante'] = trim($datos[7]);
                                $alum['alum_apePat']   = strtoupper(trim($datos[8]));
                                $alum['alum_apeMat']   = strtoupper(trim($datos[9]));
                                $alum['alum_nombre']   = strtoupper(trim($datos[10]));
                                $alum['fecha_nac']     = $this->cal_fecha_nac($datos[12]);
                                $alum['EstadoMatricula'] = strtoupper(trim($datos[14]));
                                $id_alumno = $this->update_app_matrix_evaluacion_alumno_siagie($alum, $eva['codmodce']);
                                if($id_alumno) $registro_subidos++;
                            }
                        }
                    }
                
        }
        if($registro_subidos){
            $this->actualizar_iiee_a_evaluar_RIE(array('import_siagie'=>1),$conf_permisos['esc_codmod']);
            echo $registro_subidos.' alumnos importados satisfactoriamente. '.$codmodce_CSV;
        }else{
            echo "No sé ha podido importar ningún alumno, recargue la página e inténtelo nuevamente.";
        }
    }

    function actualizar_iiee_a_evaluar_RIE($data,$codmodce){
	    //$this->db_b->update('iiee_a_evaluar_RIE',$data,array('codmod'=>$codmodce,'import_siagie'=>1));
        Iiee_a_evaluar_RIE::where($data,array('codmod'=>$codmodce,'import_siagie'=>1));
    }
	
	function generar_secciones($codmodce, $cod_grado){
        $codmodce = str_replace(",","','",$codmodce);
    	$query = $this->db_b->query("SELECT seccion,id_evaluacion FROM app_matrix_evaluacion WHERE codmodce IN('$codmodce') AND cod_grado ='$cod_grado' AND estado = 1 GROUP BY seccion ORDER BY seccion ASC");
    	$result = $query->result();
    	if ($result) {
    		return $result;
    	} else {
    		return false;
    	}
    }

    function update_app_matrix_evaluacion_alumno_siagie($alum, $codmodce=''){
    	$where['id_evaluacion'] = $alum['id_evaluacion'];
    	if($alum['dni']==""){
    		//Validar nor Nombre de Estudiante
    		$where['alum_nombre']   = $alum['alum_nombre'];
    		$where['alum_apePat']   = $alum['alum_apePat'];
    		$where['alum_apeMat']   = $alum['alum_apeMat'];
    	}else{
    		//Validar por DNI
    		$where['dni']           = $alum['dni'];
    	}
    	$where['estado']        = 1;
    	//Validar si el estudiante existe
        $result = App_matrix_evaluacion_alumno::where($where)->get()->toArray();
    	$result = (count($result)>0)? $result[0]:false;
    	if ($result) {
    		//Actualizar datos del estudiante
            App_matrix_evaluacion_alumno::where(['id_detalle_alumno'=>$result['id_detalle_alumno']])->update($result);
    		return $result['id_detalle_alumno'];
    	} else {
    		//Validar alumnos por Nombre estre los que no tiene DNI
    		$where2['id_evaluacion'] = $alum['id_evaluacion'];
    		if($alum['dni']==""){
    		$where2['alum_nombre']   = $alum['alum_nombre'];
    		$where2['alum_apePat']   = $alum['alum_apePat'];
    		$where2['alum_apeMat']   = $alum['alum_apeMat'];
    		$where2['dni']   		 = "";
    		}else{
    		$where2['dni']   		 = $alum['dni'];;
    		}
    		$where2['estado']        = 1;
            $result = App_matrix_evaluacion_alumno::where($where)->get()->toArray();
            $result = (count($result)>0)? $result[0]:false;
    		if ($result) {
    			//Actualizar datos del estudiante
    			App_matrix_evaluacion_alumno::where(['id_detalle_alumno'=>$alum->id_detalle_alumno])->update($result2);
    			return $result2->id_detalle_alumno;
    		} else {
    			//Insertar un nuevo estudiante
    		 	$alum['usuario']     =     $alum['dni'];                                                      //$codmodce
    		 	if($alum['dni']==""){ $alum['usuario'] = $this->generar_usuario_alumno( $alum['id_evaluacion'] ); }
                 return App_matrix_evaluacion_alumno::insertGetId($alum);
    			return $this->db_b->insert_id();
    		}
    	}
    }
    function generar_usuario_alumno($id_evaluacion){
        $result = App_matrix_evaluacion::where(['id_evaluacion'=>$id_evaluacion,'estado'=>1])->get()->toArray(); 
        $result = (count($result)>0)? $result[0]:false;
        
        $codmod  = $result['codmodce'];
 		$formato = '000';
 		$where['anio']   = date('Y');
 		$where['codmod'] = $codmod;
 		$where['estado'] = 1;   
            //$query = $this->db_b->get_where('correlativo',$where);
            $result = CorrelativoDocente::where($where)->get()->toArray();
            $result = (count($result)>0)? $result[0]:false;			
            if ($result) {
				//$this->db_b->update('correlativo',array('numero'=>$result->numero + 1),$where);
                CorrelativoDocente::where(['numero'=>$result['numero']])->update($where);
				return $codmod.sprintf("%'.03d", $result['numero']+1);
			} else {
				 //$this->db_b->insert('correlativo',array('anio'=>date('Y'),'codmod'=>$codmod,'formato'=>$formato,'numero'=>1));
				CorrelativoDocente::insertGetId(array('anio'=>date('Y'),'codmod'=>$codmod,'formato'=>$formato,'numero'=>1));
                return $codmod.sprintf("%'.03d", 1);
			}
        }
    function cal_fecha_nac($fecha){
        $r_fecha = explode("/", $fecha);
        if( count($r_fecha)==3 ){
            return $r_fecha[2]."-".$r_fecha[1]."-".$r_fecha[0];
        }else{
            return NULL;
        }
    }

    function update_app_matrix_evaluacion_siagie($eva){
        $where['codmodce']  = $eva['codmodce'];
        $where['cod_grado'] = $eva['cod_grado'];
        $where['seccion']   = $eva['seccion'];
        $where['estado']    = 1;
        //$query = $this->db_b->get_where('app_matrix_evaluacion',$where);
        //$result = $query->row();
        $result = App_matrix_evaluacion::where($where)->get()->toArray();
        $result = (count($result)>0)? $result[0]:false;
        if ($result) {
            //$this->db_b->update('app_matrix_evaluacion',$eva, array('id_evaluacion'=>$result->id_evaluacion) );
            App_matrix_evaluacion::where(['id_evaluacion'=>$result['id_evaluacion']])->update($eva);
            return $result['id_evaluacion'];
        } else {
            //$this->db_b->insert('app_matrix_evaluacion',$eva);
            return App_matrix_evaluacion::insertGetId($eva);
            //return $this->db_b->insert_id();
        }   
    }

    public function cal_cod_grado($grado, $id_nivel){
        $cod_grado = '';
        $nivel     = '';
        
        $grado = (strpos($grado,'2 AÑOS')>-1)?'Grupo 2 años':$grado;
        $grado = (strpos($grado,'3 AÑOS')>-1)?'Grupo 3 años':$grado;
        $grado = (strpos($grado,'4 AÑOS')>-1)?'Grupo 3 años':$grado;
        $grado = (strpos($grado,'5 AÑOS')>-1)?'Grupo 5 años':$grado;
        
        $grado = (strpos($grado,'2 años')>-1)?'Grupo 2 años':$grado;
        $grado = (strpos($grado,'3 años')>-1)?'Grupo 3 años':$grado;
        $grado = (strpos($grado,'4 años')>-1)?'Grupo 4 años':$grado;
        $grado = (strpos($grado,'5 años')>-1)?'Grupo 5 años':$grado;
        
        $grado = (strpos($grado,'PRIMERO')>-1)?'PRIMERO':$grado;
        $grado = (strpos($grado,'SEGUNDO')>-1)?'SEGUNDO':$grado;
        $grado = (strpos($grado,'TERCERO')>-1)?'TERCERO':$grado;
        $grado = (strpos($grado,'CUARTO')>-1) ?'CUARTO' :$grado;
        $grado = (strpos($grado,'QUINTO')>-1) ?'QUINTO' :$grado;
        $grado = (strpos($grado,'SEXTO')>-1)  ?'SEXTO'  :$grado;
        
        switch ($grado) {
            case 'PRIMERO': $cod_grado = '1'; break;
            case 'SEGUNDO': $cod_grado = '2'; break;
            case 'TERCERO': $cod_grado = '3'; break;
            case 'CUARTO' : $cod_grado = '4'; break;
            case 'QUINTO' : $cod_grado = '5'; break;
            case 'SEXTO'  : $cod_grado = '6'; break;
            
            case 'Grupo 1 años'  : $cod_grado = '1'; break;
            case 'Grupo 2 años'  : $cod_grado = '2'; break;
            case 'Grupo 3 años'  : $cod_grado = '3'; break;
            case 'Grupo 4 años'  : $cod_grado = '4'; break;
            case 'Grupo 5 años'  : $cod_grado = '5'; break;

            default : $cod_grado = false ; break;
        }

        switch ($id_nivel) {
            case '2': $nivel = 'EBE'; break;
            case '3': $nivel = 'SEC'; break;
            case '4': $nivel = 'PRI'; break;
            case '5': $nivel = 'INI'; break;
            case '6': $nivel = 'BAI'; break;
            case '7': $nivel = 'BAA'; break;
            default : $nivel = false; break;
        }
        return ($cod_grado && $nivel)?$cod_grado.$nivel:'';
    }
    
    function eliminar_alumnos(Request $request){
        $codmodce= $request['codmodce'];
        //$query = $this->db_b->get_where('app_matrix_evaluacion',array('codmodce'=>$codmodce,'estado'=>1));
		$result = App_matrix_evaluacion::where(array('codmodce'=>$codmodce,'estado'=>1))->get()->toArray();
        
        $result = (count($result)>0)? $result:false;
		if ($result) {
			foreach ($result as $key) {
				//$this->db_b->update('app_matrix_evaluacion',array('estado'=>0),array('id_evaluacion'=>$key->id_evaluacion ));
                App_matrix_evaluacion::where(['id_evaluacion'=>$key['id_evaluacion']])->update(['estado'=>0]);

				//$this->db_b->update('app_matrix_evaluacion_alumno',array('estado'=>0),array('id_evaluacion'=>$key->id_evaluacion ));
                App_matrix_evaluacion_alumno::where(['id_evaluacion'=>$key['id_evaluacion']])->update(['estado'=>0]);

            }
				//$this->db_b->update('iiee_a_evaluar_RIE',array('import_siagie'=>0),array('codmod'=>$codmodce,'import_siagie'=>1));
                Iiee_a_evaluar_RIE::where(['codmod'=>$codmodce])->update(['import_siagie'=>0]);


			return 'Todos los alumnos del nivel fueron eliminados. Importe nuevamente el archivo CSV del SIAGIE. \n "."Codigo Modular: '.$codmodce;
		} else {
			return 'No se cargo';
		}
	}
	

       // echo $conf_permisos->cen_edu;
        //echo $conf_permisos['cen_edu'];
    }

