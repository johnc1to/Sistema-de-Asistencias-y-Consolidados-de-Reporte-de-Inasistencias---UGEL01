<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\Models\Nexus_excepcional;
use App\Models\Nexus;
use App\Models\Receptor;
use App\Models\Iiee_a_evaluar_rie;
use DB;//Conexion a BD 
use Storage;
use Illuminate\Support\Facades\Session;

class RegistroPagos extends Controller
{
    public function listar_registro_pagos(Request $request){
        $director = session('siic01');
        //dd($director);
        if (!$director || empty($director['conf_permisos'][0]['codlocal'])) {
            return redirect()->back()->with('error', 'No se encontró la sesión del director o el codlocal.');
        }

        $codlocal = $director['conf_permisos'][0]['codlocal'];

        $institucion = Iiee_a_evaluar_rie::join('conf_permisos as P', 'P.esc_codmod', '=', 'iiee_a_evaluar_rie.codmod')
            ->join('contacto as C', 'C.id_contacto', '=', 'P.id_contacto')
            ->where('C.dni', $director['dni'])
            ->where('C.estado', 1)
            ->where('C.flg', 1)
            ->where('P.estado', 1)
            ->where('iiee_a_evaluar_rie.estado', 1)
            ->select('iiee_a_evaluar_rie.*')
            ->first();

        if ($institucion && empty($institucion->dni_director)) {
            $institucion->dni_director = $director['dni'];
            $institucion->save();
        }

        // Ahora ya consultamos con dni seguro
        $institucion = Iiee_a_evaluar_rie::select('idmodalidad','modalidad', 'institucion', 'direccion_ie', 'distrito')
            ->where('codlocal', $codlocal)
            ->where('dni_director', $director['dni'])
            ->first();

        // Obtener director UGEL 01
        $directorUgel = DB::table('nexus')
            ->select('nombres', 'apellipat', 'apellimat')
            ->where('descargo', 'DIRECTOR DE UNIDAD DE GESTIÓN EDUCATIVA LOCAL')
            ->where('nexus.estado', 1)
            ->first();

        // Obtener localidad 
        $localidad= DB::table('escale')
            ->select('localidad')
            ->where('codlocal', $codlocal)
            ->value('localidad');

        // Obtener nombre_anio 
        $anioActual = now()->year;

        $nombreAnio = DB::table('nombre_anio')
            ->where('anio',$anioActual)
            ->value('nombre');

        // Obtener resolucion
        $resolucion = Iiee_a_evaluar_rie::select('nro_rdcreacion')
            ->where('codlocal', $codlocal)
            ->value('nro_rdcreacion');

        //Obtener codmods
        $codmodulares = Iiee_a_evaluar_rie::select('nivel', 'codmod')
            ->where('codlocal', $codlocal)
            ->get();
        
        //correo institucional
        $correo_inst = Iiee_a_evaluar_rie::select('iiee_a_evaluar_rie')
            ->select('correo_inst')
            ->where('codlocal', $codlocal)
            ->value('correo_inst');

        // Obtener logo 
        $logo= Iiee_a_evaluar_rie::select('logo')
            ->where('codlocal', $codlocal)
            ->value('logo');

        $logoBD = Iiee_a_evaluar_rie::where('codlocal', $codlocal)
            ->value('logo'); // Esto puede ser null o un string

        $nombreLogo = $logoBD ? basename($logoBD) : null;
        $rutaLogoWeb = $nombreLogo ? 'storage/logoie/' . $nombreLogo : null;

        //Obtener turno 
        $d_cod_tur= Iiee_a_evaluar_rie::select('turno')
            ->where('codlocal', $codlocal)
            ->value('turno');  
        
        //Obtener firma
        $firmaGuardada = DB::connection('siic_anexos')->table('anexo03')
            ->where('id_contacto', $director['id_contacto'])
            ->value('firma');

        //Obtener oficio
        $oficioguardado = DB::connection('siic_anexos')->table('anexo03')
            ->where('id_contacto', $director['id_contacto'])
            ->value('oficio');
        
        //Obtener expediente
        $expedienteguardado = DB::connection('siic_anexos')->table('anexo03')
            ->where('id_contacto', $director['id_contacto'])
            ->value('expediente');

        //Lista de docentes desde nexus (para control de acceso)
        if (!$institucion) {
            return redirect()->back()->with('error', 'No se encontró institución para este director.');
        }

        //  Aquí ya tenemos idmodalidad de frente
        $idModalidad = $institucion->idmodalidad;

        //  Ahora ya tenemos el idmodalidad de la institución directamente
        $idnivelesModalidad = DB::table('niveles')
            ->where('Idmodalidad', $idModalidad)
            ->pluck('idnivel');
        //dd($idnivelesModalidad);
        $personalNexus = DB::table('nexus')
            ->select(
                'nexus.numdocum as dni',
                DB::raw("CONCAT(nexus.apellipat, ' ', nexus.apellimat, ', ', nexus.nombres) as nombres"),
                'nexus.descargo as cargo',
                'nexus.situacion as condicion',
                'nexus.jornlab as jornada',
                'nexus.descniveduc as nivel',
                'nexus.nombreooii as ugel',
                'nexus.codplaza as cod',
                'nexus.obser as obser',
                'nexus.descmovim as mov',
                'nexus.fecinicio as finicio',
                'nexus.fectermino as ftermino',
                DB::raw("'OFICIAL' as fuente") // <- Para saber de dónde viene
            )
            ->where('nexus.codlocal', $codlocal)
            ->whereIn('nexus.idnivel', $idnivelesModalidad)
            ->whereNotNull('nexus.numdocum')
            ->where('nexus.numdocum', '!=', 'VACANTE')
            ->where('nexus.situacion', '!=', 'VACANTE')
            ->where('nexus.estado', 1);

        $personalExcepcional = DB::table('nexus_excepcional')
            ->select(
                'nexus_excepcional.numdocum as dni',
                DB::raw("CONCAT(nexus_excepcional.apellipat, ' ', nexus_excepcional.apellimat, ', ', nexus_excepcional.nombres) as nombres"),
                'nexus_excepcional.descargo as cargo',
                'nexus_excepcional.situacion as condicion',
                'nexus_excepcional.jornlab as jornada',
                'nexus_excepcional.descniveduc as nivel',
                'nexus_excepcional.nombreooii as ugel',
                'nexus_excepcional.codplaza as cod',
                'nexus_excepcional.obser as obser',
                'nexus_excepcional.descmovim as mov',
                'nexus_excepcional.fecinicio as finicio',
                'nexus_excepcional.fectermino as ftermino',
                DB::raw("'EXCEPCIONAL' as fuente")
            )
            ->where('nexus_excepcional.codlocal', $codlocal)
            ->whereIn('nexus_excepcional.idnivel', $idnivelesModalidad)
            ->whereNotNull('nexus_excepcional.numdocum')
            ->where('nexus_excepcional.numdocum', '!=', 'VACANTE')
            ->where('nexus_excepcional.situacion', '!=', 'VACANTE')
            ->where('nexus_excepcional.estado', 1);

        $personal = $personalNexus->unionAll($personalExcepcional)->get();
                
        //dd($personal);
        // Niveles únicos
        $niveles = $personal->pluck('nivel')->unique()->sort()->values();

        // Nivel seleccionado
        $nivelSeleccionado = $request->get('nivel');
        if (!$nivelSeleccionado || !$niveles->contains($nivelSeleccionado)) {
            $nivelSeleccionado = $niveles->first();
        }
        //dd($nivelSeleccionado);
        // Filtra por nivel
        $filtrados = $personal->where('nivel', $nivelSeleccionado)->values();
        //dd($filtrados);
        return view('RegistroPagos/registropagos', [
            'niveles' => $niveles,
            'personal' => $personal,
            'nivelSeleccionado' => $nivelSeleccionado ?? null,
            'personalJson' => $personal,
            'direccion_ie' => $institucion->direccion_ie ?? '',
            'distrito' => $institucion->distrito ?? '',
            'directorUgel' => $directorUgel,
            'institucion' => $institucion->institucion ?? '',
            'localidad' => $localidad,
            'nombreAnio' =>$nombreAnio,
            'codlocal' => $codlocal,
            'logo' => $logo,
            'codmodulares' => $codmodulares,
            'resolucion' => $resolucion,
            'correo_inst' =>$correo_inst,

        ]);
    }
    
}
