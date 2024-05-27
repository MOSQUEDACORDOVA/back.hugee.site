<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobOffers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class JobOffersController extends Controller
{

    /**
     * Almacena una nueva oferta de trabajo.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Valida los datos de la solicitud
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'whatsapp' => 'nullable|string',
            'mail' => 'nullable|string|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Crea una nueva oferta de trabajo
            $jobOffer = new JobOffers();
            $jobOffer->title = $request->title;
            $jobOffer->description = $request->description;
            $jobOffer->whatsapp = $request->whatsapp;
            $jobOffer->mail = $request->mail;
            $jobOffer->status = $request->status ?? 2; // Establece un valor por defecto para 'status'
            $jobOffer->save();

            // Retorna una respuesta de éxito
            return response()->json(['message' => 'Oferta de trabajo creada con éxito', 'data' => $jobOffer], 201);
        } catch (\Exception $e) {
            // Loguea el error
            Log::error('Error al guardar la oferta de trabajo: ' . $e->getMessage());
            return response()->json(['error' => 'Error al guardar la oferta de trabajo'], 500);
        }
    }
}
