<?php
namespace App\Http\Traits;

use App\Models\bienes\BienesMovimiento;
use App\Models\bienes\PdfFirmaDesplazamiento;
use App\Models\bienes\UserSiguhs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use setasign\Fpdi\Fpdi;
trait RefirmaInvokerTraits {

    protected function fileDownloadUrl($ruta=null)
    {
        if($ruta->numero_firmas == 0)
        {
            return "https://siguhs.ugel01.gob.pe/movimiento_patrimonio/".$ruta->correlativo.".pdf";
        }else{
            return "https://siguhs.ugel01.gob.pe/movimiento_patrimonio/firmados/".$ruta->correlativo."_".$ruta->numero_firmas.".pdf";
        }
    }

    private function CalcularXYSimple1($movimiento,$persona){
        if($movimiento->id_movimiento == 4)
        {
            $tipo2 = PdfFirmaDesplazamiento::whereCorrelativoId($movimiento->correlativo)->wherePersonaId($persona->CodPer)
            ->whereEstado(1)->whereTipo(2)->first();
            $tipo3= PdfFirmaDesplazamiento::whereCorrelativoId($movimiento->correlativo)->wherePersonaId($persona->CodPer)
            ->whereEstado(1)->whereTipo(3)->first();
            $tipo4= PdfFirmaDesplazamiento::whereCorrelativoId($movimiento->correlativo)->wherePersonaId($persona->CodPer)
            ->whereEstado(1)->whereTipo(4)->first();
            //$persona->CodPer
            if($movimiento->numero_firmas ==3)
            {
                if($movimiento->id_persona_transferente == $persona->CodPer and $tipo4 == null)
                {
                    $CalculoEjeY=490;
                    $PosicionY=$CalculoEjeY;
                    $PosicionX='650';
                    $motivo ="RETORNO DEL BIEN";
                }
            }else{

                if($movimiento->id_persona_transferente == $persona->CodPer and $tipo2 == null)
                {
                    $CalculoEjeY=490;
                    $PosicionY=$CalculoEjeY;
                    $PosicionX='260';
                    $motivo ="TRANSFERENTE";
                }

                 if($movimiento->id_persona_receptor == $persona->CodPer and $tipo3 == null)
                {
                    $CalculoEjeY=490;
                    $PosicionY=$CalculoEjeY;
                    $PosicionX='460';
                    $motivo ="RECEPTOR";
                }
            }

            //650

        }else {
            $tipo2 = PdfFirmaDesplazamiento::whereCorrelativoId($movimiento->correlativo)->wherePersonaId($persona->CodPer)
            ->whereEstado(1)->whereTipo(2)->first();
            $tipo3= PdfFirmaDesplazamiento::whereCorrelativoId($movimiento->correlativo)->wherePersonaId($persona->CodPer)
            ->whereEstado(1)->whereTipo(3)->first();
            if($movimiento->correlativo>=279 and $movimiento->correlativo<=376)
            {

                if($movimiento->id_persona_transferente == $persona->CodPer and $tipo2 == null)
                {
                $CalculoEjeY=470;
                $PosicionY=$CalculoEjeY;
                $PosicionX='65';
                $motivo ="TRANSFERENTE";
                }
                if($movimiento->id_persona_receptor == $persona->CodPer and $tipo3 == null)
                {
                    $CalculoEjeY=470;
                    $PosicionY=$CalculoEjeY;
                    $PosicionX='650';
                    $motivo ="RECEPTOR";
                }

            }else{
                if($movimiento->id_persona_transferente == $persona->CodPer and $tipo2 == null)
                {
                    $CalculoEjeY=490;
                    $PosicionY=$CalculoEjeY;
                    $PosicionX='260';
                    $motivo ="TRANSFERENTE";
                }
                if($movimiento->id_persona_receptor == $persona->CodPer and $tipo3 == null)
                {
                    $CalculoEjeY=490;
                    $PosicionY=$CalculoEjeY;
                    $PosicionX='460';
                    $motivo ="RECEPTOR";
                }
            }
        }

        return array("EjeX"=>$PosicionX,"EjeY"=>$PosicionY,"motivo"=>$motivo);
      }

    public function firmaSimple(Request $request)
    {
        $pagina =  BienesMovimiento::whereCorrelativo($request->input("id"))->whereEstado(1)->first();
        $userSiguhs = UserSiguhs::where("NomUsu",$request->session()->get("siic01_admin")["ddni"])->where("EstUsu",1)->first();


        $filedownloadurl = $this->fileDownloadUrl($pagina);
        $idpatron=time();

        if($pagina->numero_firmas == 0)
        {
            $archivo = $pagina->correlativo."_".($pagina->numero_firmas+1).".pdf";
        }else{
            $archivo = $pagina->correlativo."_".($pagina->numero_firmas+1).".pdf";
        }

        $posicionxy = $this->CalcularXYSimple1($pagina,$userSiguhs);


        $param ='{
            "app":"'.config('identificador.app.tipo').'",
            "fileUploadUrl":"https://siguhs.ugel01.gob.pe/laravel",
            "reason":"'.$posicionxy["motivo"].'",
            "mode":"'.config('identificador.firma.simple.pdf').'",
            "type":"'.config('identificador.type').'",
            "clientId":"'.config('identificador.keys.public').'",
            "clientSecret":"'.config('identificador.keys.secrect').'",
            "dcfilter":".*FIR.*|.*FAU.*",
            "fileDownloadUrl":"'.$filedownloadurl.'",
            "posx":"'.$posicionxy["EjeX"].'",
            "posy":"'.$posicionxy["EjeY"].'",
            "protocol":"'.config('identificador.protocolo.https').'",
            "contentFile":"'.$idpatron.'.pdf",
            "stampAppearanceId":"0",
            "isSignatureVisible":"true",
            "idFile":"MyForm1",
            "outputFile":"'.$archivo.'",
            "fileDownloadLogoUrl":"'.URL::asset('/imagenes/escudos.png').'",
            "fileDownloadStampUrl":"'.URL::asset('/imagenes/escudos.png').'",
            "pageNumber":"'.($pagina->pagina_pdf-1).'",
            "signatureLevel":"0",
            "maxFileSize":"'.config('identificador.maxFileSize').'",
            "fontSize":"'.config('identificador.fontSize').'",
            "timestamp":"false"
        }';
      // return "<pre>".$param;
       return   base64_encode($param);
    }
}
