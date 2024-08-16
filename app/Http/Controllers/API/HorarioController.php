<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HorarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show(string $id)
    {
        //
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

    public function listar(Request $request)
    {
        $nombre = $request->input('nombre');
        $horaInicio = $request->input('horaInicio');
        $horaFin = $request->input('horaFin');
        $ambiente = $request->input('ambiente');
        $curso = $request->input('curso');
        $dia = $request->input('dia');

        // Tratar los valores '0' como null
        $ambiente = ($ambiente == 0) ? null : $ambiente;
        $curso = ($curso == 0) ? null : $curso;
        $dia = ($dia == 0) ? null : $dia;

        $query = DB::table('horario as H')
            ->join('docente as D', 'D.id', '=', 'H.Docente')
            ->join('asignatura as A', 'A.id', '=', 'H.Curso')
            ->select('D.Nombre as NombreDocente', 'D.Apellidos as ApellidoDocente', 'H.Numero', 'H.Ambiente', 'H.Dia', 'H.HoraInicio', 'H.HoraFin', 'A.Nombre as NombreCurso');

        if (!is_null($dia)) {
            $query->where('H.Dia', '=', $dia);
        }

        if (!is_null($nombre)) {
            $query->where('D.Nombre', 'like', '%' . $nombre . '%');
        }

        if (!is_null($ambiente)) {
            $query->where('H.Ambiente', '=', $ambiente);
        }

        if (!is_null($curso)) {
            $query->where('H.Curso', '=', $curso);
        }

        if (!is_null($horaInicio) && is_null($horaFin)) {
            // Si solo se envía horaInicio, comparar desde horaInicio hasta cualquier hora
            $query->where(DB::raw('TIME("' . $horaInicio . '")'), '<=', DB::raw('TIME(H.HoraInicio)'));
        } elseif (!is_null($horaInicio) && !is_null($horaFin)) {
            // Si se envían ambas horas, comparar entre horaInicio y horaFin
            $query->where(DB::raw('TIME("' . $horaInicio . '")'), '<=', DB::raw('TIME(H.HoraInicio)'))
                ->where(DB::raw('TIME("' . $horaFin . '")'), '>=', DB::raw('TIME(H.HoraFin)'));
        }

        $results = $query->get();

        return response()->json($results);
    }




    public function listarCursos()
    {
        $results = DB::table('asignatura')
            ->select('id', 'Nombre')
            ->get();

        return response()->json($results);
    }
}
