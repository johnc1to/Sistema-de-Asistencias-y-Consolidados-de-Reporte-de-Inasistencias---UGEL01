<?php

namespace App\Http\Controllers;

use App\Models\Situacion;
use App\Models\SituacionLaboral;
use Illuminate\Http\Request;

class SituacionLaboralController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function history(Request $request)
    {
        $request->request->add(["estado"=>1]);
        $data = $request->except("_token");
        $respuesta = SituacionLaboral::where($data)->orderBy("created_at","DESC")->get();
        return response()->json(["data"=>$respuesta,"mensaje"=>1]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      try {
            $existe = SituacionLaboral::where("idTit",$request->input("idTit"))->where("idAlu",$request->input("idAlu"))
                        ->where("idPro",$request->input("idPro"))->where("flg",1)->first();
            if($existe)
            {
                $existe->flg=2;
                $existe->save();
            }
            $data = $request->except(["idTit","idAlu","idPro","_token"]);
            $situacion = new SituacionLaboral();
            $situacion->descripcion = json_encode($data,JSON_PRETTY_PRINT);
            $situacion->idTit = $request->input("idTit");
            $situacion->idAlu = $request->input("idAlu");
            $situacion->idPro = $request->input("idPro");
            $situacion->save();
            return response()->json(["mensaje"=>"Respuestas de seguimiento al egresado guardas con exito"]);
      } catch (\Throwable $th) {
        //throw $th;
      }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
