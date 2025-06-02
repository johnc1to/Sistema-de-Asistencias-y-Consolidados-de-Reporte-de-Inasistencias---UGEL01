<?php

namespace App\Http\Controllers\bienes;

use App\Http\Controllers\Controller;
use App\Models\bienes\Bienes;
use App\Models\bienes\BienesMovimiento;
use App\Models\bienes\Persona;
use App\Models\Directores;
use App\Models\bienes\UserSiguhs;
use Illuminate\Http\Request;

class BienesUser extends Controller
{
    public function index(Request $request)
    {
        $userSiguhs = UserSiguhs::where("NomUsu",$request->session()->get("siic01_admin")["ddni"])->where("EstUsu",1)->first();
        if($userSiguhs)
        {
            $bienes = Bienes::with(["tipobien","marca","modelo","sede","condiciondescipcion","terceropracticante","equipo"])
                        ->whereIdPersona($userSiguhs->CodPer)->whereEstado(1)->get();
                       // dd($bienes);
            return view("bienes.index",compact("bienes"));
        }

    }

    public function transferido(Request $request)
    {
         
      // try{
        $userSiguhs = UserSiguhs::where("NomUsu",$request->session()->get("siic01_admin")["ddni"])->where("EstUsu",1)->first();
        
        if($userSiguhs)
        {
            $dato = BienesMovimiento::selectRaw("numero_firmas,observacion_rechazo,usuario_rechazo,bienes_movimiento1.estado,bienes_movimiento1.sustento,fecha_aprobacion_eti,movimiento,correlativo,id_persona_transferente,id_persona_receptor,id_movimiento,bienes_movimiento1.flg,flg_aprobacion_eti")
                ->join("tipo_bien as tb","tb.id","=","bienes_movimiento1.id_tipo_bien")
                ->with("movimiento1")
                ->with("firmas",function($query)use($userSiguhs){
                    $query->where("persona_id", $userSiguhs->CodPer)->whereIn("tipo",[2,3])->whereEstado(1);
                })
                ->whereIn("bienes_movimiento1.estado",[0,1])
                ->whereIn("tb.flg",[1,0])
                ->where("usuario_rechazo","=",null)
                ->whereRaw("YEAR(bienes_movimiento1.fecha_creacion)>=2025")
                ->where(function($where)use($userSiguhs){
                    $where->whereIdPersonaTransferente( $userSiguhs->CodPer)->orWhere("id_persona_receptor", $userSiguhs->CodPer);
                })

                ->groupBy(["numero_firmas","observacion_rechazo","usuario_rechazo","bienes_movimiento1.estado","bienes_movimiento1.sustento","fecha_aprobacion_eti","movimiento","correlativo","id_persona_transferente","id_persona_receptor","id_movimiento","flg","flg_aprobacion_eti"])->get();
 //dd($dato);
            //$dato = BienesBienesMovimiento::with(["movimiento"])->where("estado",1)->get();
            if($dato)
            {
                foreach ($dato as $key => $value) {
                    $dato[$key]->transferente = $this->ConsultaPersona($value->id_persona_transferente);
                    $dato[$key]->receptor = $this->ConsultaPersona($value->id_persona_receptor);
                    if($value->usuario_rechazo!=null)
                    {
                        $dato[$key]->usuario_rechazo = $this->ConsultaPersona($value->usuario_rechazo);

                    }
                    $dato[$key]->flg_tipoBien = $this->flgTipoBien($value->correlativo);
                    $dato[$key]->fecha_modificacion = date("Y-m-d");
                    $dato[$key]->id =$value->correlativo;
                }
            }
           
           
        }
         return view("bienes.transferido",compact(["dato","userSiguhs"]));
      // } catch (\Throwable $th) {
         //   dd($th);
       // }
    }

    protected function flgTipoBien($correlativo)
    {
        return BienesMovimiento::selectRaw("tb.flg")->join("tipo_bien as tb","tb.id","=","bienes_movimiento1.id_tipo_bien")
                    ->where("bienes_movimiento1.estado",1)
                    ->where("bienes_movimiento1.correlativo",$correlativo)->groupBy(["tb.flg"])->get();
    }

    protected function ConsultaPersona($id)
    {
        if(strlen($id) == 8)
        {
            $persona=Directores::selectRaw("nombre as NocPer")
            ->where("dni",$id)->first();
            // dd($persona);
             if($persona)
             {
                 return $persona->NocPer;
             }
        }else{
            $persona=Persona::findOrFail($id,["NocPer"]);
           // dd($persona);
            if($persona)
            {
                return $persona->NocPer;
            }
        }
        return "No encontrado";
    }
}
