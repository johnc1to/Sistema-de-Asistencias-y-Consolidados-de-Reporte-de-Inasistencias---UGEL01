<?php

namespace App\Http\Controllers\asistencia;

use App\Http\Controllers\Controller;
use App\Models\teletrabajo\JmmjActividadesTeletrabajo;
use App\Models\teletrabajo\JmmjPersonalTeletrabajo;
use App\Models\teletrabajo\JmmjUrlTeam;
use App\Models\teletrabajo\WtsLogAsistencia;
use App\Models\teletrabajo\WtsUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsistenciaTeletrabajo extends Controller
{
    public function index(Request $request)
    {
        $dia = $this->dia(date("Y-m-d"));
        $dni = $request->session()->get("siic01_admin")["ddni"];
       $teletrabajo = WtsUsuario::where("estado",1)->where("sede_id",1)->where("dni",$dni)->with(["personalteletrabajo"=>function($query) use($dia)
        {
            $query->where("estado",1)->where("frecuencia","like","%".$dia."%");
        }])->first();

        return view("asistencia.index",compact("teletrabajo"));
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
                    }])->where("dni", $dni)->where("estado",1)->first();

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
        ->where("tipo_asistencia",1)->orderBy("fecha","DESC")->get();
        return response()->json(["data"=>$asistencia]);
    }

    public function monitoreo_asistencia()
    {
        $dni = request()->session()->get("siic01_admin")["ddni"];
        $cargo = request()->session()->get("siic01_admin")["cargo"];
        $personal = null;$teletrabajo=null;
        if ($cargo) {
            // Convert the cargo string to lowercase for case-insensitive comparison
            $lowerCargo = strtolower($cargo);
            // Check if the lowercase string contains 'coord' or 'jef'
            if (str_contains($lowerCargo, 'coord'))
            {
                $equipos = request()->session()->get("siic01_admin")["id_oficina"];
                $personal = JmmjPersonalTeletrabajo::where("id_equipo",$equipos)
                ->where("estado",1)->where("flg",1)->get()->pluck("id_usuario")->toArray();

            } else if(str_contains($lowerCargo, 'jef'))
            {
                $area = request()->session()->get("siic01_admin")["id_area"];
                $personal = JmmjPersonalTeletrabajo::where("id_area",$area)
                ->where("estado",1)->where("flg",1)->get()->pluck("id_usuario")->toArray();

            }
        } else {
            // Handle the case where $cargo might be null or empty
            $error="No cuenta con cargo definido.";
            return view("asistencia.error",compact("error"));
        }
        if($personal==null)
        {
            $error="El cargo con el que cuenta no es de Coordinador o Jefe dentro del SIIC01.";
            return view("asistencia.error",compact("error"));
        }

        $dia = $this->dia(date("Y-m-d"));
       $teletrabajo = WtsUsuario::where("estado",1)->where("sede_id",1)->whereIn("idUsuario",$personal)
       ->with(["personalteletrabajo"=>function($query) use($dia)
        {
            $query->where("estado",1)->where("frecuencia","like","%".$dia."%");
        }])->get();

        return view("asistencia.view-monitoreo",compact('teletrabajo'));
    }

    public function data_monitoreo_asistencia(Request $request)
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

        $asistencia = WtsLogAsistencia::leftJoin('wts_usuarios', function ($join) {
            // Compara la columna dni de wts_log_asistencia
            // con el resultado de concatenar '1' y el dni de wts_usuarios
            $join->on(DB::raw("concat('1', wts_usuarios.dni)"), '=', 'wts_log_asistencia.dni');
        })
        ->where("wts_usuarios.estado",1)->where("wts_log_asistencia.estado",1)->where("wts_usuarios.sede_id",1)
        ->whereIn("wts_usuarios.idUsuario",$personal)
        ->where("wts_log_asistencia.tipo_asistencia",1)->orderBy("fecha","DESC")->get();
        return response()->json(["data"=>$asistencia]);
    }

    public function link_acceso()
    {
        $area = request()->session()->get("siic01_admin")["id_area"];
        $url = JmmjUrlTeam::where("estado",1)->where("id_area",$area)->first();
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
                $personal = JmmjPersonalTeletrabajo::where("id_equipo",$equipos)->where("frecuencia","like","%".$dia."%")
                ->where("estado",1)->where("flg",1)->get()->pluck("id_usuario")->toArray();

            } else if(str_contains($lowerCargo, 'jef'))
            {
                $area = request()->session()->get("siic01_admin")["id_area"];
                $personal = JmmjPersonalTeletrabajo::where("id_area",$area)->where("frecuencia","like","%".$dia."%")
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
            $persona = WtsUsuario::where("estado",1)->where("sede_id",1)->whereIn("idUsuario",$personal)->get();
            return view("asistencia.actividades-teletrabajo",compact("persona"));

        }else{
            $error="No cuenta personal en teletrabajo el día de hoy.";
            return view("asistencia.error",compact("error"));
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
                $personal = JmmjPersonalTeletrabajo::where("id_equipo",$equipos)->where("frecuencia","like","%".$dia."%")
                ->where("estado",1)->where("flg",1)->get()->pluck("id_usuario")->toArray();

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
        if($personal=="sin")
        {
            return response()->json(["data"=>null]);
        }
        if($personal!=null)
        {
            $persona = WtsUsuario::join("jmmj_actividades_teletrabajo","jmmj_actividades_teletrabajo.id_usuario","=","wts_usuarios.idUsuario")->
            where("jmmj_actividades_teletrabajo.estado",1)->where("wts_usuarios.sede_id",1)->whereIn("idUsuario",$personal)
            ->where("wts_usuarios.estado",1)
            ->get();
            return response()->json(["data"=>$persona]);

        }else{
            return response()->json(["data"=>null]);
        }
        return response()->json(["data"=>null]);
    }


    public function guardar_actividades(Request $request)
    {
        $id_usuario = $request->id_usuario;
        $actividad = $request->actividad;
        $dni = request()->session()->get("siic01_admin")["ddni"];
        $asigna = WtsUsuario::where("estado",1)->where("sede_id",1)->where("dni",$dni)->first();
        $usuario = WtsUsuario::findOrFail($id_usuario);

        $data = JmmjActividadesTeletrabajo::create([
            'id_usuario' => $id_usuario,
            'id_usuario_cj'=>($asigna)?$asigna->idUsuario:0,
            'id_area'=>$usuario->area_id,
            'id_equipo' => $usuario->equipo_id,
            'actividad' => $actividad,
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
        $actividades->save();
        return response()->json(["data"=>1]);



        //dd($request->all());
    }
}
