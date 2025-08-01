<?php

namespace App\Http\Controllers\bienes;

use App\Http\Controllers\Controller;
use App\Http\Traits\RefirmaInvokerNoPatrimonialTraits;
use App\Models\bienes\Bienes;
use App\Models\bienes\BienTemporalDevolucion;
use App\Models\bienes\BienTemporalDevolucionNoPatrimonial;
use App\Models\bienes\PdfFirmaDesplazamientoNoPatrimonial;
use App\Models\bienes\UserSiguhs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefirmaInvokerNoPatrimonialController extends Controller
{
    use RefirmaInvokerNoPatrimonialTraits;

    public function __construct(Request $request)
    {
    
        $this->middleware('verificar.sesion.admin');
    }
    public function index(Request $request)
    {
        return $this->firmaSimple($request);
    }

    /**inicio jmmj 28-01-2025 */
    public function update_firmas(Request $request)
    {
        DB::connection("bienes")->beginTransaction();
        $userSiguhs = UserSiguhs::where("NomUsu",$request->session()->get("siic01_admin")["ddni"])->where("EstUsu",1)->first();

        try {
            $pagina =   DB::connection("bienes")->table("bienes_movimiento_no_patrimonials")->whereCorrelativo($request->input("id"))->whereEstado(1)->first();
            DB::connection("bienes")->table("bienes_movimiento_no_patrimonials")->whereCorrelativo($request->input("id"))->whereEstado(1)
            ->update(["numero_firmas"=>$pagina->numero_firmas+1]);

            $tipo2 = PdfFirmaDesplazamientoNoPatrimonial::whereCorrelativoId($request->input("id"))->wherePersonaId($userSiguhs->CodPer)
            ->whereEstado(1)->whereTipo(2)->first();
            $tipo3= PdfFirmaDesplazamientoNoPatrimonial::whereCorrelativoId($request->input("id"))->wherePersonaId($userSiguhs->CodPer)
            ->whereEstado(1)->whereTipo(3)->first();

            if( $pagina->id_persona_transferente == $userSiguhs->CodPer and $tipo2 == null)
            {
                $tipo = 2;
            }

            if( $pagina->id_persona_receptor == $userSiguhs->CodPer and $tipo3 == null)
            {
                $tipo = 3;
            }

            if($pagina->id_movimiento == 4 and ($pagina->numero_firmas+1) == 4)
            {
                $tipo = 4;
            }

            PdfFirmaDesplazamientoNoPatrimonial::create(['correlativo_id'=>$request->input("id"), 'persona_id'=>$userSiguhs->CodPer,
            'numero_firma'=>$pagina->numero_firmas+1,"tipo"=>$tipo]);

             if($pagina->id_movimiento == 4 and ($pagina->numero_firmas+1) == 4)
             {
                $dato = DB::connection("bienes")->table("bienes_movimiento_no_patrimonials")->selectRaw("btd.id")
                ->join("bien_temporal_devolucion_no_patrimonials as btd","btd.id_bien_movimiento","=","bienes_movimiento_no_patrimonials.id")
                ->whereNull("btd.fecha_recepcion")->where("bienes_movimiento_no_patrimonials.id_movimiento",4)
                ->whereCorrelativo($pagina->correlativo)->get()->pluck("id")->toArray();
                $bienes = DB::connection("bienes")->table("bienes_movimiento_no_patrimonials")->selectRaw("id_bien")
                ->whereCorrelativo($pagina->correlativo)
                ->whereEstado(1)->get()->pluck("id_bien")->toArray();
                 BienTemporalDevolucionNoPatrimonial::whereIn("id",$dato)->update(["fecha_recepcion"=>date("Y-m-d")]);
                 Bienes::whereIn("id",$bienes)->update(["id_movimiento"=>1,"situacion"=>10]);
             }

            DB::connection("bienes")->commit();
            return response()->json(["icon"=>"success","title"=>"Se ingreso un salto de linea en la fila ".$request->input("numero_firmas")." del PDF.","url"=>"-1"]);
        } catch (\Throwable $e) {
            DB::connection("bienes")->rollback();
            return $e->getMessage();
        }
    }
     /**fin jmmj 28-01-2025 */
}
