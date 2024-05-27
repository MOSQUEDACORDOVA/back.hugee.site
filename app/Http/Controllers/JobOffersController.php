<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobOffers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;

class JobOffersController extends Controller
{

    /**
     * Retorna todas las ofertas de empleo.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // Obtiene todas las ofertas de empleo
            $jobOffers = JobOffers::all();

            // Retorna una respuesta con las ofertas de empleo
            return response()->json(['data' => $jobOffers], 200);
        } catch (\Exception $e) {
            // Loguea el error
            Log::error('Error al obtener las ofertas de empleo: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener las ofertas de empleo'], 500);
        }
    }
    
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

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'whatsapp' => 'nullable|string',
            'mail' => 'nullable|string|email',
            'status' => 'sometimes|required|integer', // Agregar validación para el estado
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $jobOffer = JobOffers::findOrFail($id);
            $jobOffer->update($request->all());

            return response()->json(['message' => 'Oferta de trabajo actualizada con éxito', 'data' => $jobOffer], 200);
        } catch (\Exception $e) {
            Log::error('Error al actualizar la oferta de trabajo: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar la oferta de trabajo'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $jobOffer = JobOffers::findOrFail($id);
            $jobOffer->delete();

            return response()->json(['message' => 'Oferta de trabajo eliminada con éxito'], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error al eliminar la oferta de trabajo: ' . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar la oferta de trabajo'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}








