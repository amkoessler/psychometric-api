<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Http\Resources\PatientResource;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Buscar todos os pacientes
        $patients = Patient::all();

        // Retorna a coleção formatada 
        return PatientResource::collection($patients);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $patientCode)
    {
        // Usa where('patient_id', ...) para buscar pelo código único de 6 dígitos.
        // firstOrFail() garante que se não for encontrado, ele retorne um 404.
        $patient = Patient::where('patient_code', $patientCode)->firstOrFail();

        // Retorna o paciente encontrado, formatado pelo Resource
        return new PatientResource($patient);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
