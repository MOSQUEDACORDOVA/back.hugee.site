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

        // Definir regla de validación personalizada
        Validator::extend('valid_whatsapp', function ($attribute, $value, $parameters, $validator) {
            // Verificar si el número de WhatsApp tiene el formato correcto
            return preg_match('/^\d{1,3}\d{6,15}$/', $value);
            
        });

        // Valida los datos de la solicitud
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'whatsapp' => 'nullable|string|required_without:mail|valid_whatsapp',
            'mail' => 'nullable|string|required_without:whatsapp|email',
        ], [
            'whatsapp.valid_whatsapp' => 'El campo WhatsApp debe ser un número válido con el código de área.'
        ]);

        // Verifica si al menos uno de los campos 'whatsapp' o 'mail' está presente
        if (!$request->has('whatsapp') && !$request->has('mail')) {
            return response()->json(['error' => 'Debes proporcionar al menos un número de WhatsApp o una dirección de correo electrónico.'], 422);
        }

        if ($validator->fails()) {
            return response()->json(['error' => 'Error de validación', 'errors' => $validator->errors()], 422);
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
            return response()->json(['error' => 'Error al guardar la oferta de trabajo', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // Definir regla de validación personalizada
        Validator::extend('valid_whatsapp', function ($attribute, $value, $parameters, $validator) {
            // Verificar si el número de WhatsApp tiene el formato correcto
            return preg_match('/^\d{1,3}\d{6,15}$/', $value);
            
        });

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'whatsapp' => 'nullable|string|valid_whatsapp',
            'mail' => 'nullable|string|email',
            'status' => 'sometimes|required|integer', // Agregar validación para el estado
        ], [
            'whatsapp.valid_whatsapp' => 'El campo WhatsApp debe ser un número válido con el código de área.'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Error de validación', 'errors' => $validator->errors()], 422);
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








