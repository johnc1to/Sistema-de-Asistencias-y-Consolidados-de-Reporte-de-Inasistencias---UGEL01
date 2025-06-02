<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FirmaController extends Controller
{
    public function guardarFirma(Request $request)
    {
        if ($request->hasFile('firma')) {
            $user = auth()->user(); // o el id del director como $request->id_director

            $file = $request->file('firma');
            $filename = 'firma_' . $user->id . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs('public/firmasdirector', $filename);

            // Actualiza el campo en la tabla correspondiente (ej: tabla anexo03 o contacto)
            $user->firma = $filename;
            $user->save();

            return response()->json(['success' => true, 'path' => $filename]);
        }

        return response()->json(['success' => false]);
    }
}
