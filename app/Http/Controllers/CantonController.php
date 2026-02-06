<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Canton;
use Illuminate\Support\Facades\DB; // <--- IMPORTANTE: Faltaba esto para que funcione la búsqueda
use Illuminate\Validation\Rule;

class CantonController extends Controller
{

    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        // 1. Iniciar consulta ordenada por código
        $query = Canton::orderBy('codigo', 'asc');

        // 2. Búsqueda inteligente (Nombre o Código)
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                // Detectar base de datos para usar ILIKE (Postgres) o LIKE (MySQL)
                $operator = DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';

                $sub->where('nombre', $operator, "%{$q}%")
                    ->orWhere('codigo', $operator, "%{$q}%");
            });
        }

        // 3. Paginación manteniendo los filtros
        $cantones = $query->paginate(10)->withQueryString();

        return view('cantones.canton-index', compact('cantones'));
    }

    public function create()
    {
        $isModal = request()->ajax();
        // Asegúrate de que el archivo exista en resources/views/cantones/canton-create.blade.php
        return view('cantones.canton-create', compact('isModal'));
    }

public function store(Request $request)
    {
        // 1. SOLUCIÓN: Convertimos a mayúsculas ANTES de validar
        // Así la validación compara "OTAVALO" con lo que hay en la BD.
        $request->merge(['nombre' => strtoupper($request->nombre)]);

        $request->validate([
            'nombre' => 'required|string|max:255|unique:cantones,nombre',
        ], [
            'nombre.required' => 'El nombre del cantón es obligatorio.',
            'nombre.unique'   => 'Este nombre de cantón ya está registrado.',
            'nombre.max'      => 'El nombre no puede superar los 255 caracteres.'
        ]);

        try {
            // Ya no hace falta strtoupper aquí porque lo hicimos en el merge arriba
            Canton::create($request->all());

            return redirect()->route('cantones.index')
                ->with('success', 'Cantón creado correctamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear: ' . $e->getMessage());
        }
    }

// --- EDITAR (SOLUCIÓN AL ERROR 500) ---
    public function edit($id) // Cambiamos (Canton $canton) por ($id)
    {
        // Buscamos manualmente. Si no existe, da error 404 (controlado) en vez de 500.
        $canton = Canton::findOrFail($id); 
        
        $isModal = request()->ajax();
        return view('cantones.canton-edit', compact('canton', 'isModal'));
    }

    // --- ACTUALIZAR ---
public function update(Request $request, $id)
    {
        $canton = Canton::findOrFail($id);

        // Convertimos a mayúsculas ANTES de validar también aquí
        $request->merge(['nombre' => strtoupper($request->nombre)]);

        $request->validate([
            'nombre' => ['required', 'string', 'max:255', Rule::unique('cantones')->ignore($canton->id)],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique'   => 'Ya existe otro cantón con este nombre.'
        ]);

        try {
            $canton->update($request->all());
            return redirect()->route('cantones.index')->with('success', 'Cantón actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    // --- ELIMINAR ---
public function destroy($id)
    {
        $canton = Canton::findOrFail($id);
        
        try {
            if ($canton->parroquias()->exists()) {
                return redirect()->route('cantones.index')->with('error', 'No se puede eliminar: tiene parroquias asociadas.');
            }
            $canton->delete();
            return redirect()->route('cantones.index')->with('success', 'Cantón eliminado.');
        } catch (\Exception $e) {
            return redirect()->route('cantones.index')->with('error', 'Ocurrió un error al intentar eliminar.');
        }
    }
public function show(Canton $canton)
    {
        $canton->load('parroquias');
        return view('cantones.canton-show', compact('canton'));
    }
}