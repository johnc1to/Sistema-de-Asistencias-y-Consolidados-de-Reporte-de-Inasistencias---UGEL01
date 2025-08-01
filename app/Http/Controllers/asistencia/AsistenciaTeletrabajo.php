<?php

namespace App\Http\Controllers\asistencia;

use App\Http\Controllers\Controller;
use App\Models\teletrabajo\JmmjActividadesTeletrabajo;
use App\Models\teletrabajo\JmmjObservacionTeletrabajo;

use App\Models\teletrabajo\JmmjPersonalTeletrabajo;
use App\Models\teletrabajo\JmmjPersonalTeletrabajoExcepcion;

use App\Models\teletrabajo\JmmjUrlTeam;
use App\Models\teletrabajo\WtsLogAsistencia;
use App\Models\teletrabajo\WtsUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;//jmmj 30-05-2025
class AsistenciaTeletrabajo extends Controller
{
    public function __construct(Request $request)
    {
    
        $this->middleware('verificar.sesion.admin');
    }
    
    public function index(Request $request)
    {
        $dia = $this->dia(date("Y-m-d"));
        $dni = $request->session()->get("siic01_admin")["ddni"];
       $teletrabajo = WtsUsuario::where("estado",1)->where("sede_id",1)->where("dni",$dni)
            ->with(["personalteletrabajo"=>function($query) use($dia)
            {
                $query->where("estado",1)->where("frecuencia","like","%".$dia."%");
            }])->first();
        $diferencia = $this->calcularDiferencia($teletrabajo);

        return view("asistencia.index",compact("teletrabajo","diferencia"));
    }
    
    public function calcularDiferencia($teletrabajo)
    {
        if(isset($teletrabajo))
        {
            if(isset($teletrabajo->personalteletrabajo))
            {
                $fecha1 = Carbon::now();
                $fecha2 = Carbon::createFromFormat('Y-m-d', $teletrabajo->personalteletrabajo->fecha_fin);

                // Calcular la diferencia
                $diferenciaEnDias = $fecha1->diffInDays($fecha2); // Diferencia en días
                $diferenciaEnHoras = $fecha1->diffInHours($fecha2); // Diferencia en horas
                $diferenciaEnMinutos = $fecha1->diffInMinutes($fecha2); // Diferencia en minutos

                // Formatear la diferencia
                $diferenciaFormateada = $fecha1->diff($fecha2)->format('%d días, %h horas, %i minutos');
                return $diferenciaEnDias;
                // return response()->json([
                //     'diferencia_dias' => $diferenciaEnDias,
                //     'diferencia_horas' => $diferenciaEnHoras,
                //     'diferencia_minutos' => $diferenciaEnMinutos,
                //     'diferencia_formateada' => $diferenciaFormateada,
                // ]);
            }
            return null;
        }
        return null;

    }

    public function data()
    {
        $dni = request()->session()->get("siic01_admin")["ddni"];
        $asistencia = WtsLogAsistencia::where("dni","1".$dni)->where("fecha",date("Y-m-d"))
        ->where("tipo_asistencia",1)->orderBy("idLogAsistencia","DESC")->get();
        return response()->json(["data"=>$asistencia]);
    }

    public function store(Request $request)
    {
       // date_default_timezone_set('America/Lima');
        $dni = $request->session()->get("siic01_admin")["ddni"];
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $tipo = 1; // 1 para teletrabajo, 0 para presencial
        $horaEntrada = "8:00:00";
        $horaSalida = "17:00:00";
        // Convertir las horas a objetos DateTime
        $horaActual = new \DateTime($hora);
        $horaEntradaObj = new \DateTime($horaEntrada);
        // Calcular la diferencia
        $diferencia = $horaActual->diff($horaEntradaObj);
        // Obtener la diferencia en minutos
        $diferenciaEnMinutos = ($diferencia->h * 60) + $diferencia->i;
        // Si la hora actual es mayor que la hora de entrada, la diferencia será positiva
        if ($horaActual > $horaEntradaObj) {
            $tardanzaMinutos = $diferenciaEnMinutos;
        } else {
            $tardanzaMinutos = 0; // No hay tardanza si la hora actual es menor o igual a la hora de entrada
        }
        // Verifica si ya existe un registro de asistencia para el día actual
        if($dni != null){

                // Si no existe, crea un nuevo registro de asistencia
               $result =  WtsLogAsistencia::create([
                    'dni' => "1".$dni,
                    'entrada_salida' => 0, // 0 para entrada, 1 para salida
                    'hora' => $hora,
                    'fecha' => $fecha,
                    'fechadate' => date("Y-m-d H:i:s"),
                    'verificado' => 1,
                    'status' => 1,
                    'minutoTardanza' => $tardanzaMinutos,
                    'estado' => 1,
                    'WorkCode' => 1,
                    'tipo_asistencia' => $tipo,
                    'fecha_registro' => date("Y-m-d H:i:s"),
                    'idUsuario' => null,
                    'ip'=> $this->getRealIP(),
                    'geo' => $request->input("geo"),
                    'precision_ubicacion' => $request->input("precision_ubicacion"),
                    'fechaActualizacion' => date("Y-m-d H:i:s")
                ]);
                if($result)
                {
                    $usuario = WtsUsuario::selectRaw("idUsuario,dni")->with(["personalteletrabajo"=>function($query)
                    {
                        $query->with(["urlTeams"=>function($query)
                        {
                            $query->where("estado",1);
                        }]);
                    }])->with(["personalteletrabajoexcepcion"=>function($query)
                    {
                        $query->with(["urlTeams"=>function($query)
                        {
                            $query->where("estado",1);
                        }]);
                    }])->where("sede_id",1)
                    ->where("dni", $dni)->where("estado",1)->first();

                    // Si se creó correctamente, devuelve una respuesta exitosa
                    return response()->json(['message' => 'Asistencia registrada correctamente',"usuario"=>$usuario], 200);
                }
                else
                {
                    return response()->json(['message' => 'Error al registrar la asistencia'], 500);
                }
        }
        // Aquí puedes manejar la lógica para almacenar la asistencia de teletrabajo
        // Por ejemplo, guardar en la base de datos o realizar alguna acción específica

        return response()->json(['message' => 'Asistencia registrada correctamente']);
    }
    
     private function getRealIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
            return $_SERVER['HTTP_CLIENT_IP'];

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
       //echo $_SERVER['REMOTE_ADDR'];
        return $_SERVER['REMOTE_ADDR'];
    }


    public function salida(Request $request)
    {
        $dni = $request->session()->get("siic01_admin")["ddni"];
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");

        // Verifica si ya existe un registro de entrada para el día actual
        $asistencia = WtsLogAsistencia::where("dni", "1" . $dni)
            ->where("fecha", $fecha)
           // ->where("entrada_salida", 0) // 0 para entrada
            ->where("tipo_asistencia", 1) // 1 para teletrabajo
            ->first();

        if ($asistencia) {
            // Registra la salida
            $result = WtsLogAsistencia::create([
                'dni' => "1" . $dni,
                'entrada_salida' => 1, // 1 para salida
                'hora' => $hora,
                'fecha' => $fecha,
                'fechadate' => date("Y-m-d H:i:s"),
                'verificado' => 1,
                'status' => 1,
                'minutoTardanza' => 0,
                'estado' => 1,
                'WorkCode' => 1,
                'tipo_asistencia' => 1,
                'fecha_registro' => date("Y-m-d H:i:s"),
                'idUsuario' => null,
                'ip'=> $this->getRealIP(),
                'geo' => $request->input("geo"),
                'precision_ubicacion' => $request->input("precision_ubicacion"),
                'fechaActualizacion' => date("Y-m-d H:i:s")
            ]);

            if ($result) {
                return response()->json(['message' => 'Salida registrada correctamente'], 200);
            } else {
                return response()->json(['message' => 'Error al registrar la salida'], 500);
            }
        } else {
            $result = WtsLogAsistencia::create([
                'dni' => "1" . $dni,
                'entrada_salida' => 1, // 1 para salida
                'hora' => $hora,
                'fecha' => $fecha,
                'fechadate' => date("Y-m-d H:i:s"),
                'verificado' => 1,
                'status' => 1,
                'minutoTardanza' => 0,
                'estado' => 1,
                'WorkCode' => 1,
                'tipo_asistencia' => 1,
                'fecha_registro' => date("Y-m-d H:i:s"),
                'idUsuario' => null,
                 'ip'=> $this->getRealIP(),
                'fechaActualizacion' => date("Y-m-d H:i:s")
            ]);
            if ($result) {
                return response()->json(['message' => 'No se encontró un registro de entrada para el día actual, se rocedió a marcar su salida'], 200);
            } else {
                return response()->json(['message' => 'Error al registrar la salida'], 500);
            }
        }
    }

    private function dia($today)
    {
        $daysOfWeek = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo'
        ];
        $dayOfWeek = date('l', strtotime($today));
        $dayOfWeekInSpanish = $daysOfWeek[$dayOfWeek];
        return  $dayOfWeekInSpanish;
    }

    public function reporte_asistencia(Request $request)
    {
       // dd($request->session()->get("siic01_admin"));
        return view("asistencia.reporte-asistencia");
    }

    public function data_reporte_asistencia_teletrabajo()
    {
        $dni = request()->session()->get("siic01_admin")["ddni"];
        $asistencia = WtsLogAsistencia::where("dni","1".$dni)
        ->where("tipo_asistencia",1)->orderBy("idLogAsistencia","DESC")->get();
        return response()->json(["data"=>$asistencia]);
    }

    public function monitoreo_asistencia()
    {
        $dni = request()->session()->get("siic01_admin")["ddni"];
        $cargo = request()->session()->get("siic01_admin")["cargo"];
        $personal = null;$teletrabajo=null; $jc=null;$url=null;$error=null;
         $dia = $this->dia(date("Y-m-d"));
        if ($cargo) {
            // Convert the cargo string to lowercase for case-insensitive comparison
            $lowerCargo = strtolower($cargo);
            // Check if the lowercase string contains 'coord' or 'jef'
            if (str_contains($lowerCargo, 'coord'))
            {
                $equipos = request()->session()->get("siic01_admin")["id_oficina"];
                $personal = JmmjPersonalTeletrabajo::where("id_equipo",$equipos)->where("frecuencia","like","%".$dia."%")
                ->where("estado",1)->where("flg",1)->get()->pluck("id_usuario")->toArray();
                 $jc="Coordinador";
                $url = JmmjUrlTeam::selectRaw("jmmj_url_teams.id,organigrama.DesOrg")->join("organigrama","organigrama.id","=","jmmj_url_teams.id_equipo")->
                whereIdEquipo($equipos)->whereEstado(1)->get();
                 $personal1 = JmmjPersonalTeletrabajoExcepcion::where("id_equipo",$equipos)
                ->where("estado",1)->where("flg",1)->get()->pluck("id_usuario")->toArray();
                if($personal1!=null)
                {
                    $personal = array_merge($personal,$personal1);
                }
                $personal = array_unique($personal);
            } else if(str_contains($lowerCargo, 'jef'))
            {
                $area = request()->session()->get("siic01_admin")["id_area"];
                $personal = JmmjPersonalTeletrabajo::where("id_area",$area)->where("frecuencia","like","%".$dia."%")
                ->where("estado",1)->where("flg",1)->get()->pluck("id_usuario")->toArray();
                $jc="jefe";
                   $url = JmmjUrlTeam::selectRaw("jmmj_url_teams.id,organigrama.DesOrg")->join("organigrama","organigrama.id","=","jmmj_url_teams.id_equipo")
                ->whereIdArea($area)->whereEstado(1)->get();
            }
        } else {
            // Handle the case where $cargo might be null or empty
            $error="No cuenta con cargo definido.";
            return view("asistencia.error",compact("error"));
        }
        if($jc==null)
        {
            $error="El cargo con el que cuenta no es de Coordinador o Jefe dentro del SIIC01.";
            return view("asistencia.error",compact("error"));
        }
        if($personal==null)
        {
            $error="No cuenta con personal en teletrabajo para el día de hoy";
          //  return view("asistencia.error",compact("error"));
        }

        $dia = $this->dia(date("Y-m-d"));
       $teletrabajo = WtsUsuario::where("estado",1)->where("sede_id",1)->whereIn("idUsuario",$personal)
       ->with(["personalteletrabajo"=>function($query) use($dia)
        {
            $query->where("estado",1)->where("frecuencia","like","%".$dia."%");
        }])->get();
        
       

        return view("asistencia.view-monitoreo",compact('teletrabajo','url','jc','error'));
    }

    public function data_monitoreo_asistencia(Request $request)
    {
        //dd(request()->session()->get("siic01_admin"));
        $dni = request()->session()->get("siic01_admin")["ddni"];
        $cargo = request()->session()->get("siic01_admin")["cargo"];
        $personal = null;
        $dia = $this->dia(date("Y-m-d"));
        if ($cargo) {
            // Convert the cargo string to lowercase for case-insensitive comparison
            $lowerCargo = strtolower($cargo);
            // Check if the lowercase string contains 'coord' or 'jef'
            if (str_contains($lowerCargo, 'coord'))
            {
                $equipos = request()->session()->get("siic01_admin")["id_oficina"];
                $personal = JmmjPersonalTeletrabajo::where("id_equipo",$equipos)->where("frecuencia","like","%".$dia."%")
                ->where("estado",1)->where("flg",1)->get()->pluck("id_usuario")->toArray();
                
                $personal1 = JmmjPersonalTeletrabajoExcepcion::where("id_equipo",$equipos)->where("frecuencia","like","%".$dia."%")
                ->where("estado",1)->where("flg",1)->get()->pluck("id_usuario")->toArray();
                if($personal1!=null)
                {
                    $personal = array_merge($personal,$personal1);
                }
                $personal = array_unique($personal);

            } else if(str_contains($lowerCargo, 'jef'))
            {
                $area = request()->session()->get("siic01_admin")["id_area"];
                $personal = JmmjPersonalTeletrabajo::where("id_area",$area)->where("frecuencia","like","%".$dia."%")
                ->where("estado",1)->where("flg",1)->get()->pluck("id_usuario")->toArray();

            }
        } else {
            // Handle the case where $cargo might be null or empty
            return response()->json(["data"=>null]);
        }
        if($personal==null)
        {
            return response()->json(["data"=>null]);
        }

        $asistencia = WtsUsuario::whereIn("idUsuario",$personal)->where("sede_id",1)->where("estado",1)->get();
        if($asistencia)
        {
            foreach ($asistencia as $key => $value) {
                $asistencia[$key]->fecha_hoy = date("Y-m-d");
                $asistencia[$key]->asistencia = $this->log_asistencia($value->dni);
                $asistencia[$key]->observacion = $this->observacion($value->idUsuario);

            }
        }

        return response()->json(["data"=>$asistencia]);
    }
    
     protected function observacion($idUsuario)
    {
        return JmmjObservacionTeletrabajo::where("id_usuario",$idUsuario)->where("estado",1)->get();
    }

    protected function observacion1($idUsuario,$fecha)
    {
        return JmmjObservacionTeletrabajo::where("id_usuario",$idUsuario)->where("fecha_observacion",$fecha)->where("estado",1)->get();
    }
     public function data_monitoreo_asistencia1(Request $request)
    {
        //dd(request()->session()->get("siic01_admin"));
        $dni = request()->session()->get("siic01_admin")["ddni"];
        $cargo = request()->session()->get("siic01_admin")["cargo"];
        $personal = null;
        if ($cargo) {
            // Convert the cargo string to lowercase for case-insensitive comparison
            $lowerCargo = strtolower($cargo);
            // Check if the lowercase string contains 'coord' or 'jef'
            if (str_contains($lowerCargo, 'coord'))
            {
                $equipos = request()->session()->get("siic01_admin")["id_oficina"];
                $personal = JmmjPersonalTeletrabajo::where("id_equipo",$equipos)
                ->where("estado",1)->where("flg",1)->get()->pluck("id_usuario")->toArray();
                 $personal1 = JmmjPersonalTeletrabajoExcepcion::where("id_equipo",$equipos)
                ->where("estado",1)->where("flg",1)->get()->pluck("id_usuario")->toArray();
                if($personal1!=null)
                {
                    $personal = array_merge($personal,$personal1);
                }
                $personal = array_unique($personal);

            } else if(str_contains($lowerCargo, 'jef'))
            {
                $area = request()->session()->get("siic01_admin")["id_area"];
                $personal = JmmjPersonalTeletrabajo::where("id_area",$area)
                ->where("estado",1)->where("flg",1)->get()->pluck("id_usuario")->toArray();

            }
        } else {
            // Handle the case where $cargo might be null or empty
            return response()->json(["data"=>null]);
        }
        if($personal==null)
        {
            return response()->json(["data"=>null]);
        }
        

        $asistencia = WtsUsuario::selectRaw("wts_usuarios.dni,wts_usuarios.idUsuario,wts_usuarios.equipo,wts_usuarios.area,wts_usuarios.nombres,
        wts_usuarios.apellidos,wts_usuarios.regimen,wts_log_asistencia.fecha")->join('wts_log_asistencia', function ($join) {
                // Compara la columna dni de wts_log_asistencia
                // con el resultado de concatenar '1' y el dni de wts_usuarios
                $join->on(DB::raw("concat('1', wts_usuarios.dni)"), '=', 'wts_log_asistencia.dni');
            })->
        whereIn("wts_usuarios.idUsuario",$personal)->where("wts_usuarios.sede_id",1)->where("wts_usuarios.estado",1)->where("wts_log_asistencia.tipo_asistencia",1)
        ->groupBy(["wts_usuarios.dni","wts_usuarios.idUsuario","wts_usuarios.equipo","wts_usuarios.area","wts_usuarios.nombres",
        "wts_usuarios.apellidos","wts_usuarios.regimen","wts_log_asistencia.fecha"])->get();
       // dd($asistencia->toArray());
        if($asistencia)
        {
            foreach ($asistencia as $key => $value) {

                $asistencia[$key]->asistencia = $this->log_asistencia1($value->dni,$value->fecha);
                $asistencia[$key]->observacion = $this->observacion1($value->idUsuario,$value->fecha);

            }
        }

        return response()->json(["data"=>$asistencia]);
    }
    private function log_asistencia1($dni,$fecha){
        return WtsLogAsistencia::whereEstado(1)->whereDni("1".$dni)->whereFecha($fecha)->whereTipoAsistencia(1)->get();

    }
    private function log_asistencia($dni){
        return WtsLogAsistencia::whereEstado(1)->whereDni("1".$dni)->whereFecha(date("Y-m-d"))->whereTipoAsistencia(1)->get();

    }

    public function link_acceso(Request $request)
    {
        $id = $request->input("id");
        $area = request()->session()->get("siic01_admin")["id_area"];
        $url = JmmjUrlTeam::where("estado",1)->where("id_area",$area)->whereId($id)->first();
        return response()->json(["data"=>$url]);

    }

    public function actividades_teletrabajo(Request $request)
    {
        $dni = request()->session()->get("siic01_admin")["ddni"];
        $cargo = request()->session()->get("siic01_admin")["cargo"];
        $personal = "sin";$teletrabajo=null;
        $dia = $this->dia(date("Y-m-d"));

        if ($cargo) {
            // Convert the cargo string to lowercase for case-insensitive comparison
            $lowerCargo = strtolower($cargo);
            // Check if the lowercase string contains 'coord' or 'jef'
            if (str_contains($lowerCargo, 'coord'))
            {
                $equipos = request()->session()->get("siic01_admin")["id_oficina"];
                $personal = JmmjPersonalTeletrabajo::where("id_equipo",$equipos)
                ->where("frecuencia","like","%".$dia."%")
                ->where("estado",1)->where("flg",1)->get()->pluck("id_usuario")->toArray();
                 $personal1 = JmmjPersonalTeletrabajoExcepcion::where("id_equipo",$equipos)
                 ->where("frecuencia","like","%".$dia."%")
                ->where("estado",1)->where("flg",1)->get()->pluck("id_usuario")->toArray();
                if($personal1!=null)
                {
                    $personal = array_merge($personal,$personal1);
                }
                $personal = array_unique($personal);

            } else if(str_contains($lowerCargo, 'jef'))
            {
                $area = request()->session()->get("siic01_admin")["id_area"];
                $personal = JmmjPersonalTeletrabajo::where("id_area",$area)
                ->where("frecuencia","like","%".$dia."%")
                ->where("estado",1)->where("flg",1)->get()->pluck("id_usuario")->toArray();

            }
        } else {
            // Handle the case where $cargo might be null or empty
            $error="No cuenta con cargo definido.";
            return view("asistencia.error",compact("error"));
        }
        if($personal=="sin")
        {
            $error="El cargo con el que cuenta no es de Coordinador o Jefe dentro del SIIC01.";
            return view("asistencia.error",compact("error"));
        }
        if($personal!=null)
        {
            $persona = WtsUsuario::with("personalTeletrabajo")->where("estado",1)->where("sede_id",1)->whereIn("idUsuario",$personal)->get();
            return view("asistencia.actividades-teletrabajo",compact("persona"));

        }else{
            $persona = null;//jmmj 30-05-2025
            $error="No cuenta personal en teletrabajo el día de hoy.";
            return view("asistencia.actividades-teletrabajo",compact("persona","error"));//jmmj 30-05-2025
        
        }
    }

    public function data_actividad_teletrabajo()
    {
        $dni = request()->session()->get("siic01_admin")["ddni"];
        $cargo = request()->session()->get("siic01_admin")["cargo"];
        $personal = "sin";$teletrabajo=null;
        $dia = $this->dia(date("Y-m-d"));

        if ($cargo) {
            // Convert the cargo string to lowercase for case-insensitive comparison
            $lowerCargo = strtolower($cargo);
            // Check if the lowercase string contains 'coord' or 'jef'
            if (str_contains($lowerCargo, 'coord'))
            {
                $equipos = request()->session()->get("siic01_admin")["id_oficina"];
                $personal = JmmjPersonalTeletrabajo::where("id_equipo",$equipos)
                //->where("frecuencia","like","%".$dia."%")
                ->where("estado",1)->where("flg",1)->get()->pluck("id_usuario")->toArray();
                $personal1 = JmmjPersonalTeletrabajoExcepcion::where("id_equipo",$equipos)
                //->where("frecuencia","like","%".$dia."%")
                ->where("estado",1)->where("flg",1)->get()->pluck("id_usuario")->toArray();
                if($personal1!=null)
                {
                    $personal = array_merge($personal,$personal1);
                }
                $personal = array_unique($personal);

            } else if(str_contains($lowerCargo, 'jef'))
            {
                $area = request()->session()->get("siic01_admin")["id_area"];
                $personal = JmmjPersonalTeletrabajo::where("id_area",$area)
                //->where("frecuencia","like","%".$dia."%")
                ->where("estado",1)->where("flg",1)->get()->pluck("id_usuario")->toArray();

            }
        } else {
            // Handle the case where $cargo might be null or empty
            return response()->json(["data"=>null]);

        }
        if($personal=="sin")
        {
            return response()->json(["data"=>null]);
        }
        if($personal!=null)
        {
            $persona = WtsUsuario::join("jmmj_actividades_teletrabajo","jmmj_actividades_teletrabajo.id_usuario","=","wts_usuarios.idUsuario")->
            where("jmmj_actividades_teletrabajo.estado",1)->where("wts_usuarios.sede_id",1)->whereIn("idUsuario",$personal)
            ->where("wts_usuarios.estado",1)
             ->orderBy("jmmj_actividades_teletrabajo.id","desc")
            ->get();
            return response()->json(["data"=>$persona]);

        }else{
            return response()->json(["data"=>null]);
        }
        return response()->json(["data"=>null]);
    }


    public function guardar_actividades(Request $request)
    {
        
       
        $dni = request()->session()->get("siic01_admin")["ddni"];
        $asigna = WtsUsuario::where("estado",1)->where("sede_id",1)->where("dni",$dni)->first();
         if ($request->has('id_usuario')) {
            $id_usuario = $request->id_usuario;
        }else
        {

            $id_usuario = $asigna->idUsuario;
        }

        $actividad = $request->actividad;
        $accion = $request->accion; //jmmj 30-05-2025
        $usuario = WtsUsuario::findOrFail($id_usuario);

        $data = JmmjActividadesTeletrabajo::create([
            'id_usuario' => $id_usuario,
            'id_usuario_cj'=>($asigna)?$asigna->idUsuario:0,
            'id_area'=>$usuario->area_id,
            'id_equipo' => $usuario->equipo_id,
            'actividad' => $actividad,
            'accion' => $accion, //jmmj 30-05-2025
            'estado' => 1,
            'fecha_actividad' => date("Y-m-d H:i:s")
        ]);
        if($data)
        {
        return response()->json(["data"=>$data]);}
        else
        {
            return response()->json(["data"=>null]);
        }
    }

    public function actividades_teletrabajo_respuesta(Request $request)
    {
        return view("asistencia.actividades-teletrabajo-respuesta");
    }

    public function data_actividad_teletrabajo_respuesta(Request $request)
    {
        $dni = request()->session()->get("siic01_admin")["ddni"];
        $area = request()->session()->get("siic01_admin")["id_area"];
        $equipo = request()->session()->get("siic01_admin")["id_oficina"];
        $usuario = WtsUsuario::where(["dni"=>$dni,"sede_id"=>1,"estado"=>1])->first();

        $actividades = JmmjActividadesTeletrabajo::selectRaw("concat(wts_usuarios.nombres,' ',wts_usuarios.apellidos) as nombres,jmmj_actividades_teletrabajo.*")
                    ->leftJoin("wts_usuarios","wts_usuarios.idUsuario","=","jmmj_actividades_teletrabajo.id_usuario_cj")->
                    where("jmmj_actividades_teletrabajo.estado",1)->
                    where(["jmmj_actividades_teletrabajo.id_area"=>$area,
                    "jmmj_actividades_teletrabajo.id_usuario"=>$usuario->idUsuario])->get();

                    return response()->json(["data"=>$actividades]);
    }

    public function guardar_actividades_respuesta(Request $request)
    {
        $actividades = JmmjActividadesTeletrabajo::findOrFail($request->id);
        $actividades->respuesta = $request->respuesta;
        $actividades->situacion = $request->situacion;
        $actividades->fecha_respuesta = date("Y-m-d H:i:s");
        $actividades->accion = $request->accion;//jmmj 30-05-2025
        $actividades->medio_verificacion = $request->medio_verificacion; //jmmj 30-05-2025
        $actividades->save();
        return response()->json(["data"=>1]);



        //dd($request->all());
    }
    
    //jmmj 30-05-2025
    public function cumplimiento(Request $request){
        $dni = request()->session()->get("siic01_admin")["ddni"];
        $asigna = WtsUsuario::where("estado",1)->where("sede_id",1)->where("dni",$dni)->first();
        if ($request->has('id_usuario')) {
            $id_usuario = $request->id_usuario;
        }else
        {

            $id_usuario = $asigna->idUsuario;
        }

        $actividad = JmmjActividadesTeletrabajo::findOrFail($request->input('id'));
        $actividad->condicion = $request->input('condicion');
        $actividad->comentario_responsable = $request->input('comentario_responsable');
        $actividad->save();

        if($actividad)
        {
        return response()->json(["data"=>$actividad]);}
        else
        {
            return response()->json(["data"=>null]);
        }
    }

    public function exportarActividadesPDF(Request $request,$fecha_inicio,$fecha_fin)
    {
           $cargo = request()->session()->get("siic01_admin")["cargo"];
           if ($cargo) {
                // Convert the cargo string to lowercase for case-insensitive comparison
                $lowerCargo = strtolower($cargo);
                // Check if the lowercase string contains 'coord' or 'jef'
                if (str_contains($lowerCargo, 'coord'))
                {
                    $equipos = request()->session()->get("siic01_admin")["id_oficina"];
                    $personal = JmmjPersonalTeletrabajo::where("id_equipo",$equipos)
                    //->where("frecuencia","like","%".$dia."%")
                    ->where("estado",1)->where("flg",1)->get()->pluck("id_usuario")->toArray();
                    $personal1 = JmmjPersonalTeletrabajoExcepcion::where("id_equipo",$equipos)
                   // ->where("frecuencia","like","%".$dia."%")
                    ->where("estado",1)->where("flg",1)->get()->pluck("id_usuario")->toArray();
                    if($personal1!=null)
                    {
                        $personal = array_merge($personal,$personal1);
                    }
                    $personal = array_unique($personal);
    
                } else if(str_contains($lowerCargo, 'jef'))
                {
                    $area = request()->session()->get("siic01_admin")["id_area"];
                    $personal = JmmjPersonalTeletrabajo::where("id_area",$area)
                    //->where("frecuencia","like","%".$dia."%")
                    ->where("estado",1)->where("flg",1)->get()->pluck("id_usuario")->toArray();
                }
            }
        // Puedes filtrar por área, fecha, etc. según tu necesidad
        $actividades = WtsUsuario::whereIn("idUsuario",$personal)->where("sede_id",1)->where("estado",1)
        ->with(["actividadesTeletrabajo"=>function($query) use($fecha_inicio, $fecha_fin) {
            // Aquí puedes aplicar filtros adicionales si es necesario
            if ($fecha_inicio!="" && $fecha_fin!="") {
                $fechaInicio = Carbon::parse($fecha_inicio)->startOfDay();
                $fechaFin = Carbon::parse($fecha_fin)->endOfDay();
                $query->whereBetween('fecha_actividad', [$fechaInicio, $fechaFin])
                ->where("estado",1)->orderBy('fecha_actividad', 'desc');
            }
            else {
                $query->where("estado",1)->orderBy('fecha_actividad', 'desc');
            }
        }])->get();
    //dd($actividades[0]->actividadesteletrabajo);
        // Puedes pasar más datos a la vista si lo necesitas
        $pdf = Pdf::loadView('asistencia.pdf', compact('actividades','fecha_inicio','fecha_fin')) ->setPaper('a3', 'landscape');
    
        return $pdf->stream('reporte_actividades_teletrabajo.pdf');
    }


    public function exportarActividadesPDF1(Request $request,$fecha_inicio,$fecha_fin,$salto_linea)
    {
            $fechaInicio = Carbon::parse($fecha_inicio)->startOfDay();
            $fechaFin = Carbon::parse($fecha_fin)->endOfDay();
           $dni = request()->session()->get("siic01_admin")["ddni"];
           $personal = WtsUsuario::where("dni",$dni)->where("sede_id",1)->where("estado",1)->first();
    
            JmmjActividadesTeletrabajo::where("id_usuario",$personal->idUsuario)->whereBetween('fecha_actividad', [$fechaInicio, $fechaFin])
                ->where("estado",1)->update(["salto_linea"=>$salto_linea]);
        // Puedes filtrar por área, fecha, etc. según tu necesidad
        $actividades = WtsUsuario::where("idUsuario",$personal->idUsuario)->where("sede_id",1)->where("estado",1)
        ->with(["actividadesTeletrabajo"=>function($query) use($fecha_inicio, $fecha_fin) {
            // Aquí puedes aplicar filtros adicionales si es necesario
            if ($fecha_inicio!="" && $fecha_fin!="") {
                $fechaInicio = Carbon::parse($fecha_inicio)->startOfDay();
                $fechaFin = Carbon::parse($fecha_fin)->endOfDay();
                $query->whereBetween('fecha_actividad', [$fechaInicio, $fechaFin])
                ->where("estado",1)->orderBy('fecha_actividad', 'desc');
            }
            else {
                $query->where("estado",1)->orderBy('fecha_actividad', 'desc');
            }
        }])->get();
    //dd($actividades[0]->actividadesteletrabajo);
        // Puedes pasar más datos a la vista si lo necesitas
        //return view("asistencia.pdf",compact('actividades','fecha_inicio','fecha_fin','salto_linea'));
        $pdf = Pdf::loadView('asistencia.pdf', compact('actividades','fecha_inicio','fecha_fin','salto_linea')) ->setPaper('a3', 'landscape');
    
        return $pdf->stream('reporte_actividades_teletrabajo.pdf');
    }
    //jmmj 30-05-2025
    
    
    public function guardar_observacion(Request $request)
    {
        $dni = request()->session()->get("siic01_admin")["ddni"];
        $asigna = WtsUsuario::where("estado",1)->where("sede_id",1)->where("dni",$dni)->first();
    
    
        $id_usuario_observa = $asigna->idUsuario;
    
    
        $observacion = $request->observacion;
    
        $data = JmmjObservacionTeletrabajo::create([
            'id_usuario' => $request->id_usuario, // ID del usuario al que se le hace la observación
            'id_area'=>$asigna->area_id,
            'id_equipo' => $asigna->equipo_id,
            'id_usuario_observa' => $id_usuario_observa, // ID del usuario que hace la observación
            'observacion' => $observacion,
            'estado' => 1,
            'fecha_observacion' => date("Y-m-d H:i:s")
        ]);
        if($data)
        {
        return response()->json(["data"=>$data]);}
        else
        {
            return response()->json(["data"=>null]);
        }
    }
}
