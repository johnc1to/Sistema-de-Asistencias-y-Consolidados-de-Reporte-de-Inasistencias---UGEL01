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

class Excel extends Controller
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
		    
		    
    public function fecha(){
        echo date('Y-m-d H:i:s');
    }
    
    
    public function soportetecnico($where=""){
        $query = DB::connection('formularios')->select("SELECT S.*,T.desTip,T.docestTip,
        IF(IFNULL(S.tipDocSop,'DNI')='DNI','L.E./D.N.I.',S.tipDocSop) as tipDocSop,
        IF(IFNULL(S.tipDocAntSop,'DNI')='DNI','L.E./D.N.I.',S.tipDocAntSop) as tipDocAntSop,
        SUBSTRING(S.graSop,1,1) as graSop,
        IFNULL(cueSop,'-') as cueSop,IFNULL(dniSop,'-') as dniSop,R.institucion,R.nivel,DATE_FORMAT(creado_at,'%d/%m/%Y') as fecha, DATE_FORMAT(S.updated_at,'%d/%m/%Y') as modificado 
        FROM soportetecnico S 
        INNER JOIN soportetecnicotipo T ON S.idTip = T.idTip
        INNER JOIN siic01ugel01gob_directores.iiee_a_evaluar_RIE R ON S.codmodSop = R.codmod 
        WHERE S.estSop=1 ".$where."
        ORDER BY idTip ASC");
        $result = array();
        foreach ($query as $key){
            $result[] = (Array)$key;
        }
        return $result;
    }

    public function anexo05director(){
        date_default_timezone_set('America/Lima');
        $fileName="ANEXO 5 Formato Directores.xlsx";   
        $spread = new Spreadsheet();
        $sheet = $spread->getActiveSheet();
        $sheet->setTitle("DIRECTORES");
        $letra = range('A','Z');
        $cabeza = array("DRE","UGEL","CÓDIGO MODULAR","ANEXO","NOMBRES","APELLIDO PATERNO","APELLIDO MATERNO","TIPO DE DOCUMENTO","NÚMERO DE DOCUMENTO","TELÉFONO PERSONAL","CORREO ELECTRÓNICO");
        for ($i=0; $i < count($cabeza); $i++) {
            $sheet->setCellValue($letra[$i]."5", $cabeza[$i]);
            $sheet->getColumnDimension($letra[$i])->setAutoSize(true);
        }
        $data = $this->soportetecnico("and S.etaSop='SOLICITADO' and S.idTip=6");

        for ($i=0; $i < count($data); $i++) { 
        $nro = $i+6;
        $key = $data[$i];
        $sheet->setCellValue("A".$nro, "DRE LIMA METROPOLITANA");
        $sheet->setCellValue("B".$nro, "UGEL 01 SAN JUAN DE MIRAFLORES");
        $sheet->setCellValue("C".$nro, $key['codmodSop']);
        $sheet->setCellValue("D".$nro, "0");
        $sheet->setCellValue("E".$nro, $key['nomSop']);
        $sheet->setCellValue("F".$nro, $key['apepatSop']);
        $sheet->setCellValue("G".$nro, $key['apematSop']);
        $sheet->setCellValue("H".$nro, $key['tipDocSop']);
        $sheet->setCellValue("I".$nro, $key['dniSop']);
        $sheet->setCellValue("J".$nro, $key['telSop']);
        //$sheet->setCellValue("K".$nro, "");
        $sheet->setCellValue("K".$nro, $key['corSop']);
        }
        $sheet->getRowDimension('5')->setRowHeight(39.8);
        $sheet->getStyle('A5:N5')->getFont()->setBold(true);//Negrita en fila superior
        //$sheet->getStyle('A5:N'.($i+1))->getFont()->setName('Courier New');//Negrita en fila superior
        # Crear un "escritor"
        
        // HOJA VERSION
        $spread->createSheet(1);
        $spread->setActiveSheetIndex(1);
        $spread->setActiveSheetIndex(1)->setCellValue('A1', '4');
        $spread->getActiveSheet()->setTitle('VERSION');
        $spread->setActiveSheetIndex(0);
        
        $writer = new Xlsx($spread);
        # Le pasamos la ruta de guardado
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
    }

    public function anexo06docentes(){
        date_default_timezone_set('America/Lima');
        $fileName="ANEXO 6 Formato Docente.xlsx";   
        $spread = new Spreadsheet();
        $sheet = $spread->getActiveSheet();
        $sheet->setTitle("DOCENTES");
        //$sheet->freezePaneByColumnAndRow(1,2);//Inmobilizar fila superior
        //$sheet->setCellValueByColumnAndRow(1, 1, "Valor A1"); 
        $letra = range('A','Z');
        $cabeza = array("DRE","UGEL","CÓDIGO MODULAR","ANEXO","NOMBRES","APELLIDO PATERNO","APELLIDO MATERNO","TIPO DE DOCUMENTO","NÚMERO DE DOCUMENTO","CORREO ELECTRÓNICO PERSONAL","TELÉFONO");
        for ($i=0; $i < count($cabeza); $i++) {
            $sheet->setCellValue($letra[$i]."5", $cabeza[$i]);
            $sheet->getColumnDimension($letra[$i])->setAutoSize(true);
        }
        $data = $this->soportetecnico("and S.etaSop='SOLICITADO' and S.idTip=3");
        for ($i=0; $i < count($data); $i++) {
        $nro = $i+6;
        $key = $data[$i];
        $sheet->setCellValue("A".$nro, "DRE LIMA METROPOLITANA");
        $sheet->setCellValue("B".$nro, "UGEL 01 SAN JUAN DE MIRAFLORES");
        $sheet->setCellValue("C".$nro, $key['codmodSop']);
        $sheet->setCellValue("D".$nro, "0");
        $sheet->setCellValue("E".$nro, $key['nomSop']);
        $sheet->setCellValue("F".$nro, $key['apepatSop']);
        $sheet->setCellValue("G".$nro, $key['apematSop']);
        $sheet->setCellValue("H".$nro, $key['tipDocSop']);
        $sheet->setCellValue("I".$nro, $key['dniSop']);
        $sheet->setCellValue("J".$nro, $key['corSop']);
        $sheet->setCellValue("K".$nro, $key['telSop']);
        }
        $sheet->getRowDimension('5')->setRowHeight(39.8);
        $sheet->getStyle('A5:k5')->getFont()->setBold(true);//Negrita en fila superior
        //$sheet->getStyle('A5:k'.($i+1))->getFont()->setName('Courier New');//Negrita en fila superior
        
        // HOJA VERSION
        $spread->createSheet(1);
        $spread->setActiveSheetIndex(1);
        $spread->setActiveSheetIndex(1)->setCellValue('A1', '4');
        $spread->getActiveSheet()->setTitle('VERSION');
        $spread->setActiveSheetIndex(0);
        
        $writer = new Xlsx($spread);
        # Le pasamos la ruta de guardado
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');    
    }

    public function anexo07estudiantes(){
        date_default_timezone_set('America/Lima');
        $fileName="ANEXO 7 Formato Estudiantes.xlsx";   
        $spread = new Spreadsheet();
        $sheet = $spread->getActiveSheet();
        $sheet->setTitle("ESTUDIANTES");
        //$sheet->freezePaneByColumnAndRow(1,2);//Inmobilizar fila superior
        //$sheet->setCellValueByColumnAndRow(1, 1, "Valor A1"); 
        $letra = range('A','Z');
        $cabeza = array("DRE","UGEL","CÓDIGO MODULAR","ANEXO","GRADO","NOMBRES","APELLIDO PATERNO","APELLIDO MATERNO","TIPO DE DOCUMENTO","NÚMERO DE DOCUMENTO","CÓDIGO ESTUDIANTE");
        for ($i=0; $i < count($cabeza); $i++) { 
            $sheet->setCellValue($letra[$i]."5", $cabeza[$i]);
            $sheet->getColumnDimension($letra[$i])->setAutoSize(true);
        }
        $data = $this->soportetecnico("and S.etaSop='SOLICITADO' and S.idTip=4");
        for ($i=0; $i < count($data); $i++) {
        $nro = $i+6;
        $key = $data[$i];
        $sheet->setCellValue("A".$nro, "DRE LIMA METROPOLITANA");
        $sheet->setCellValue("B".$nro, "UGEL 01 SAN JUAN DE MIRAFLORES");
        $sheet->setCellValue("C".$nro, $key['codmodSop']);
        $sheet->setCellValue("D".$nro, "0");
        $sheet->setCellValue("E".$nro, $key['graSop']);
        $sheet->setCellValue("F".$nro, $key['nomSop']);
        $sheet->setCellValue("G".$nro, $key['apepatSop']);
        $sheet->setCellValue("H".$nro, $key['apematSop']);
        $sheet->setCellValue("I".$nro, $key['tipDocSop']);
        $sheet->setCellValue("J".$nro, $key['dniSop']);
        $sheet->setCellValue("K".$nro, $key['codSop']);
        }
        $sheet->getRowDimension('5')->setRowHeight(39.8);
        $sheet->getStyle('A5:K5')->getFont()->setBold(true);//Negrita en fila superior
        $sheet->getStyle('A5:K5')->getFont()->setName('Courier New');//Negrita en fila superior
        
        // HOJA VERSION
        $spread->createSheet(1);
        $spread->setActiveSheetIndex(1);
        $spread->setActiveSheetIndex(1)->setCellValue('A1', '4');
        $spread->getActiveSheet()->setTitle('VERSION');
        $spread->setActiveSheetIndex(0);
        
        $writer = new Xlsx($spread);
        # Le pasamos la ruta de guardado
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');    
    }

    public function anexo08reportemensual(Request $request){
        date_default_timezone_set('America/Lima');
        $fileName="ANEXO 8 Reporte mensual - cambio de contraseñas.xlsx";   
        $spread = new Spreadsheet();
        $sheet = $spread->getActiveSheet();
        $sheet->setTitle("Hoja1");
        //$sheet->freezePaneByColumnAndRow(1,2);//Inmobilizar fila superior
        //$sheet->setCellValueByColumnAndRow(1, 1, "Valor A1"); 
        $sheet->setCellValue("A5", "REPORTE MENSUAL - CAMBIO DE CONTRASEÑAS");
        $sheet->setCellValue("A6", "Fecha del reporte:");
        $sheet->setCellValue("A7", "Periodo: mes");
        $sheet->setCellValue("A8", "DRE:");
        $sheet->setCellValue("A9", "UGEL:");
        $sheet->setCellValue("A10", "Nombre del especialista:");
        $sheet->setCellValue("A11", "Usuario asignado:");
        $sheet->setCellValue("A12", "Indicaciones: En el nombre de usuario indicar los datos como se presentan en la consola de administración, en la cuenta @aprendoencasa solo colocar la cuenta del correo @aprendoencasa.pe");

        $sheet->setCellValue("C6", date('d/m/Y'));
        $sheet->setCellValue("C7", $request['mes']);
        $sheet->setCellValue("C8", "DRE LIMA METROPOLITANA");
        $sheet->setCellValue("C9", "UGEL 01 SAN JUAN DE MIRAFLORES");
        $sheet->setCellValue("C10", "MILAGROS OSORES MAITA");
        $sheet->setCellValue("C11", "150102pass01@aprendoencasa.pe");
        
            $sheet->setCellValue("A14", 'N°');
            $sheet->setCellValue("B14", 'NOMBRE DEL USUARIO');
            $sheet->setCellValue("C14", 'CUENTA @APRENDOENCASA');
            $sheet->setCellValue("D14", 'FECHA');
            $sheet->getColumnDimension('A')->setWidth(4);
            $sheet->getColumnDimension('B')->setWidth(32.67);
            $sheet->getColumnDimension('C')->setWidth(27.67);
            $sheet->getColumnDimension('D')->setWidth(15.78);
        
        $data = $this->soportetecnico("and S.etaSop='ATENDIDO' and T.ordTip=1 and DATE_FORMAT(creado_at,'%Y-%m')='".$request['aniomes']."'");
        $nro = 15;
        if($data){
        for ($i=0; $i < count($data); $i++) {       
        $nro = 15+$i; 
        $key = $data[$i];
        $sheet->setCellValue("A".$nro, $i+1);
        $sheet->setCellValue("B".$nro, $key['nomSop'].' '.$key['apepatSop'].' '.$key['apematSop']);
        $sheet->setCellValue("C".$nro, $key['cueSop']);
        $sheet->setCellValue("D".$nro, $key['modificado']);
        }
        }else{
            $sheet->setCellValue("A".$nro, 'No se ha realizado cambios de contraseña este mes'); 
        }
        $sheet->mergeCells('A12:D13');
        $sheet->getStyle('A12')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A5:A11')->getFont()->setBold(true);
        $sheet->getStyle('A14:D14')->getFont()->setBold(true);//Negrita en fila superior
        
        // HOJA VERSION
        $spread->createSheet(1);
        $spread->setActiveSheetIndex(1);
        $spread->setActiveSheetIndex(1)->setCellValue('A1', '4');
        $spread->getActiveSheet()->setTitle('VERSION');
        $spread->setActiveSheetIndex(0);
        
        $writer = new Xlsx($spread);
        # Le pasamos la ruta de guardado
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');    
    }
    
    public function anexo09actualizaciondirectores(){
        date_default_timezone_set('America/Lima');
        $fileName="ANEXO 9 Formato Actualizacion de Directores.xlsx";   
        $spread = new Spreadsheet();
        $sheet = $spread->getActiveSheet();
        $sheet->setTitle("DIRECTORES");
        $letra = range('A','Z');
        $cabeza = array("DRE","UGEL","CÓDIGO MODULAR","ANEXO","NOMBRES DEL NUEVO DIRECTOR","APELLIDO PATERNO DEL NUEVO DIRECTOR","APELLIDO MATERNO DEL NUEVO DIRECTOR","TIPO DE DOCUMENTO DEL NUEVO DIRECTOR","NÚMERO DE DOCUMENTO DEL NUEVO DIRECTOR","NÚMERO CELULAR DEL NUEVO DIRECTOR","CORREO ELECTRÓNICO DEL NUEVO DIRECTOR","TIPO DE DOCUMENTO DEL DIRECTOR ANTERIOR","NÚMERO DE DOCUMENTO DEL DIRECTOR ANTERIOR","NOMBRES DIRECTOR ANTERIOR","APELLIDO PATERNO DIRECTOR ANTERIOR","APELLIDO MATERNO DIRECTOR ANTERIOR","MOTIVO (ELEGIR)");
        for ($i=0; $i < count($cabeza); $i++) {
            $sheet->setCellValue($letra[$i]."5", $cabeza[$i]);
            $sheet->getColumnDimension($letra[$i])->setAutoSize(true);
        }
        $data = $this->soportetecnico("and S.etaSop='SOLICITADO' and S.idTip=7");
        //dd($data);
        for ($i=0; $i < count($data); $i++) { 
        $nro = $i+6;
        $key = $data[$i];
        $sheet->setCellValue("A".$nro, "DRE LIMA METROPOLITANA");
        $sheet->setCellValue("B".$nro, "UGEL 01 SAN JUAN DE MIRAFLORES");
        $sheet->setCellValue("C".$nro, $key['codmodSop']);
        $sheet->setCellValue("D".$nro, '0');
        $sheet->setCellValue("E".$nro, $key['nomSop']);
        $sheet->setCellValue("F".$nro, $key['apepatSop']);
        $sheet->setCellValue("G".$nro, $key['apematSop']);
        $sheet->setCellValue("H".$nro, $key['tipDocSop']);
        $sheet->setCellValue("I".$nro, $key['dniSop']);
        $sheet->setCellValue("J".$nro, $key['telSop']);
        $sheet->setCellValue("K".$nro, $key['corSop']);
        $sheet->setCellValue("L".$nro, $key['tipDocAntSop']);
        $sheet->setCellValue("M".$nro, $key['dniAntSop']);
        $sheet->setCellValue("N".$nro, $key['nomAntSop']);
        $sheet->setCellValue("O".$nro, $key['apepatAntSop']);
        $sheet->setCellValue("P".$nro, $key['apematAntSop']);
        $sheet->setCellValue("Q".$nro, $key['obsSop']);
        }
        //$sheet->getRowDimension('1')->setRowHeight(39.8);
        $sheet->getStyle('A5:N5')->getFont()->setBold(true);//Negrita en fila superior
        $sheet->getStyle('A5:N'.($i+1))->getFont()->setName('Courier New');//Negrita en fila superior
        # Crear un "escritor"
        
        // HOJA VERSION
        $spread->createSheet(1);
        $spread->setActiveSheetIndex(1);
        $spread->setActiveSheetIndex(1)->setCellValue('A1', '4');
        $spread->getActiveSheet()->setTitle('VERSION');
        $spread->setActiveSheetIndex(0);
        
        $writer = new Xlsx($spread);
        # Le pasamos la ruta de guardado
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
    }
    
    public function anexotraslado(){
        date_default_timezone_set('America/Lima');
        $fileName="TRASLADO.xlsx";   
        $spread = new Spreadsheet();
        $sheet = $spread->getActiveSheet();
        $sheet->setTitle("TRASLADO");
        $sheet->freezePaneByColumnAndRow(1,2);//Inmobilizar fila superior
        //$sheet->setCellValueByColumnAndRow(1, 1, "Valor A1"); 
        $letra = range('A','Z');
        $cabeza = array("DRE","UGEL","TIPO","CÓDIGO MODULAR","INSTITUCION","GRADO","NOMBRES","APELLIDO.PATERNO","APELLIDO.MATERNO","DNI","CÓDIGO ESTUDIANTE");
        for ($i=0; $i < count($cabeza); $i++) { 
            $sheet->setCellValue($letra[$i]."1", $cabeza[$i]);
            $sheet->getColumnDimension($letra[$i])->setAutoSize(true);
        }
        $data = $this->soportetecnico("and S.etaSop='TRASLADO DE UGEL'");
        for ($i=0; $i < count($data); $i++) {
        $nro = $i+2;
        $key = $data[$i];
        $sheet->setCellValue("A".$nro, "DRE LIMA METROPOLITANA");
        $sheet->setCellValue("B".$nro, "UGEL 01 SAN JUAN DE MIRAFLORES");
        $sheet->setCellValue("C".$nro, $key['docestTip']);
        $sheet->setCellValue("D".$nro, $key['codmodSop']);
        $sheet->setCellValue("E".$nro, $key['institucion']);
        $sheet->setCellValue("F".$nro, $key['graSop']);
        $sheet->setCellValue("G".$nro, $key['nomSop']);
        $sheet->setCellValue("H".$nro, $key['apepatSop']);
        $sheet->setCellValue("I".$nro, $key['apematSop']);
        $sheet->setCellValue("J".$nro, $key['dniSop']);
        $sheet->setCellValue("K".$nro, $key['codSop']);
        }
        $sheet->getRowDimension('1')->setRowHeight(39.8);
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);//Negrita en fila superior
        $sheet->getStyle('A1:K'.($i+1))->getFont()->setName('Courier New');//Negrita en fila superior
        
        // HOJA VERSION
        $spread->createSheet(1);
        $spread->setActiveSheetIndex(1);
        $spread->setActiveSheetIndex(1)->setCellValue('A1', '4');
        $spread->getActiveSheet()->setTitle('VERSION');
        $spread->setActiveSheetIndex(0);
        
        $writer = new Xlsx($spread);
        # Le pasamos la ruta de guardado
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');    
    }
    
    public function verreqseccincrementosecciones($where=""){
        $query =DB::connection('notificacion')->select("SELECT 
            I.codmod,
            I.codlocal,
            I.institucion,
            I.distrito,
            I.tecnico,
            I.jornada,
            I.horas,
            I.nivel,
            I.gestion,
            I.turno,
            S.grado,
            S.seccincremento,
            I.horas*S.seccincremento as bolsahoras,
            S.aulafisica,
            S.mobiliario,
            R.cod_reclamo,
            R.idreclamo,
            MAX(IF(A.file_tipo_tramite='Oficio simple de solicitud de requerimiento de incremento de seccion',A.archivo,NULL)) as adj1,
            MAX(IF(A.file_tipo_tramite='Copia de la ficha de resumen extraida del aplicativo SIAGIE y PAP aprobado 2022',A.archivo,NULL)) as adj2
            FROM siic01ugel01gob_directores.iiee_a_evaluar_RIE I 
            INNER JOIN reclamos_detallerequerimientoseccion  S ON I.codmod = S.codmod
            INNER JOIN reclamos R ON S.idreclamo = R.idreclamo
            INNER JOIN reclamos_adjunto A ON R.idreclamo = A.idreclamo
            WHERE R.estado=1 $where
            GROUP BY 
            I.codmod,
            I.codlocal,
            I.institucion,
            I.distrito,
            I.tecnico,
            I.jornada,
            I.horas,
            I.nivel,
            I.gestion,
            I.turno,
            S.grado,
            S.seccincremento,
            S.seccincremento,
            S.aulafisica,
            S.mobiliario,
            R.cod_reclamo,
            R.idreclamo
            ORDER BY I.institucion ASC,I.idnivel ASC");
        $result = array();
        foreach ($query as $key){
            $result[] = (Array)$key;
        }
        return $result;
    }

    public function anexo_racio_reqseccincrementosecciones(Request $request){
        $where = '';
        $where .= ($request['idnivel'])?' and idnivel='.$request['idnivel']:'';
        $fileName="Requerimiento de secciones.xlsx";   
        $spread = new Spreadsheet();
        $sheet = $spread->getActiveSheet();
        $sheet->setTitle("Requerimiento de secciones");
        $sheet->freezePaneByColumnAndRow(1,3);//Inmobilizar fila superior
        //$sheet->setCellValueByColumnAndRow(1, 1, "Valor A1"); 
        $letra = range('A','Z');
        $cabeza = array("N°","Código Modular","Código Local","Institución Educativa","Distrito","Tec","Jor","Horas","Nivel","Gestion","Turno","Grado","Cantidad de secciones de incremento","Horas bolsa","Cuenta con aula fisica","Cuenta con mobiliario","Expediente");
        for ($i=0; $i < count($cabeza); $i++) {
            $sheet->setCellValue($letra[$i]."2", $cabeza[$i]);
            $sheet->getColumnDimension($letra[$i])->setAutoSize(true);
        }
        $sheet->setCellValue("A1","DATOS DE LA II.EE");
        $sheet->setCellValue("L1", "REQUERIMIENTO");
        $sheet->setCellValue("Q1", "DOCUMENTO - SUSTENTO");
        $sheet->mergeCells("A1:K1");
        $sheet->mergeCells("L1:P1");
        
        $data = $this->verreqseccincrementosecciones($where);
            for ($i=0; $i < count($data); $i++) {
                $nro = $i+3;
                $col=0;
                $key = $data[$i];
                $sheet->setCellValue($letra[$col++].$nro, $i+1);
                $sheet->setCellValue($letra[$col++].$nro, $key['codmod']);
                $sheet->setCellValue($letra[$col++].$nro, $key['codlocal']);
                $sheet->setCellValue($letra[$col++].$nro, $key['institucion']);
                $sheet->setCellValue($letra[$col++].$nro, $key['distrito']);
                $sheet->setCellValue($letra[$col++].$nro, $key['tecnico']);
                $sheet->setCellValue($letra[$col++].$nro, $key['jornada']);
                $sheet->setCellValue($letra[$col++].$nro, $key['horas']);
                $sheet->setCellValue($letra[$col++].$nro, $key['nivel']);
                $sheet->setCellValue($letra[$col++].$nro, $key['gestion']);
                $sheet->setCellValue($letra[$col++].$nro, $key['turno']);

                $sheet->setCellValue($letra[$col++].$nro, $key['grado']);
                $sheet->setCellValue($letra[$col++].$nro, $key['seccincremento']);
                $sheet->setCellValue($letra[$col++].$nro, $key['bolsahoras']);
                $sheet->setCellValue($letra[$col++].$nro, $key['aulafisica']);
                $sheet->setCellValue($letra[$col++].$nro, $key['mobiliario']);

                $sheet->setCellValue($letra[$col++].$nro, $key['cod_reclamo']);
            }
        //$sheet->getRowDimension('1')->setRowHeight(39.8);
        $sheet->setCellValue("B".($nro+2), "NOTA: LAS II.EE, SOLO PUEDEN CONTAR CON UN MAXIMO DE 3 SECCIONES POR NIVEL COMO CANTIDAD MAXIMA DE REQUERIMIENTOS.");
        $sheet->getStyle("B".($nro+2))->getFont()->setBold(true);
        $sheet->mergeCells("B".($nro+2).":"."L".($nro+2));

        $sheet->getStyle('A1:'.$letra[$col].'2')->getFont()->setBold(true);//Negrita en fila superior
        //$sheet->getStyle('A1:J'.($i+1))->getFont()->setName('Courier New');//Negrita en fila superior
        $writer = new Xlsx($spread);
        # Le pasamos la ruta de guardado
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');    
    }
    
    
    function get_excel_postulantes(Request $request){
    $titulo          = $request['titulo'];
    $carpeta         = $request['carpeta'];
    $idmantenimiento = $request['idmantenimiento'];
    $idreclamo       = $request['idreclamo'];
    $this->excel_postulantes($titulo,$carpeta,$idmantenimiento,$idreclamo);
    }
    
    //$titulo='CAS 012-2020',$carpeta='',$idmantenimiento=110,$idreclamo='1396,1171,1166,1345,1390'
    public function excel_postulantes(Request $request){
        $titulo          = $request['titulo'];
        $carpeta         = $request['carpeta'];
        $idmantenimiento = $request['idmantenimiento'];
        $idreclamo       = $request['idreclamo'];
        date_default_timezone_set('America/Lima');
        $spread = new Spreadsheet();
        $sheet = $spread->getActiveSheet();
        $filename=substr('postulantes',0,20); //Guardar nuestro libro de trabajo como este nombre de archivo
        $sheet->setTitle($filename);
        
        $spread->getActiveSheet()->setCellValue('A1', $titulo);
        $spread->getActiveSheet()->setCellValue('A3', 'N');
        $spread->getActiveSheet()->setCellValue('B3', 'EXPEDIENTE');
        $spread->getActiveSheet()->setCellValue('C3', 'DNI');
        $spread->getActiveSheet()->setCellValue('D3', 'APELLIDOS Y NOMBRES');
        $spread->getActiveSheet()->setCellValue('E3', 'EVALUACIÓN CURRICULAR');
        $spread->getActiveSheet()->setCellValue('F3', 'OBSERVACIÓN');
        $spread->getActiveSheet()->setCellValue('G3', 'ESPECIALISTA EVALUÓ');
        $spread->getActiveSheet()->setCellValue('H3', 'FECHA EVALUACIÓN');
        $spread->getActiveSheet()->setCellValue('I3', 'HORA EVALUACIÓN');
        $spread->getActiveSheet()->setCellValue('J3', 'CELULAR');
        $spread->getActiveSheet()->setCellValue('K3', 'CORREO');
        $spread->getActiveSheet()->setCellValue('L3', 'FECHA DE EXPEDIENTE');
        
        $spread->getActiveSheet()->setCellValue('M3', 'RESUMEN DE SU PEDIDO');
        $spread->getActiveSheet()->setCellValue('N3', 'FUNDAMENTOS DEL PEDIDO');
        $spread->getActiveSheet()->setCellValue('O3', 'DIRECCIÓN');

        $spread->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $spread->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $spread->getActiveSheet()->getColumnDimension('C')->setWidth(11);
        $spread->getActiveSheet()->getColumnDimension('D')->setWidth(35);
        $spread->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $spread->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $spread->getActiveSheet()->getColumnDimension('G')->setWidth(25);
        $spread->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $spread->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $spread->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $spread->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $spread->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        
        $spread->getActiveSheet()->getColumnDimension('M')->setWidth(20);
        $spread->getActiveSheet()->getColumnDimension('N')->setWidth(20);
        $spread->getActiveSheet()->getColumnDimension('O')->setWidth(20);

        //$data = $this->reclamos->listar_postulantes_x_exp(" AND R.id_tipo_tramite = ".$idmantenimiento." and R.idreclamo IN($idreclamo) ");
        $data = DB::connection('notificacion')->select("SELECT 
        R.resumen_pedido,
        R.reclamo,
        R.expediente,
        M.id_especialidad,
        DATE_FORMAT(R.fecha_expediente,'%d/%m/%Y') as fecha_expediente,
        R.cas_puntaje,
        U.documento,
        R.cod_reclamo,
        U.apellido_paterno,
        U.apellido_materno,
        U.nombres,
        CONCAT(U.nombres,' ',U.apellido_paterno,' ',U.apellido_materno) as postulante,
        R.celular,
        R.correo,
        R.cas_evaluacion,
        R.cas_observacion,
        R.cas_especialista,
        DATE_FORMAT(R.cas_fecha_evaluacion,'%d/%m/%Y') as cas_fecha_evaluacion,
        DATE_FORMAT(R.cas_fecha_evaluacion,'%H:%i:%s') as cas_hora_evaluacion,
        CONCAT(
            IF(U.tipo_via<>'',CONCAT('',U.tipo_via),''),
            IF(U.domicilio<>'',CONCAT(' ',U.domicilio),''),
            IF(U.inmueble<>'',CONCAT(' ',U.inmueble),''),
            IF(U.interior<>'',CONCAT(' dptm ',U.interior),''),
            IF(U.piso<>'',CONCAT(' piso ',U.piso),''),
            IF(U.mz<>'',CONCAT(' Mz ',U.mz),''),
            IF(U.lote<>'',CONCAT(' lote ',U.lote),''),
            IF(U.km<>'',CONCAT(' Km ',U.km),''),
            IF(U.sector<>'',CONCAT(' sector ',U.sector),''),
            IF(U.block<>'',CONCAT(' block ',U.block),''),
            IF(U.tipo_zona<>'',CONCAT(' - ',U.tipo_zona),''),
            IF(U.nombre_zona<>'',CONCAT(' ',U.nombre_zona),'')
        ) as texto_domicilio
        FROM reclamos R 
        INNER JOIN receptor        U ON R.idreceptor      = U.idreceptor
        INNER JOIN mantenimiento   M ON R.id_tipo_tramite = M.idmantenimiento
        WHERE R.estado = 1 and R.etapa NOT IN('SOLICITADO','OBSERVADO','EN ESPERA') and R.id_tipo_tramite = $idmantenimiento");
        $nro = 4;
        if($data){
        for ($i=0; $i < count($data); $i++) {
            $key = $data[$i];
            $spread->getActiveSheet()->setCellValue('A'.$nro, ($i+1));
            $spread->getActiveSheet()->setCellValue('B'.$nro, $key->cod_reclamo);
            $spread->getActiveSheet()->setCellValue('C'.$nro, $key->documento);
            $spread->getActiveSheet()->setCellValue('D'.$nro, $key->apellido_paterno.' '.$key->apellido_materno.' '.$key->nombres);
            $spread->getActiveSheet()->setCellValue('E'.$nro, $key->cas_puntaje);
            $spread->getActiveSheet()->setCellValue('F'.$nro, $key->cas_observacion);
            $spread->getActiveSheet()->setCellValue('G'.$nro, $key->cas_especialista);
            $spread->getActiveSheet()->setCellValue('H'.$nro, $key->cas_fecha_evaluacion);
            $spread->getActiveSheet()->setCellValue('I'.$nro, $key->cas_hora_evaluacion);
            $spread->getActiveSheet()->setCellValue('J'.$nro, $key->celular);
            $spread->getActiveSheet()->setCellValue('K'.$nro, $key->correo);
            $spread->getActiveSheet()->setCellValue('L'.$nro, $key->fecha_expediente);
            $spread->getActiveSheet()->setCellValue('M'.$nro, $key->resumen_pedido);
            $spread->getActiveSheet()->setCellValue('N'.$nro, $key->reclamo);
            $spread->getActiveSheet()->setCellValue('O'.$nro, $key->texto_domicilio);
            $nro++;
        }
        }else{
            $spread->getActiveSheet()->setCellValue('A'.$nro, 'NO SE HA ENCONTRADO INFORMACIÓN DE POSTULANTES PARA MOSTRAR');
        }
        
        $spread->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
        $spread->getActiveSheet()->getStyle('A3:O3')->getFont()->setBold(true);
        //$sheet->getRowDimension('1')->setRowHeight(39.8);
        //$sheet->getStyle('A1:N1')->getFont()->setBold(true);//Negrita en fila superior
        //$sheet->getStyle('A1:N'.($i+1))->getFont()->setName('Courier New');//Negrita en fila superior
        # Crear un "escritor"
        $writer = new Xlsx($spread);
        # Le pasamos la ruta de guardado
        if($carpeta){
        $writer->save($carpeta.'postulantes.xls');
        }else{
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($titulo.".xlsx").'"');
        $writer->save('php://output');
        }
    }
    
    public function exportar_respuestas_ficha(Request $request){
        ini_set('memory_limit', '1800M');
        ini_set('max_execution_time', 0);
        date_default_timezone_set('America/Lima');
        $fileName="Respuestas.xlsx";   
        $spread = new Spreadsheet();
        $sheet = $spread->getActiveSheet();
        $sheet->setTitle("Respuestas");
        //$sheet->freezePaneByColumnAndRow(1,2);//Inmobilizar fila superior
        $idficha  = $request['idficha'];
        $ficha    = Fichas::where(['estFic'=>1,'idFic'=>$idficha])->first();
        $pregunta = Preguntas::where(['estPre'=>1,'idFic'=>$idficha])->select("*",DB::raw("IF(textPre='-',gruPre,textPre) as texto"))->orderBy('ordPre','ASC')->get();
        $data = $this->get_respuestas_ficha($idficha,$ficha->tipFic);
        //echo '<pre>';
        //print_r($data);
        //echo '</pre>';
        //dd($data);
        $nletra = 0;
        $letras = array(
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
            
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "Nro"); 
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "CODLOCAL");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "CODMOD"); 
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "RED");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "INSTITUCION"); 
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "MODALIDAD");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "NIVEL");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "DISTRITO"); 
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "DNI");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "DIRECTOR");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "CARGO");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "CELULAR"); 
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "CORREO");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "FECHA");

            
            if($ficha->tipFic=='AL DIRECTIVO'){
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "ESPECIALISTA");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "DNI");
            }
            
            if($ficha->tipFic=='AL DIRECTIVO' or $ficha->tipFic=='DIRECTIVO AL DOCENTE'){
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "NUMERO DE VISITA A LA IE");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "FECHA DE APLICACION");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "MEDIO POR EL CUAL SE DESARROLLA LA ASISTENCIA TECNICA");
            
            $iniciomerge=$nletra;
            $spread->getActiveSheet()->setCellValue($letras[$nletra]."1",  "DATOS DEL DOCENTE MONITOREADO");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "DOCENTE");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "DNI");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "GRADO");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "SECCION");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "AREA");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "N° DE ESTUDIANTES PATRICULADOS");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "N° de ESTUDIANTES ATENDIDOS PRESENCIAL");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "N° de ESTUDIANTES ATENDIDOS ASINCRÓNICO");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "TIPO DE SERVICIO");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "TELEFONO");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "CORREO ELECTRONICO");
            $spread->getActiveSheet()->mergeCells($letras[$iniciomerge]."1:".($letras[$nletra-1])."1");
            }

            foreach($pregunta as $key){
            $iniciomerge=$nletra;
            $spread->getActiveSheet()->setCellValue($letras[$nletra]."1",    $key->gruPre);
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  (($key->nroPre=='-')?'':$key->nroPre.'. ').$key->textPre);
                if($key->varTitPre){
                    foreach( explode(',',$key->varTitPre)  as $key1){
                        $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  $key1);
                    }
                }
                $spread->getActiveSheet()->mergeCells($letras[$iniciomerge]."1:".($letras[$nletra-1])."1");
            }
            
            if($ficha->tipFic=='AL DIRECTIVO' or $ficha->tipFic=='DIRECTIVO AL DOCENTE'){
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "CONCLUSIONES");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "RECOMENDACIONES");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "COMPROMISOS DEL DIRECTIVO");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "¿CÓMO SE IMPLEMENTA EL COMPROMISO?");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "COMPROMISOS DEL DOCENTE");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "¿CÓMO SE IMPLEMENTA EL COMPROMISO?");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "COMPROMISOS DEL ESPECIALISTA DE UGEL");
            $spread->getActiveSheet()->setCellValue($letras[$nletra++]."2",  "¿CÓMO SE IMPLEMENTA EL COMPROMISO?");
            }
            
            $spread->getActiveSheet()->getColumnDimension("A")->setWidth(8);
            $spread->getActiveSheet()->getColumnDimension("B")->setWidth(11);
            $spread->getActiveSheet()->getColumnDimension("C")->setWidth(11);
            $spread->getActiveSheet()->getColumnDimension("D")->setWidth(6);
            $spread->getActiveSheet()->getColumnDimension("E")->setWidth(30);
            $spread->getActiveSheet()->getColumnDimension("F")->setWidth(11);
            $spread->getActiveSheet()->getColumnDimension("G")->setWidth(11);
            $spread->getActiveSheet()->getColumnDimension("H")->setWidth(18);
            $spread->getActiveSheet()->getColumnDimension("I")->setWidth(11);
            $spread->getActiveSheet()->getColumnDimension("J")->setWidth(20);
            $spread->getActiveSheet()->getColumnDimension("K")->setWidth(11);
            $spread->getActiveSheet()->getColumnDimension("L")->setWidth(11);
            $spread->getActiveSheet()->getColumnDimension("M")->setWidth(20);
            $spread->getActiveSheet()->getColumnDimension("N")->setWidth(12);
            $spread->getActiveSheet()->getColumnDimension("O")->setWidth(12);

            $row = 3;
		    $nro = 1;
            //dd($data);
            if($data){
                foreach($data as $key){
                    $nletra = 0;
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $nro); //$key->idRec
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->codlocRec);
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->codmodRec); 
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->redRec);
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->insRec);
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->textModalidadRec);//modalidad
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->textNivelesRec);//nivRec
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->disRec); 
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->dniRec);
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->director);
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->carRec);
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->telRec); 
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->corRec);
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->fecha_reporte);

                    if($ficha->tipFic=='AL DIRECTIVO'){
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->especialista);
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->ddni);
                    }
                    
                    if($ficha->tipFic=='AL DIRECTIVO' or $ficha->tipFic=='DIRECTIVO AL DOCENTE'){
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->nroVisRec);
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->fecAplRec);
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->AsiTecRec);
                    
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->docente);
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->dniDoc);
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->graDoc);
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->secDoc);
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->areDoc);
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->nroEstRec);
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->nroEstPreRec);
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->nroEstAsiRec);
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->tipSerRec);
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->telDoc);
                    $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->corDoc);
                    }
                    
                    
                    
                    foreach($pregunta as $key1){
                        //$objPHPExcel->setActiveSheetIndex(0)->setCellValue($letras[$nletra++].$row,  $key1->texto);
                        if($key->detalle){
                        //Mostrar respuesta
                        $rescorrecta = false;
                        foreach($key->detalle as $key3){
                            if($key1->idPre==$key3->idPre){
                                $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row, $key3->resRdd);
                                $rescorrecta = $key3;
                            }
                        }
                        if(!$rescorrecta){ $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row, ''); }
                        //Mostrar respuesta
                        
                        //Mostrar varHtmlPre adicionales
                        
                        if($key1->varHtmlPre){
                                foreach( explode(',',$key1->varHtmlPre)  as $key2){
                                    //$objPHPExcel->setActiveSheetIndex(0)->setCellValue($letras[$nletra++].$row,  $key2);
                                    
                                    if($rescorrecta->adicional){
                                        //$spread->getActiveSheet()->setCellValue($letras[$nletra++].$row, 'AAA');
                                        //NO EXPORTA EN MONITOREO DE DIRECTOR A DOCENTE
                                        
                                        $adicionalcorrecta = false;
                                        foreach($rescorrecta->adicional as $key4){
                                            if($key4->varVaa==$key2){
                                                $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key4->valRaa);
                                                $adicionalcorrecta = $key4;
                                            }
                                        }
                                        if(!$adicionalcorrecta){ $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row, ''); }
                                        
                                        //NO EXPORTA EN MONITOREO DE DIRECTOR A DOCENTE
                                    }else{
                                        $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row, '');
                                    }
                                }
                            }
                        
                        
                        }else{
                                //Mostrar respuesta
                                $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row, '');
                                //Mostrar respuesta
                                //Mostrar respuestas adicionales
                                if($key1->var_titulo){
                                        foreach( explode(',',$key1->var_titulo)  as $key2){
                                            $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key2);
                                        }
                                    }
                                //Mostrar respuestas adicionales
                        }
                        }
                        
                        
                        
                        
                        $spread->getActiveSheet()->getStyle("J".$row)->getNumberFormat()->setFormatCode('#');
                    
                        if($ficha->tipFic=='AL DIRECTIVO' or $ficha->tipFic=='DIRECTIVO AL DOCENTE'){
                        $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->conRec);
                        $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->recRec);
                        $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->comDirRec);
                        $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->impDirRec);
                        $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->comDocRec);
                        $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->impDocRec);
                        $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->comEspRec);
                        $spread->getActiveSheet()->setCellValue($letras[$nletra++].$row,  $key->impEspRec);
                        }
                    $nro++;
                    $row++;
                    
                    
                }
            }
            
        $writer = new Xlsx($spread);
        # Le pasamos la ruta de guardado
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
        //echo memory_get_usage()/1024/1024;
    }


    public function get_respuestas_ficha($idficha,$tipFic='FICHA'){
        if($tipFic=='FICHA'){
            $result = DB::connection('ficha')->select("SELECT 
            R.*,
            CONCAT(IFNULL(R.nomRec,''),' ',IFNULL(R.apePatRec,''),' ',IFNULL(R.apeMatRec,'')) as director,
            IF(R.idNivRec IN(1),'ETP',IF(R.idNivRec IN(2),'EBE',IF(R.idNivRec IN(3,4,5),'EBR',IF(R.idNivRec IN(6,7),'EBA','')))) as modalidad,
            R.nroVisRec,
            DATE_FORMAT(R.creado_at,'%d/%m/%Y %H:%i:%s') as fecAplRec,
            DATE_FORMAT(R.updated_at,'%d/%m/%Y') as fecha_reporte,
            F.modFic,
            F.tipFic
            FROM fichas F 
            INNER JOIN receptores          R ON F.idFic    = R.idFic
            WHERE F.estFic=1 and R.estRec=1 and R.culRec=1 and F.idFic = $idficha");
        }else{
            $result = DB::connection('ficha')->select("SELECT 
            Rd.*,
            R.*,
            D.*,
            CONCAT(IFNULL(R.nomRec,''),' ',IFNULL(R.apePatRec,''),' ',IFNULL(R.apeMatRec,'')) as director,
            CONCAT(IFNULL(D.nomDoc,''),' ',IFNULL(D.apePatDoc,''),' ',IFNULL(D.apeMatDoc,'')) as docente,
            IF(R.idNivRec IN(1),'ETP',IF(R.idNivRec IN(2),'EBE',IF(R.idNivRec IN(3,4,5),'EBR',IF(R.idNivRec IN(6,7),'EBA','')))) as modalidad,
            CONCAT(E.esp_nombres,' ',E.esp_apellido_paterno,'',E.esp_apellido_materno) as especialista,
            E.ddni,
            R.nroVisRec,
            DATE_FORMAT(Rd.fecAplRec,'%d/%m/%Y %H:%i:%s') as fecAplRec,
            Rd.AsiTecRec,
            DATE_FORMAT(R.updated_at,'%d/%m/%Y') as fecha_reporte,
            F.modFic,
            F.tipFic
            FROM fichas F 
            INNER JOIN receptores          R ON F.idFic    = R.idFic
            LEFT  JOIN receptores_deta Rd ON R.idRec=Rd.idRec
            LEFT  JOIN docente         D  ON R.idDoc = D.idDoc
            LEFT  JOIN siic01ugel01gob_directores.especialistas E ON R.idEspRec = E.idespecialista 
            WHERE F.estFic=1 and R.estRec=1 and R.culRec=1 and F.idFic = $idficha");
        }
        
        if ($result) {
            for ($i=0; $i < count($result); $i++) {
                $result[$i]->detalle = $this->ver_ficha_respondida($idficha,$result[$i]->idRec);
            }
            return $result;
        }else{
            return false;
        }
    }

    public function ver_ficha_respondida($idficha,$idreceptor,$grupo=false){
        $where = ($grupo)?"and P.gruPre='$grupo'":"";
        $result = DB::connection('ficha')->select("SELECT *,P.idPre FROM preguntas P 
        LEFT JOIN respuestas_detalles D ON P.idPre = D.idPre and D.estRdd=1 and D.idRec = $idreceptor
        WHERE P.estPre=1 $where and P.idFic = $idficha 
        ORDER BY P.ordPre ASC");
        if($result){
            for ($i=0; $i < count($result); $i++) {
                if($result[$i]->varHtmlPre and $result[$i]->idRdd){
                    $result[$i]->adicional = DB::connection('ficha')->select("SELECT*FROM respuestas_adicionales WHERE estRaa=1 and idRdd=".$result[$i]->idRdd." and varVaa IN('".str_replace(",","','",$result[$i]->varHtmlPre)."')");
               }else{
                    $result[$i]->adicional = false;
               }
            }
            return $result;
        }else{
            return false;
        }
    }
    
    public function excel_registro_matricula_modulos_cetpro(Request $request){
        $where  = '';
        //$where .= ($request['codmod'])?" and P.codModPee = '".$request['codmod']."' ":'';
        //$where .= ($request['perOff'])?" and O.perOff = '".$request['perOff']."' ":'';
        $where .= ($request['codmod'])?" and rie.codmod = '".$request['codmod']."' ":'';
        $where .= ($request['anio'])?" and oo.periodo = '".$request['anio']."' ":'';
        
        
        date_default_timezone_set('America/Lima');
        $spread = new Spreadsheet();
        $sheet = $spread->getActiveSheet();
        $filename=substr('matricula',0,20); //Guardar nuestro libro de trabajo como este nombre de archivo
        $sheet->setTitle($filename);
        
        $spread->getActiveSheet()->setCellValue('A1', 'REGISTRO  DE  MATRÍCULA  INSTITUCIONAL '.(($request['perOff'])?$request['perOff']:''));
        $spread->getActiveSheet()->setCellValue('A2', 'EDUCACIÓN  TÉCNICO-PRODUCTIVA');
        $spread->getActiveSheet()->setCellValue('A4', 'REGIÓN');
        $spread->getActiveSheet()->setCellValue('B4', 'UGEL');
        $spread->getActiveSheet()->setCellValue('C4', 'CÓDIGO MODULAR');
        $spread->getActiveSheet()->setCellValue('D4', 'NOMBRE DEL CETPRO');
        $spread->getActiveSheet()->setCellValue('E4', 'OPCIÓN OCUPACIONAL');
        $spread->getActiveSheet()->setCellValue('F4', 'CICLO (BÁSICO/AUXILIAR TÉCNICO) (MEDIO/TÉCNICO)');
        $spread->getActiveSheet()->setCellValue('G4', 'N° DE RESOLUCIÓN QUE AUTORIZA LA OPCION OCUPACIONAL');
        $spread->getActiveSheet()->setCellValue('H4', 'MÓDULO  DE LA OPCIÓN OCUPACIONAL');
        $spread->getActiveSheet()->setCellValue('I4', 'TIPO DE DOCUMENTO');
        $spread->getActiveSheet()->setCellValue('J4', 'N° DNI');
        $spread->getActiveSheet()->setCellValue('K4', 'APELLIDO PATERNO');
        $spread->getActiveSheet()->setCellValue('L4', 'APELLIDO MATERNO');
        $spread->getActiveSheet()->setCellValue('M4', 'NOMBRES');
        $spread->getActiveSheet()->setCellValue('N4', 'SEXO');
        $spread->getActiveSheet()->setCellValue('O4', 'FECHA DE NACIMIENTO');

        $spread->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $spread->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $spread->getActiveSheet()->getColumnDimension('C')->setWidth(11);
        $spread->getActiveSheet()->getColumnDimension('D')->setWidth(35);
        $spread->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $spread->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $spread->getActiveSheet()->getColumnDimension('G')->setWidth(25);
        $spread->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $spread->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $spread->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $spread->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $spread->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $spread->getActiveSheet()->getColumnDimension('M')->setWidth(20);
        $spread->getActiveSheet()->getColumnDimension('N')->setWidth(20);
        $spread->getActiveSheet()->getColumnDimension('O')->setWidth(20);

        $data = DB::connection('cetpromin')->select("SELECT rie.codmod,rie.codlocal,rie.institucion,oo.periodo,oo.rd_aprobacion,oo.opcion_ocupacional,oo.nivel_formativo,mo.descripcion,a.docAlu,a.nomAlu,a.apePatAlu,a.apeMatAlu,a.sexAlu,a.fecNacAlu FROM `opcion_ocupacionals` oo
        JOIN siic01ugel01gob_directores.iiee_a_evaluar_RIE as rie on rie.codmod=oo.codigo_modular
        JOIN modulo_ocupacionals mo on mo.opcion_ocupacional_id=oo.id
        JOIN modulo_ofertados mof on mof.modulo_ocupacional_id=mo.id
        JOIN matricula_modulos mm on mm.modulo_ofertado_id=mof.id
        JOIN alumnos a on a.idAlu=mm.alumno_id
        where oo.estado=1 and mo.estado=1 and mm.estado=1 and mof.estado=1 and a.estAlu=1 $where
                ");
        
        $nro = 5;
        
        if($data){
        for ($i=0; $i < count($data); $i++) {
            $key = $data[$i];
            $spread->getActiveSheet()->setCellValue('A'.$nro, 'LIMA');
            $spread->getActiveSheet()->setCellValue('B'.$nro, 'UGEL 01');
            $spread->getActiveSheet()->setCellValue('C'.$nro, $key->codmod);
            $spread->getActiveSheet()->setCellValue('D'.$nro, $key->institucion);
            $spread->getActiveSheet()->setCellValue('E'.$nro, $key->opcion_ocupacional);
            $spread->getActiveSheet()->setCellValue('F'.$nro, $key->nivel_formativo);
            $spread->getActiveSheet()->setCellValue('G'.$nro, $key->rd_aprobacion);
            $spread->getActiveSheet()->setCellValue('H'.$nro, $key->descripcion);
            $spread->getActiveSheet()->setCellValue('I'.$nro, 'DNI');
            $spread->getActiveSheet()->setCellValue('J'.$nro, $key->docAlu);
            $spread->getActiveSheet()->setCellValue('K'.$nro, $key->apePatAlu);
            $spread->getActiveSheet()->setCellValue('L'.$nro, $key->apeMatAlu);
            $spread->getActiveSheet()->setCellValue('M'.$nro, $key->nomAlu);
            $spread->getActiveSheet()->setCellValue('N'.$nro, $key->sexAlu);
            $spread->getActiveSheet()->setCellValue('O'.$nro, $key->fecNacAlu);
            $nro++;
        }
        }else{
            $spread->getActiveSheet()->setCellValue('A'.$nro, 'NO SE HA ENCONTRADO INFORMACIÓN PARA MOSTRAR');
        }
        
        $spread->getActiveSheet()->getStyle('A1:A2')->getFont()->setSize(15);
        $spread->getActiveSheet()->getStyle('A1:A2')->getFont()->setBold(true);
        $spread->getActiveSheet()->getStyle('A4:O4')->getFont()->setBold(true);
        $spread->getActiveSheet()->mergeCells("A1:O1");
        $spread->getActiveSheet()->mergeCells("A2:O2");

        $writer = new Xlsx($spread);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode("registro_matricula_cetpro.xlsx").'"');
        $writer->save('php://output');

    }
    
    public function excel_registro_matricula_cetpro(Request $request){
        $where  = '';
        $where .= ($request['codmod'])?" and P.codModPee = '".$request['codmod']."' ":'';
        $where .= ($request['perOff'])?" and O.perOff = '".$request['perOff']."' ":'';
        $where .= ($request['anio'])?" and SUBSTRING(O.perOff,1,4) = '".$request['anio']."' ":'';
        
        
        date_default_timezone_set('America/Lima');
        $spread = new Spreadsheet();
        $sheet = $spread->getActiveSheet();
        $filename=substr('matricula',0,20); //Guardar nuestro libro de trabajo como este nombre de archivo
        $sheet->setTitle($filename);
        
        $spread->getActiveSheet()->setCellValue('A1', 'REGISTRO  DE  MATRÍCULA  INSTITUCIONAL '.(($request['perOff'])?$request['perOff']:''));
        $spread->getActiveSheet()->setCellValue('A2', 'EDUCACIÓN  TÉCNICO-PRODUCTIVA');
        $spread->getActiveSheet()->setCellValue('A4', 'REGIÓN');
        $spread->getActiveSheet()->setCellValue('B4', 'UGEL');
        $spread->getActiveSheet()->setCellValue('C4', 'CÓDIGO MODULAR');
        $spread->getActiveSheet()->setCellValue('D4', 'NOMBRE DEL CETPRO');
        $spread->getActiveSheet()->setCellValue('E4', 'PROGRAMA DE ESTUDIOS O OPCIÓN OCUPACIONAL O ESPECIALIDAD');
        $spread->getActiveSheet()->setCellValue('F4', 'CICLO (BÁSICO/AUXILIAR TÉCNICO) (MEDIO/TÉCNICO)');
        $spread->getActiveSheet()->setCellValue('G4', 'N° DE RESOLUCIÓN QUE AUTORIZA EL PROGRAMA DE ESTUDIO (ADJUNTAR RESOLUCIÓN)');
        $spread->getActiveSheet()->setCellValue('H4', 'MÓDULO  DEL PROGRAMA DE ESTUDIOS QUE SE DESARROLLAN');
        $spread->getActiveSheet()->setCellValue('I4', 'TIPO DE DOCUMENTO');
        $spread->getActiveSheet()->setCellValue('J4', 'N° DNI');
        $spread->getActiveSheet()->setCellValue('K4', 'APELLIDO PATERNO');
        $spread->getActiveSheet()->setCellValue('L4', 'APELLIDO MATERNO');
        $spread->getActiveSheet()->setCellValue('M4', 'NOMBRES');
        $spread->getActiveSheet()->setCellValue('N4', 'SEXO');
        $spread->getActiveSheet()->setCellValue('O4', 'FECHA DE NACIMIENTO');

        $spread->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $spread->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $spread->getActiveSheet()->getColumnDimension('C')->setWidth(11);
        $spread->getActiveSheet()->getColumnDimension('D')->setWidth(35);
        $spread->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $spread->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $spread->getActiveSheet()->getColumnDimension('G')->setWidth(25);
        $spread->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $spread->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $spread->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $spread->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $spread->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $spread->getActiveSheet()->getColumnDimension('M')->setWidth(20);
        $spread->getActiveSheet()->getColumnDimension('N')->setWidth(20);
        $spread->getActiveSheet()->getColumnDimension('O')->setWidth(20);

        $data = DB::connection('cetpromin')->select("SELECT
                RIE.codmod,
                RIE.institucion,
                P.proEstPee,
                P.nivForPee,
                P.rdPee,
                MO.modMod,
                A.tipDocAlu,
                A.docAlu,
                UPPER(A.apePatAlu) as apePatAlu,
                UPPER(A.apeMatAlu) as apeMatAlu,
                UPPER(A.nomAlu)    as nomAlu,
                A.sexAlu,
                A.fecNacAlu
                FROM alumnos A 
                INNER JOIN matriculas M  ON A.idAlu = M.idAlu
                INNER JOIN ofertas_formativas O ON M.idOff = O.idOff
                INNER JOIN modulos MO ON O.idMod = MO.idMod
                INNER JOIN programas_estudio P ON O.idPro=P.idPro
                INNER JOIN iiee_a_evaluar_RIE RIE ON P.codModPee  = RIE.codmod
                WHERE M.estMat = 1 $where
                GROUP BY 
                RIE.codmod,
                RIE.institucion,
                P.proEstPee,
                P.nivForPee,
                P.rdPee,
                MO.modMod,
                A.tipDocAlu,
                A.docAlu,
                A.apePatAlu,
                A.apeMatAlu,
                A.nomAlu,
                A.sexAlu,
                A.fecNacAlu
                ");
        
        $nro = 5;
        
        if($data){
        for ($i=0; $i < count($data); $i++) {
            $key = $data[$i];
            $spread->getActiveSheet()->setCellValue('A'.$nro, 'LIMA');
            $spread->getActiveSheet()->setCellValue('B'.$nro, 'UGEL 01');
            $spread->getActiveSheet()->setCellValue('C'.$nro, $key->codmod);
            $spread->getActiveSheet()->setCellValue('D'.$nro, $key->institucion);
            $spread->getActiveSheet()->setCellValue('E'.$nro, $key->proEstPee);
            $spread->getActiveSheet()->setCellValue('F'.$nro, $key->nivForPee);
            $spread->getActiveSheet()->setCellValue('G'.$nro, $key->rdPee);
            $spread->getActiveSheet()->setCellValue('H'.$nro, $key->modMod);
            $spread->getActiveSheet()->setCellValue('I'.$nro, $key->tipDocAlu);
            $spread->getActiveSheet()->setCellValue('J'.$nro, $key->docAlu);
            $spread->getActiveSheet()->setCellValue('K'.$nro, $key->apePatAlu);
            $spread->getActiveSheet()->setCellValue('L'.$nro, $key->apeMatAlu);
            $spread->getActiveSheet()->setCellValue('M'.$nro, $key->nomAlu);
            $spread->getActiveSheet()->setCellValue('N'.$nro, $key->sexAlu);
            $spread->getActiveSheet()->setCellValue('O'.$nro, $key->fecNacAlu);
            $nro++;
        }
        }else{
            $spread->getActiveSheet()->setCellValue('A'.$nro, 'NO SE HA ENCONTRADO INFORMACIÓN PARA MOSTRAR');
        }
        
        $spread->getActiveSheet()->getStyle('A1:A2')->getFont()->setSize(15);
        $spread->getActiveSheet()->getStyle('A1:A2')->getFont()->setBold(true);
        $spread->getActiveSheet()->getStyle('A4:O4')->getFont()->setBold(true);
        $spread->getActiveSheet()->mergeCells("A1:O1");
        $spread->getActiveSheet()->mergeCells("A2:O2");

        $writer = new Xlsx($spread);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode("registro_matricula_cetpro.xlsx").'"');
        $writer->save('php://output');

    }
    
    
    public function excel_registro_titulados_cetpro(Request $request){
        $where  = '';
        //$where .= ($request['codmod'])?" and P.codModPee = '".$request['codmod']."' ":'';
        //$where .= ($request['perOff'])?" and O.perOff = '".$request['perOff']."' ":'';
        //$where .= ($request['anio'])?" and SUBSTRING(O.perOff,1,4) = '".$request['anio']."' ":'';
        $anio = $request['anio'];
        
        
        date_default_timezone_set('America/Lima');
        $spread = new Spreadsheet();
        $sheet = $spread->getActiveSheet();
        $filename=substr('matricula',0,20); //Guardar nuestro libro de trabajo como este nombre de archivo
        $sheet->setTitle($filename);
        
        $spread->getActiveSheet()->setCellValue('A1', 'REGISTRO  DE  TITULADOS  INSTITUCIONAL '.(($request['perOff'])?$request['perOff']:''));
        $spread->getActiveSheet()->setCellValue('A2', 'EDUCACIÓN  TÉCNICO-PRODUCTIVA');
        $spread->getActiveSheet()->setCellValue('A4', 'N°');
        
        $spread->getActiveSheet()->setCellValue('B4', 'CODIGO MODULAR');
        $spread->getActiveSheet()->setCellValue('C4', 'CODIGO LOCAL');
        $spread->getActiveSheet()->setCellValue('D4', 'CETPRO');
        
        $spread->getActiveSheet()->setCellValue('E4', 'TIPO DOCUMENTO');
        $spread->getActiveSheet()->setCellValue('F4', 'NÚMERO');
        $spread->getActiveSheet()->setCellValue('G4', 'APELLIDOS Y NOMBRES');
        $spread->getActiveSheet()->setCellValue('H4', 'SEXO');
        $spread->getActiveSheet()->setCellValue('I4', 'FECHA DE NACIMIENTO');
        $spread->getActiveSheet()->setCellValue('J4', 'PROGRAMA DE ESTUDIOS');
        $spread->getActiveSheet()->setCellValue('K4', 'NUMERO TOTAL DE CRÉDITOS');
        $spread->getActiveSheet()->setCellValue('L4', 'NUMERO TOTAL DE MÓDULOS');
        $spread->getActiveSheet()->setCellValue('M4', 'MODALIDAD DEL SERVICIO');
        $spread->getActiveSheet()->setCellValue('N4', 'FECHA DE EGRESO');
        $spread->getActiveSheet()->setCellValue('O4', 'CODIGO DE REGISTRO INSTITUCIONAL DEL TÍTULO EN EL CETPRO');
        $spread->getActiveSheet()->setCellValue('P4', 'N° DE RESOLUCIÓN DIRECTORAL DE EXPEDICIÓN DEL TÍTULO EN EL CETPRO');
        $spread->getActiveSheet()->setCellValue('Q4', 'CÓDIGO DE REGISTRO INSTITUCIONAL DEL TITULO EN LA UGEL');
    
        $spread->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $spread->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $spread->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $spread->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $spread->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $spread->getActiveSheet()->getColumnDimension('F')->setWidth(11);
        $spread->getActiveSheet()->getColumnDimension('G')->setWidth(35);
        $spread->getActiveSheet()->getColumnDimension('H')->setWidth(25);
        $spread->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $spread->getActiveSheet()->getColumnDimension('J')->setWidth(25);
        $spread->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $spread->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $spread->getActiveSheet()->getColumnDimension('M')->setWidth(20);
        $spread->getActiveSheet()->getColumnDimension('N')->setWidth(20);
        $spread->getActiveSheet()->getColumnDimension('O')->setWidth(20);
        $spread->getActiveSheet()->getColumnDimension('P')->setWidth(20);
        $spread->getActiveSheet()->getColumnDimension('Q')->setWidth(20);

        $data = DB::connection('cetpromin')->select("SELECT
        RIE.codmod,
        RIE.codlocal,
        RIE.institucion,
        A.tipDocAlu,
        A.docAlu,
        CONCAT(A.apePatAlu,' ',A.apeMatAlu,', ',A.nomAlu) as estudiante,
        A.sexAlu,
        DATE_FORMAT(A.fecNacAlu,'%d/%m/%Y') as fecNacAlu,
        P.proEstPee,
        P.credPee,
        T.cantModTit,
        P.tipSerEduPee,
        DATE_FORMAT(T.fecEgrTit,'%d/%m/%Y') as fecEgrTit,
        T.codRegIeTit,
        T.rdExpTit,
        T.codRegUgelTit
        FROM titulos T
        INNER JOIN alumnos            A ON T.idAlu=A.idAlu
        INNER JOIN programas_estudio  P ON T.idPro=P.idPro
        INNER JOIN iiee_a_evaluar_RIE RIE ON P.codModPee = RIE.codmod 
        WHERE T.estTit=1 and T.rdExpTit <>'' and YEAR(T.fecEgrTit)='$anio'");
        
        $nro = 5;
        
        if($data){
        for ($i=0; $i < count($data); $i++) {
            $key = $data[$i];
            $spread->getActiveSheet()->setCellValue('A'.$nro, $i+1);
            $spread->getActiveSheet()->setCellValue('B'.$nro,  $key->codmod);
            $spread->getActiveSheet()->setCellValue('C'.$nro,  $key->codlocal);
            $spread->getActiveSheet()->setCellValue('D'.$nro,  $key->institucion);
            $spread->getActiveSheet()->setCellValue('E'.$nro,  $key->tipDocAlu);
            $spread->getActiveSheet()->setCellValue('F'.$nro, $key->docAlu);
            $spread->getActiveSheet()->setCellValue('G'.$nro, $key->estudiante);
            $spread->getActiveSheet()->setCellValue('H'.$nro, $key->sexAlu);
            $spread->getActiveSheet()->setCellValue('I'.$nro, $key->fecNacAlu);
            $spread->getActiveSheet()->setCellValue('J'.$nro, $key->proEstPee);
            $spread->getActiveSheet()->setCellValue('K'.$nro, $key->credPee);
            $spread->getActiveSheet()->setCellValue('L'.$nro, $key->cantModTit);
            $spread->getActiveSheet()->setCellValue('M'.$nro, $key->tipSerEduPee);
            $spread->getActiveSheet()->setCellValue('N'.$nro, $key->fecEgrTit);
            $spread->getActiveSheet()->setCellValue('O'.$nro, $key->codRegIeTit);
            $spread->getActiveSheet()->setCellValue('P'.$nro, $key->rdExpTit);
            $spread->getActiveSheet()->setCellValue('Q'.$nro, $key->codRegUgelTit);
            $nro++;
        }
        }else{
            $spread->getActiveSheet()->setCellValue('A'.$nro, 'NO SE HA ENCONTRADO INFORMACIÓN PARA MOSTRAR');
        }
        
        $spread->getActiveSheet()->getStyle('A1:A2')->getFont()->setSize(15);
        $spread->getActiveSheet()->getStyle('A1:A2')->getFont()->setBold(true);
        $spread->getActiveSheet()->getStyle('A4:Q4')->getFont()->setBold(true);
        $spread->getActiveSheet()->mergeCells("A1:Q1");
        $spread->getActiveSheet()->mergeCells("A2:Q2");

        $writer = new Xlsx($spread);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode("registro_titulados_cetpro.xlsx").'"');
        $writer->save('php://output');

    }
    
    
    
    
    
    public function excel_epps(){
        date_default_timezone_set('America/Lima');
        $fileName="EPPS.xlsx";   
        $spread = new Spreadsheet();
        $sheet = $spread->getActiveSheet();
        $sheet->setTitle("DIRECTORES");
        $letra = $this->letras;
        $cabeza = array('ENTIDAD', 'PROVINCIA', 'DISTRITO', 'CÓDIGO DE LOCAL', 'CÓDIGO MODULAR', 'NOMBRE DE LA INSTITUCIÓN EDUCATIVA', 'DIRECCIÓN', 'NIVEL EDUCATIVO CDD', 'CODIGO DE PLAZA', 'SUB-TIPO DE TRABAJADOR', 'CARGO', 'SITUACION LABORAL', 'APELLIDOS Y NOMBRES', 'DNI', 'REGIMEN LABORAL', 'GRUPO OCUPACIONAL', 'LENTES DE SEGURIDAD', 'MASCARILLA DE MEDIA CARA DE PLASTICO', 'FILTROS DE CARBON ACTIVADO', 'GUANTE DE JEBE DE USO INDUSTRIAL CALIBRE 25', 'GUANTE DE BADANA', 'GUANTE DE JEBE DE USO INDUSTRIAL CALIBRE 10 CAÑA LARGA', 'GUANTE MULTIFLEX', 'CASCO PROTECTOR DE PLASTICO CON OREJERAS', 'BARBIQUEJO', 'ZAPATO DIELECTRICO', 'TALLA ZAPATO DIELETRICO', 'UNIFORME DRIL (PANTALON y CAMISA)', 'TALLA DE PANTALON DRIL', 'TALLA DE CAMISA DRIL', 'CHOMPA', 'TALLA DE CHOMPA', 'BLOQUEADOR SOLAR', 'BOTIN DE CUERO CON PUNTA DE ACERO UNISEX ', 'TALLA BOTIN DE CUERO', 'ANALISIS', 'ESTADO');
        for ($i=0; $i < count($cabeza); $i++) {
            $sheet->setCellValue($letra[$i]."1", $cabeza[$i]);
            $sheet->getColumnDimension($letra[$i])->setAutoSize(true);
        }
        
        $data = Form_epps::select("*")->get()->toArray();
        for ($i=0; $i < count($data); $i++) {
        $nro = $i+2;
        $key = $data[$i];
        $j=0;
        
        $sheet->setCellValue($letra[$j++].$nro, $key['entidad']);
        $sheet->setCellValue($letra[$j++].$nro, $key['provincia']);
        $sheet->setCellValue($letra[$j++].$nro, $key['distrito']);
        $sheet->setCellValue($letra[$j++].$nro, $key['codlocal']);
        $sheet->setCellValue($letra[$j++].$nro, $key['codmod']);
        $sheet->setCellValue($letra[$j++].$nro, $key['institucion']);
        $sheet->setCellValue($letra[$j++].$nro, $key['direccion']);
        $sheet->setCellValue($letra[$j++].$nro, $key['nivel']);
        $sheet->setCellValue($letra[$j++].$nro, $key['codplaza']);
        $sheet->setCellValue($letra[$j++].$nro, $key['sub_tipo_trabajador']);
        $sheet->setCellValue($letra[$j++].$nro, $key['cargo']);
        $sheet->setCellValue($letra[$j++].$nro, $key['situacion_laboral']);
        $sheet->setCellValue($letra[$j++].$nro, $key['apellidos_y_nombres']);
        $sheet->setCellValue($letra[$j++].$nro, $key['dni']);
        $sheet->setCellValue($letra[$j++].$nro, $key['regimen_laboral']);
        $sheet->setCellValue($letra[$j++].$nro, $key['grupo_ocupacional']);
        $sheet->setCellValue($letra[$j++].$nro, $key['lentes']);
        $sheet->setCellValue($letra[$j++].$nro, $key['mascarilla']);
        $sheet->setCellValue($letra[$j++].$nro, $key['filtro']);
        $sheet->setCellValue($letra[$j++].$nro, $key['guantes_jebe']);
        $sheet->setCellValue($letra[$j++].$nro, $key['guantes_banda']);
        $sheet->setCellValue($letra[$j++].$nro, $key['guantes_jebe_industrial']);
        $sheet->setCellValue($letra[$j++].$nro, $key['guantes_multiflex']);
        $sheet->setCellValue($letra[$j++].$nro, $key['casco']);
        $sheet->setCellValue($letra[$j++].$nro, $key['barbiquero']);
        $sheet->setCellValue($letra[$j++].$nro, $key['zapato']);
        $sheet->setCellValue($letra[$j++].$nro, $key['zapato_talla']);
        $sheet->setCellValue($letra[$j++].$nro, $key['uniforme']);
        $sheet->setCellValue($letra[$j++].$nro, $key['pantalon_talla']);
        $sheet->setCellValue($letra[$j++].$nro, $key['camisa_talla']);
        $sheet->setCellValue($letra[$j++].$nro, $key['chompa']);
        $sheet->setCellValue($letra[$j++].$nro, $key['chompa_talla']);
        $sheet->setCellValue($letra[$j++].$nro, $key['bloqueador_solar']);
        $sheet->setCellValue($letra[$j++].$nro, $key['botin']);
        $sheet->setCellValue($letra[$j++].$nro, $key['botin_talla']);
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
    
    
    public function excel_equipos_informaticos(Request $request){
        $idtipo   = $request['idtipo'];
        date_default_timezone_set('America/Lima');
        $fileName="Equipos Informativos.xlsx";
        $spread = new Spreadsheet();
        $sheet = $spread->getActiveSheet();
        $sheet->setTitle("DIRECTORES");
        $letra = $this->letras;
        $cabeza = array('codlocal', 'institucion', 'modalidad', 'distrito', 'red', 'codigo_sbn', 'denominacion', 'marca', 'modelo', 'color', 'serie', 'observacion', 'director', 'Fecha de registro','Fecha de actualización');
        for ($i=0; $i < count($cabeza); $i++) {
            $sheet->setCellValue($letra[$i]."1", $cabeza[$i]);
            $sheet->getColumnDimension($letra[$i])->setAutoSize(true);
        }
        
        $data = DB::select( "SELECT 
        F.idInf,
        R.codlocal,
        R.institucion,
        R.modalidad,
        R.distrito,
        R.red,
        F.codigo_sbn,
        F.denominacion,
        F.marca,
        F.modelo,
        F.color,
        F.serie,
        F.observacion,
        CONCAT(C.nombres,' ',C.apellipat,' ',C.apellimat) as director,
        DATE_FORMAT(F.creado_at,'%Y/%m/%d %H:%i:%s ') as creado_at,
        DATE_FORMAT(F.updated_at,'%Y/%m/%d %H:%i:%s ') as updated_at
        FROM iiee_a_evaluar_RIE R 
        INNER JOIN siic01_formularios.form_equipos_informaticos F ON R.codlocal=F.codlocal
        INNER JOIN contacto C ON F.id_contacto = C.id_contacto
        WHERE F.estado=1 and F.idtipo = $idtipo
        GROUP BY 
        F.idInf,
        R.codlocal,
        R.institucion,
        R.modalidad,
        R.distrito,
        R.red,
        F.codigo_sbn,
        F.denominacion,
        F.marca,
        F.modelo,
        F.color,
        F.serie,
        F.observacion,
        C.nombres,
        C.apellipat,
        C.apellimat,
        F.creado_at,
        F.updated_at
        ");
        
        for ($i=0; $i < count($data); $i++) {
        $nro = $i+2;
        $key = $data[$i];
        $j=0;
        $sheet->setCellValue($letra[$j++].$nro, $key->codlocal);
        $sheet->setCellValue($letra[$j++].$nro, $key->institucion);
        $sheet->setCellValue($letra[$j++].$nro, $key->modalidad);
        $sheet->setCellValue($letra[$j++].$nro, $key->distrito);
        $sheet->setCellValue($letra[$j++].$nro, $key->red);
        $sheet->setCellValue($letra[$j++].$nro, $key->codigo_sbn);
        $sheet->setCellValue($letra[$j++].$nro, $key->denominacion);
        $sheet->setCellValue($letra[$j++].$nro, $key->marca);
        $sheet->setCellValue($letra[$j++].$nro, $key->modelo);
        $sheet->setCellValue($letra[$j++].$nro, $key->color);
        $sheet->setCellValue($letra[$j++].$nro, $key->serie);
        $sheet->setCellValue($letra[$j++].$nro, $key->observacion);
        $sheet->setCellValue($letra[$j++].$nro, $key->director);
        $sheet->setCellValue($letra[$j++].$nro, $key->creado_at);
        $sheet->setCellValue($letra[$j++].$nro, $key->updated_at);
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
    
        public function excel_reportecorreos(Request $request){
        $flg   = $request['flg'];
        date_default_timezone_set('America/Lima');
        $fileName="REPORTE_CORREOS_$flg.xlsx";
        $spread = new Spreadsheet();
        $sheet = $spread->getActiveSheet();
        $sheet->setTitle($flg);
        $letra = $this->letras;
        $cabeza = array('N°','Fecha de solicitud', 'Correo Institucional', 'Situación', 'correo personal', 'director', 'institucion', 'distrito', 'codigo local','Fecha de atención');
        for ($i=0; $i < count($cabeza); $i++) {
            $sheet->setCellValue($letra[$i]."1", $cabeza[$i]);
            $sheet->getColumnDimension($letra[$i])->setAutoSize(true);
        }
        
        $data = DB::select("SELECT 
                R.*,
                DATE_FORMAT(R.creado_at,'%d/%m/%Y %H:%i:%s') as creado_at,
                DATE_FORMAT(R.updated_at,'%d/%m/%Y %H:%i:%s') as updated_at,
                I.correo_inst,
                I.correo,
                I.codlocal,
                CONCAT(IFNULL(C.nombres,''),' ',IFNULL(C.apellipat,''),' ',IFNULL(C.apellimat,'')) as director,
                I.institucion,
                I.distrito,
                I.correo_pass_inst
                FROM restablecer_correo_director R 
                INNER JOIN contacto C ON R.id_contacto=C.id_contacto
                INNER JOIN iiee_a_evaluar_RIE I ON R.codmod=I.codmod
                WHERE R.estRes=1 and R.flg='$flg'");
        
        for ($i=0; $i < count($data); $i++) {
        $nro = $i+2;
        $key = $data[$i];
        $j=0;
        $sheet->setCellValue($letra[$j++].$nro, $i+1);
        $sheet->setCellValue($letra[$j++].$nro, $key->creado_at);
        $sheet->setCellValue($letra[$j++].$nro, $key->correo_inst);
        $sheet->setCellValue($letra[$j++].$nro, $key->flg);
        $sheet->setCellValue($letra[$j++].$nro, $key->correo);
        $sheet->setCellValue($letra[$j++].$nro, $key->director);
        $sheet->setCellValue($letra[$j++].$nro, $key->institucion);
        $sheet->setCellValue($letra[$j++].$nro, $key->distrito);
        $sheet->setCellValue($letra[$j++].$nro, $key->codlocal);
        $sheet->setCellValue($letra[$j++].$nro, $key->updated_at);
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
    
    
}