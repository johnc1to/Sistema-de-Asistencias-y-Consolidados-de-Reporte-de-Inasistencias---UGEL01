<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
require "../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\Preguntas;
use App\Models\Fichas;
use App\Models\Form_epps;
use DB;

class Excel2 extends Controller
{
    private $letras = array(
		    'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
		    'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
		    'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
		    'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ',
		    'DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ',
		    'EA','EB','EC','ED','EE','EF','EG','EH','EI','EJ','EK','EL','EM','EN','EO','EP','EQ','ER','ES','ET','EU','EV','EW','EX','EY','EZ',
		    'FA','FB','FC','FD','FE','FF','FG','FH','FI','FJ','FK','FL','FM','FN','FO','FP','FQ','FR','FS','FT','FU','FV','FW','FX','FY','FZ',
		    'GA','GB','GC','GD','GE','GF','GG','GH','GI','GJ','GK','GL','GM','GN','GO','GP','GQ','GR','GS','GT','GU','GV','GW','GX','GY','GZ',
		    'HA','HB','HC','HD','HE','HF','HG','HH','HI','HJ','HK','HL','HM','HN','HO','HP','HQ','HR','HS','HT','HU','HV','HW','HX','HY','HZ',
		    'IA','IB','IC','ID','IE','IF','IG','IH','II','IJ','IK','IL','IM','IN','IO','IP','IQ','IR','IS','IT','IU','IV','IW','IX','IY','IZ',
		    'JA','JB','JC','JD','JE','JF','JG','JH','JI','JJ','JK','JL','JM','JN','JO','JP','JQ','JR','JS','JT','JU','JV','JW','JX','JY','JZ',
		    'KA','KB','KC','KD','KE','KF','KG','KH','KI','KJ','KK','KL','KM','KN','KO','KP','KQ','KR','KS','KT','KU','KV','KW','KX','KY','KZ',
		    'LA','LB','LC','LD','LE','LF','LG','LH','LI','LJ','LK','LL','LM','LN','LO','LP','LQ','LR','LS','LT','LU','LV','LW','LX','LY','LZ',
		    'MA','MB','MC','MD','ME','MF','MG','MH','MI','MJ','MK','ML','MM','MN','MO','MP','MQ','MR','MS','MT','MU','MV','MW','MX','MY','MZ',
		    'NA','NB','NC','ND','NE','NF','NG','NH','NI','NJ','NK','NL','NM','NN','NO','NP','NQ','NR','NS','NT','NU','NV','NW','NX','NY','NZ',
		    'OA','OB','OC','OD','OE','OF','OG','OH','OI','OJ','OK','OL','OM','ON','OO','OP','OQ','OR','OS','OT','OU','OV','OW','OX','OY','OZ',
		    'PA','PB','PC','PD','PE','PF','PG','PH','PI','PJ','PK','PL','PM','PN','PO','PP','PQ','PR','PS','PT','PU','PV','PW','PX','PY','PZ',
		    'QA','QB','QC','QD','QE','QF','QG','QH','QI','QJ','QK','QL','QM','QN','QO','QP','QQ','QR','QS','QT','QU','QV','QW','QX','QY','QZ',
		    );
    
    public function cas_descuento(Request $request){
        $letra = $this->letras;
        $mes  = $request['mes'];
        $anio = $request['anio'];
        $diasdelmes = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
        //echo $diasdelmes;
        //exit();
        date_default_timezone_set('America/Lima');
        $fileName="IE_que_faltan_evaluacion.xlsx";   
        $spread = new Spreadsheet();
        $sheet = $spread->getActiveSheet();
        $sheet->setTitle("DIRECTORES");
        //$hoja->mergeCells('A1:C1')->setCellValue('A1','ENCABEZADO');
        $sheet->mergeCells('B2:C2')->setCellValue("B2", "PERU");
        $sheet->mergeCells('D2:L2')->setCellValue("D2", "MINISTERIO DE EDUCACIÓN");
        $sheet->mergeCells('M2:X2')->setCellValue("M2", "DIRECCION REGIONAL DE EDUCACION DE LIMA METROPOLITANA");
        $sheet->mergeCells('Y2:'.$letra[$diasdelmes+4].'2')->setCellValue("Y2", "UNIDAD DE GESTION EDUCATIVA LOCAL Nº 01");
        $sheet->mergeCells('A4:'.$letra[$diasdelmes+4].'4')->setCellValue("A4", "REPORTE DE CONSOLIDADO  DE INASISTENCIA Y TARDANZAS PERSONAL  - CAS - ASESORAMIENTO EN ASUNTOS LEGALES");
        $sheet->mergeCells('A5:'.$letra[$diasdelmes+4].'5')->setCellValue("A5", "Y JURIDICOS DE COMPETENCIA DEL MES DE MAYO DEL 2024");
        
        $sheet->getRowDimension('2')->setRowHeight('36');
        $sheet->getRowDimension('8')->setRowHeight('29.25');
        
        $sheet->mergeCells('A8:A9')->setCellValue("A8", "N°");
        $sheet->mergeCells('B8:B9')->setCellValue("B8", "APELLIDOS Y NOMBRES");
        $sheet->getStyle('B8')->getAlignment()->setWrapText(true);
        $sheet->getStyle('D2')->getAlignment()->setWrapText(true);
        $sheet->getStyle('M2')->getAlignment()->setWrapText(true);
        //$nro = 1;
        for ($i=0; $i < $diasdelmes; $i++) {
             $sheet->setCellValue($letra[$i+2]."9",$i+1);
             $sheet->getColumnDimension($letra[$i+2])->setWidth('3.29');
        }
        $sheet->mergeCells('C8:'.$letra[$i+1].'8')->setCellValue("C8", "CONSOLIDADO DE ASISTENCIA MAYO 2024");//diasdelmes
        $sheet->getStyle('C9:'.$letra[$i+1].'9')->getFont()->setSize(8);
        //AG8
        $sheet->mergeCells($letra[$i+2].'8:'.$letra[$i+2].'9')->setCellValue($letra[$i+2]."8", "Dias No Laborados");
        $sheet->mergeCells($letra[$i+3].'8:'.$letra[$i+3].'9')->setCellValue($letra[$i+3]."8", "Horas de Tardanzas");
        $sheet->mergeCells($letra[$i+4].'8:'.$letra[$i+4].'9')->setCellValue($letra[$i+4]."8", "Minutos de Tardanzas");
        $sheet->getStyle($letra[$i+2].'8')->getAlignment()->setWrapText(true);
        $sheet->getStyle($letra[$i+3].'8')->getAlignment()->setWrapText(true);
        $sheet->getStyle($letra[$i+4].'8')->getAlignment()->setWrapText(true);
        $sheet->getStyle($letra[$i+2].'8')->getFont()->setName("Calibri");
        $sheet->getStyle($letra[$i+3].'8')->getFont()->setName("Calibri");
        $sheet->getStyle($letra[$i+4].'8')->getFont()->setName("Calibri");
        $sheet->getColumnDimension($letra[$i+2])->setWidth('8.43');
        $sheet->getColumnDimension($letra[$i+3])->setWidth('8.29');
        $sheet->getColumnDimension($letra[$i+4])->setWidth('9.43');
        //$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
        
        //$sheet->getRowDimension('1')->setRowHeight(4.86);36
        //$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn('C')->setWidth('10');
        $sheet->getColumnDimension('A')->setWidth('4.86');
        $sheet->getColumnDimension('B')->setWidth('17.71');
        $sheet->getColumnDimension('C')->setWidth('3.29');
        
        //Tamano de letra
        $sheet->getStyle("A2:AJ5")->getFont()->setSize(14);
        $sheet->getStyle("A4:AJ2")->getFont()->setName("Arial");
        $sheet->getStyle("A4:AG9")->getFont()->setName("Tahoma");
        //$sheet->getStyle("A2:AG9")->getFont()->setName("Calibri");
        //Centrar
        //$sheet->getStyle('C8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //$sheet->getStyle("C8")->getAlignment()->setHorizontal('\PHPExcel_Style_Alignment::VERTICAL_CENTER');
        //$sheet->getStyle("C8")->getAlignment()->setVertical('\PHPExcel_Style_Alignment::VERTICAL_CENTER');

        //$sheet->setCellValue("A2", "PERU");
        
        /*
        $letra = $this->letras;
        $cabeza = array('RED', 'CODLOCAL', 'CODMOD', 'INSTITUCION', 'NIVEL', 'GRADO', 'SECCION', 'AREA', 'TOTAL ALUMNOS', 'REGISTROS', 'DELTA', 'COMPLETOS');
        for ($i=0; $i < count($cabeza); $i++) {
            $sheet->setCellValue($letra[$i]."1", $cabeza[$i]);
            $sheet->getColumnDimension($letra[$i])->setAutoSize(true);
        }
        */
        /*
        $data = $this->query_excel_ie_que_faltan_evaluacion_primaria();
        for ($i=0; $i < count($data); $i++) {
        $nro = $i+2;
        $key = $data[$i];
        $j=0;
        $sheet->setCellValue($letra[$j++].$nro, $key->red);
        $sheet->setCellValue($letra[$j++].$nro, $key->codlocal);
        $sheet->setCellValue($letra[$j++].$nro, $key->codmod);
        $sheet->setCellValue($letra[$j++].$nro, $key->nombie);
        $sheet->setCellValue($letra[$j++].$nro, $key->nivel);
        $sheet->setCellValue($letra[$j++].$nro, $key->cod_grado);
        $sheet->setCellValue($letra[$j++].$nro, $key->seccion);
        $sheet->setCellValue($letra[$j++].$nro, $key->materia);
        $sheet->setCellValue($letra[$j++].$nro, $key->total_alumnos);
        $sheet->setCellValue($letra[$j++].$nro, $key->registrado_datos);
        $sheet->setCellValue($letra[$j++].$nro, $key->delta);
        $sheet->setCellValue($letra[$j++].$nro, $key->completo);
        }
        */
        //$sheet->freezePaneByColumnAndRow(1,2);//Inmobilizar fila superior
        $sheet->getRowDimension('1')->setRowHeight(39.8);
        $sheet->getStyle('A4:AJ9')->getFont()->setBold(true);//Negrita en fila superior
        //$sheet->getStyle('A1:N'.($i+1))->getFont()->setName('Courier New');//Negrita en fila superior
        # Crear un "escritor"
        $writer = new Xlsx($spread);
        # Le pasamos la ruta de guardado
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
    }
    
    public function query_cas_descuento(){
        
    }
    
    public function excel_ie_que_faltan_evaluacion_primaria(){
        date_default_timezone_set('America/Lima');
        $fileName="IE_que_faltan_evaluacion.xlsx";   
        $spread = new Spreadsheet();
        $sheet = $spread->getActiveSheet();
        $sheet->setTitle("DIRECTORES");
        $letra = $this->letras;
        $cabeza = array('RED', 'CODLOCAL', 'CODMOD', 'INSTITUCION', 'NIVEL', 'GRADO', 'SECCION', 'AREA', 'TOTAL ALUMNOS', 'REGISTROS', 'DELTA', 'COMPLETOS');
        for ($i=0; $i < count($cabeza); $i++) {
            $sheet->setCellValue($letra[$i]."1", $cabeza[$i]);
            $sheet->getColumnDimension($letra[$i])->setAutoSize(true);
        }
        
        $data = $this->query_excel_ie_que_faltan_evaluacion_primaria();
        for ($i=0; $i < count($data); $i++) {
        $nro = $i+2;
        $key = $data[$i];
        $j=0;
        $sheet->setCellValue($letra[$j++].$nro, $key->red);
        $sheet->setCellValue($letra[$j++].$nro, $key->codlocal);
        $sheet->setCellValue($letra[$j++].$nro, $key->codmod);
        $sheet->setCellValue($letra[$j++].$nro, $key->nombie);
        $sheet->setCellValue($letra[$j++].$nro, $key->nivel);
        $sheet->setCellValue($letra[$j++].$nro, $key->cod_grado);
        $sheet->setCellValue($letra[$j++].$nro, $key->seccion);
        $sheet->setCellValue($letra[$j++].$nro, $key->materia);
        $sheet->setCellValue($letra[$j++].$nro, $key->total_alumnos);
        $sheet->setCellValue($letra[$j++].$nro, $key->registrado_datos);
        $sheet->setCellValue($letra[$j++].$nro, $key->delta);
        $sheet->setCellValue($letra[$j++].$nro, $key->completo);
        }
        
        $sheet->freezePaneByColumnAndRow(1,2);//Inmobilizar fila superior
        $sheet->getRowDimension('1')->setRowHeight(39.8);
        $sheet->getStyle('A1:'.$letra[count($cabeza)].'1')->getFont()->setBold(true);//Negrita en fila superior
        //$sheet->getStyle('A1:N'.($i+1))->getFont()->setName('Courier New');//Negrita en fila superior
        # Crear un "escritor"
        $writer = new Xlsx($spread);
        # Le pasamos la ruta de guardado
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
    }
    
    function query_excel_ie_que_faltan_evaluacion_primaria(){
        $data = DB::connection('registrodocente')->select("SELECT 
                I.red,
                I.codlocal,
                I.codmod,
                E.nombie,
                I.nivel,
                E.cod_grado,
                E.seccion,
                M.materia,
                E.cantidad as total_alumnos,
                Count(Distinct R.id_detalle_alumno) as registrado_datos,
                E.cantidad - Count(Distinct R.id_detalle_alumno) as delta,
                IF(E.cantidad=Count(Distinct R.id_detalle_alumno),'SI','NO') as completo
                FROM app_matrix  M 
                INNER JOIN app_matrix_detalle D ON M.id_matrix=D.id_matrix
                INNER JOIN app_matrix_evaluacion_respuesta_1PRI R ON D.id_matrix_detalle=R.id_matrix_detalle
                INNER JOIN app_matrix_evaluacion_alumno A ON R.id_detalle_alumno=A.id_detalle_alumno
                INNER JOIN app_matrix_evaluacion E ON A.id_evaluacion=E.id_evaluacion
                INNER JOIN iiee_a_evaluar_RIE I ON E.codmodce=I.codmod
                WHERE M.estado=1 and D.estado=1 and A.estado=1 and E.estado=1
                GROUP BY I.red,I.codlocal,
                I.codmod,
                E.nombie,
                I.nivel,
                E.cod_grado,
                E.seccion,
                M.materia,
                E.cantidad
                UNION
                SELECT 
                I.red,
                I.codlocal,
                I.codmod,
                E.nombie,
                I.nivel,
                E.cod_grado,
                E.seccion,
                M.materia,
                E.cantidad as total_alumnos,
                Count(Distinct R.id_detalle_alumno) as registrado_datos,
                E.cantidad - Count(Distinct R.id_detalle_alumno) as delta,
                IF(E.cantidad=Count(Distinct R.id_detalle_alumno),'SI','NO') as completo
                FROM app_matrix  M 
                INNER JOIN app_matrix_detalle D ON M.id_matrix=D.id_matrix
                INNER JOIN app_matrix_evaluacion_respuesta_2PRI R ON D.id_matrix_detalle=R.id_matrix_detalle
                INNER JOIN app_matrix_evaluacion_alumno A ON R.id_detalle_alumno=A.id_detalle_alumno
                INNER JOIN app_matrix_evaluacion E ON A.id_evaluacion=E.id_evaluacion
                INNER JOIN iiee_a_evaluar_RIE I ON E.codmodce=I.codmod
                WHERE M.estado=1 and D.estado=1 and A.estado=1 and E.estado=1
                GROUP BY I.red,I.codlocal,
                I.codmod,
                E.nombie,
                I.nivel,
                E.cod_grado,
                E.seccion,
                M.materia,
                E.cantidad
                UNION
                SELECT 
                I.red,
                I.codlocal,
                I.codmod,
                E.nombie,
                I.nivel,
                E.cod_grado,
                E.seccion,
                M.materia,
                E.cantidad as total_alumnos,
                Count(Distinct R.id_detalle_alumno) as registrado_datos,
                E.cantidad - Count(Distinct R.id_detalle_alumno) as delta,
                IF(E.cantidad=Count(Distinct R.id_detalle_alumno),'SI','NO') as completo
                FROM app_matrix  M 
                INNER JOIN app_matrix_detalle D ON M.id_matrix=D.id_matrix
                INNER JOIN app_matrix_evaluacion_respuesta_3PRI R ON D.id_matrix_detalle=R.id_matrix_detalle
                INNER JOIN app_matrix_evaluacion_alumno A ON R.id_detalle_alumno=A.id_detalle_alumno
                INNER JOIN app_matrix_evaluacion E ON A.id_evaluacion=E.id_evaluacion
                INNER JOIN iiee_a_evaluar_RIE I ON E.codmodce=I.codmod
                WHERE M.estado=1 and D.estado=1 and A.estado=1 and E.estado=1
                GROUP BY I.red,I.codlocal,
                I.codmod,
                E.nombie,
                I.nivel,
                E.cod_grado,
                E.seccion,
                M.materia,
                E.cantidad
                ORDER BY nombie ASC");
        return $data;
    }
    
    
    public function excel_ie_que_faltan_evaluacion_primaria_4_5_6(){
        date_default_timezone_set('America/Lima');
        $fileName="IE_que_faltan_evaluacion.xlsx";   
        $spread = new Spreadsheet();
        $sheet = $spread->getActiveSheet();
        $sheet->setTitle("DIRECTORES");
        $letra = $this->letras;
        $cabeza = array('RED', 'CODLOCAL', 'CODMOD', 'INSTITUCION', 'NIVEL', 'GRADO', 'SECCION', 'AREA', 'TOTAL ALUMNOS', 'REGISTROS', 'DELTA', 'COMPLETOS');
        for ($i=0; $i < count($cabeza); $i++) {
            $sheet->setCellValue($letra[$i]."1", $cabeza[$i]);
            $sheet->getColumnDimension($letra[$i])->setAutoSize(true);
        }
        
        $data = $this->query_excel_ie_que_faltan_evaluacion_primaria_4_5_6();
        for ($i=0; $i < count($data); $i++) {
        $nro = $i+2;
        $key = $data[$i];
        $j=0;
        $sheet->setCellValue($letra[$j++].$nro, $key->red);
        $sheet->setCellValue($letra[$j++].$nro, $key->codlocal);
        $sheet->setCellValue($letra[$j++].$nro, $key->codmod);
        $sheet->setCellValue($letra[$j++].$nro, $key->nombie);
        $sheet->setCellValue($letra[$j++].$nro, $key->nivel);
        $sheet->setCellValue($letra[$j++].$nro, $key->cod_grado);
        $sheet->setCellValue($letra[$j++].$nro, $key->seccion);
        $sheet->setCellValue($letra[$j++].$nro, $key->materia);
        $sheet->setCellValue($letra[$j++].$nro, $key->total_alumnos);
        $sheet->setCellValue($letra[$j++].$nro, $key->registrado_datos);
        $sheet->setCellValue($letra[$j++].$nro, $key->delta);
        $sheet->setCellValue($letra[$j++].$nro, $key->completo);
        }
        
        $sheet->freezePaneByColumnAndRow(1,2);//Inmobilizar fila superior
        $sheet->getRowDimension('1')->setRowHeight(39.8);
        $sheet->getStyle('A1:'.$letra[count($cabeza)].'1')->getFont()->setBold(true);//Negrita en fila superior
        //$sheet->getStyle('A1:N'.($i+1))->getFont()->setName('Courier New');//Negrita en fila superior
        # Crear un "escritor"
        $writer = new Xlsx($spread);
        # Le pasamos la ruta de guardado
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
    }
    
    function query_excel_ie_que_faltan_evaluacion_primaria_4_5_6(){
        $data = DB::connection('registrodocente')->select("SELECT 
                I.red,
                I.codlocal,
                I.codmod,
                E.nombie,
                I.nivel,
                E.cod_grado,
                E.seccion,
                M.materia,
                E.cantidad as total_alumnos,
                Count(Distinct R.id_detalle_alumno) as registrado_datos,
                E.cantidad - Count(Distinct R.id_detalle_alumno) as delta,
                IF(E.cantidad=Count(Distinct R.id_detalle_alumno),'SI','NO') as completo
                FROM app_matrix  M 
                INNER JOIN app_matrix_detalle D ON M.id_matrix=D.id_matrix
                INNER JOIN app_matrix_evaluacion_respuesta_4PRI R ON D.id_matrix_detalle=R.id_matrix_detalle
                INNER JOIN app_matrix_evaluacion_alumno A ON R.id_detalle_alumno=A.id_detalle_alumno
                INNER JOIN app_matrix_evaluacion E ON A.id_evaluacion=E.id_evaluacion
                INNER JOIN iiee_a_evaluar_RIE I ON E.codmodce=I.codmod
                WHERE M.estado=1 and D.estado=1 and A.estado=1 and E.estado=1
                GROUP BY I.red,I.codlocal,
                I.codmod,
                E.nombie,
                I.nivel,
                E.cod_grado,
                E.seccion,
                M.materia,
                E.cantidad
                UNION
                SELECT 
                I.red,
                I.codlocal,
                I.codmod,
                E.nombie,
                I.nivel,
                E.cod_grado,
                E.seccion,
                M.materia,
                E.cantidad as total_alumnos,
                Count(Distinct R.id_detalle_alumno) as registrado_datos,
                E.cantidad - Count(Distinct R.id_detalle_alumno) as delta,
                IF(E.cantidad=Count(Distinct R.id_detalle_alumno),'SI','NO') as completo
                FROM app_matrix  M 
                INNER JOIN app_matrix_detalle D ON M.id_matrix=D.id_matrix
                INNER JOIN app_matrix_evaluacion_respuesta_5PRI R ON D.id_matrix_detalle=R.id_matrix_detalle
                INNER JOIN app_matrix_evaluacion_alumno A ON R.id_detalle_alumno=A.id_detalle_alumno
                INNER JOIN app_matrix_evaluacion E ON A.id_evaluacion=E.id_evaluacion
                INNER JOIN iiee_a_evaluar_RIE I ON E.codmodce=I.codmod
                WHERE M.estado=1 and D.estado=1 and A.estado=1 and E.estado=1
                GROUP BY I.red,I.codlocal,
                I.codmod,
                E.nombie,
                I.nivel,
                E.cod_grado,
                E.seccion,
                M.materia,
                E.cantidad
                UNION
                SELECT 
                I.red,
                I.codlocal,
                I.codmod,
                E.nombie,
                I.nivel,
                E.cod_grado,
                E.seccion,
                M.materia,
                E.cantidad as total_alumnos,
                Count(Distinct R.id_detalle_alumno) as registrado_datos,
                E.cantidad - Count(Distinct R.id_detalle_alumno) as delta,
                IF(E.cantidad=Count(Distinct R.id_detalle_alumno),'SI','NO') as completo
                FROM app_matrix  M 
                INNER JOIN app_matrix_detalle D ON M.id_matrix=D.id_matrix
                INNER JOIN app_matrix_evaluacion_respuesta_6PRI R ON D.id_matrix_detalle=R.id_matrix_detalle
                INNER JOIN app_matrix_evaluacion_alumno A ON R.id_detalle_alumno=A.id_detalle_alumno
                INNER JOIN app_matrix_evaluacion E ON A.id_evaluacion=E.id_evaluacion
                INNER JOIN iiee_a_evaluar_RIE I ON E.codmodce=I.codmod
                WHERE M.estado=1 and D.estado=1 and A.estado=1 and E.estado=1
                GROUP BY I.red,I.codlocal,
                I.codmod,
                E.nombie,
                I.nivel,
                E.cod_grado,
                E.seccion,
                M.materia,
                E.cantidad
                ORDER BY nombie ASC");
        return $data;
    }
    
    
    public function excel_ie_que_faltan_evaluacion_secundaria(){
        date_default_timezone_set('America/Lima');
        $fileName="IE_que_faltan_evaluacion.xlsx";   
        $spread = new Spreadsheet();
        $sheet = $spread->getActiveSheet();
        $sheet->setTitle("DIRECTORES");
        $letra = $this->letras;
        $cabeza = array('RED', 'CODLOCAL', 'CODMOD', 'INSTITUCION', 'NIVEL', 'GRADO', 'SECCION', 'AREA', 'TOTAL ALUMNOS', 'REGISTROS', 'DELTA', 'COMPLETOS');
        for ($i=0; $i < count($cabeza); $i++) {
            $sheet->setCellValue($letra[$i]."1", $cabeza[$i]);
            $sheet->getColumnDimension($letra[$i])->setAutoSize(true);
        }
        
        $data = $this->query_excel_ie_que_faltan_evaluacion_secundaria();
        for ($i=0; $i < count($data); $i++) {
        $nro = $i+2;
        $key = $data[$i];
        $j=0;
        $sheet->setCellValue($letra[$j++].$nro, $key->red);
        $sheet->setCellValue($letra[$j++].$nro, $key->codlocal);
        $sheet->setCellValue($letra[$j++].$nro, $key->codmod);
        $sheet->setCellValue($letra[$j++].$nro, $key->nombie);
        $sheet->setCellValue($letra[$j++].$nro, $key->nivel);
        $sheet->setCellValue($letra[$j++].$nro, $key->cod_grado);
        $sheet->setCellValue($letra[$j++].$nro, $key->seccion);
        $sheet->setCellValue($letra[$j++].$nro, $key->materia);
        $sheet->setCellValue($letra[$j++].$nro, $key->total_alumnos);
        $sheet->setCellValue($letra[$j++].$nro, $key->registrado_datos);
        $sheet->setCellValue($letra[$j++].$nro, $key->delta);
        $sheet->setCellValue($letra[$j++].$nro, $key->completo);
        }
        
        $sheet->freezePaneByColumnAndRow(1,2);//Inmobilizar fila superior
        $sheet->getRowDimension('1')->setRowHeight(39.8);
        $sheet->getStyle('A1:'.$letra[count($cabeza)].'1')->getFont()->setBold(true);//Negrita en fila superior
        //$sheet->getStyle('A1:N'.($i+1))->getFont()->setName('Courier New');//Negrita en fila superior
        # Crear un "escritor"
        $writer = new Xlsx($spread);
        # Le pasamos la ruta de guardado
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
    }
    
    function query_excel_ie_que_faltan_evaluacion_secundaria(){
        $data = DB::connection('registrodocente')->select("SELECT 
                I.red,
                I.codlocal,
                I.codmod,
                E.nombie,
                I.nivel,
                E.cod_grado,
                E.seccion,
                M.materia,
                E.cantidad as total_alumnos,
                Count(Distinct R.id_detalle_alumno) as registrado_datos,
                E.cantidad - Count(Distinct R.id_detalle_alumno) as delta,
                IF(E.cantidad=Count(Distinct R.id_detalle_alumno),'SI','NO') as completo
                FROM app_matrix  M 
                INNER JOIN app_matrix_detalle D ON M.id_matrix=D.id_matrix
                INNER JOIN app_matrix_evaluacion_respuesta_1SEC R ON D.id_matrix_detalle=R.id_matrix_detalle
                INNER JOIN app_matrix_evaluacion_alumno A ON R.id_detalle_alumno=A.id_detalle_alumno
                INNER JOIN app_matrix_evaluacion E ON A.id_evaluacion=E.id_evaluacion
                INNER JOIN iiee_a_evaluar_RIE I ON E.codmodce=I.codmod
                WHERE M.estado=1 and D.estado=1 and A.estado=1 and E.estado=1
                GROUP BY I.red,I.codlocal,
                I.codmod,
                E.nombie,
                I.nivel,
                E.cod_grado,
                E.seccion,
                M.materia,
                E.cantidad
                UNION
                SELECT 
                I.red,
                I.codlocal,
                I.codmod,
                E.nombie,
                I.nivel,
                E.cod_grado,
                E.seccion,
                M.materia,
                E.cantidad as total_alumnos,
                Count(Distinct R.id_detalle_alumno) as registrado_datos,
                E.cantidad - Count(Distinct R.id_detalle_alumno) as delta,
                IF(E.cantidad=Count(Distinct R.id_detalle_alumno),'SI','NO') as completo
                FROM app_matrix  M 
                INNER JOIN app_matrix_detalle D ON M.id_matrix=D.id_matrix
                INNER JOIN app_matrix_evaluacion_respuesta_2SEC R ON D.id_matrix_detalle=R.id_matrix_detalle
                INNER JOIN app_matrix_evaluacion_alumno A ON R.id_detalle_alumno=A.id_detalle_alumno
                INNER JOIN app_matrix_evaluacion E ON A.id_evaluacion=E.id_evaluacion
                INNER JOIN iiee_a_evaluar_RIE I ON E.codmodce=I.codmod
                WHERE M.estado=1 and D.estado=1 and A.estado=1 and E.estado=1
                GROUP BY I.red,I.codlocal,
                I.codmod,
                E.nombie,
                I.nivel,
                E.cod_grado,
                E.seccion,
                M.materia,
                E.cantidad
                UNION
                SELECT 
                I.red,
                I.codlocal,
                I.codmod,
                E.nombie,
                I.nivel,
                E.cod_grado,
                E.seccion,
                M.materia,
                E.cantidad as total_alumnos,
                Count(Distinct R.id_detalle_alumno) as registrado_datos,
                E.cantidad - Count(Distinct R.id_detalle_alumno) as delta,
                IF(E.cantidad=Count(Distinct R.id_detalle_alumno),'SI','NO') as completo
                FROM app_matrix  M 
                INNER JOIN app_matrix_detalle D ON M.id_matrix=D.id_matrix
                INNER JOIN app_matrix_evaluacion_respuesta_3SEC R ON D.id_matrix_detalle=R.id_matrix_detalle
                INNER JOIN app_matrix_evaluacion_alumno A ON R.id_detalle_alumno=A.id_detalle_alumno
                INNER JOIN app_matrix_evaluacion E ON A.id_evaluacion=E.id_evaluacion
                INNER JOIN iiee_a_evaluar_RIE I ON E.codmodce=I.codmod
                WHERE M.estado=1 and D.estado=1 and A.estado=1 and E.estado=1
                GROUP BY I.red,I.codlocal,
                I.codmod,
                E.nombie,
                I.nivel,
                E.cod_grado,
                E.seccion,
                M.materia,
                E.cantidad
                UNION
                SELECT 
                I.red,
                I.codlocal,
                I.codmod,
                E.nombie,
                I.nivel,
                E.cod_grado,
                E.seccion,
                M.materia,
                E.cantidad as total_alumnos,
                Count(Distinct R.id_detalle_alumno) as registrado_datos,
                E.cantidad - Count(Distinct R.id_detalle_alumno) as delta,
                IF(E.cantidad=Count(Distinct R.id_detalle_alumno),'SI','NO') as completo
                FROM app_matrix  M 
                INNER JOIN app_matrix_detalle D ON M.id_matrix=D.id_matrix
                INNER JOIN app_matrix_evaluacion_respuesta_4SEC R ON D.id_matrix_detalle=R.id_matrix_detalle
                INNER JOIN app_matrix_evaluacion_alumno A ON R.id_detalle_alumno=A.id_detalle_alumno
                INNER JOIN app_matrix_evaluacion E ON A.id_evaluacion=E.id_evaluacion
                INNER JOIN iiee_a_evaluar_RIE I ON E.codmodce=I.codmod
                WHERE M.estado=1 and D.estado=1 and A.estado=1 and E.estado=1
                GROUP BY I.red,I.codlocal,
                I.codmod,
                E.nombie,
                I.nivel,
                E.cod_grado,
                E.seccion,
                M.materia,
                E.cantidad
                UNION
                SELECT 
                I.red,
                I.codlocal,
                I.codmod,
                E.nombie,
                I.nivel,
                E.cod_grado,
                E.seccion,
                M.materia,
                E.cantidad as total_alumnos,
                Count(Distinct R.id_detalle_alumno) as registrado_datos,
                E.cantidad - Count(Distinct R.id_detalle_alumno) as delta,
                IF(E.cantidad=Count(Distinct R.id_detalle_alumno),'SI','NO') as completo
                FROM app_matrix  M 
                INNER JOIN app_matrix_detalle D ON M.id_matrix=D.id_matrix
                INNER JOIN app_matrix_evaluacion_respuesta_5SEC R ON D.id_matrix_detalle=R.id_matrix_detalle
                INNER JOIN app_matrix_evaluacion_alumno A ON R.id_detalle_alumno=A.id_detalle_alumno
                INNER JOIN app_matrix_evaluacion E ON A.id_evaluacion=E.id_evaluacion
                INNER JOIN iiee_a_evaluar_RIE I ON E.codmodce=I.codmod
                WHERE M.estado=1 and D.estado=1 and A.estado=1 and E.estado=1
                GROUP BY I.red,I.codlocal,
                I.codmod,
                E.nombie,
                I.nivel,
                E.cod_grado,
                E.seccion,
                M.materia,
                E.cantidad
                ORDER BY nombie ASC");
        return $data;
    }
		    
}