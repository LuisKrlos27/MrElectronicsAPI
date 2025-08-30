<?php

namespace App\Http\Controllers;

use App\Models\Tipo;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Pulgada;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $producto = Producto::all();
        $tipo = Tipo::all();
        $pulgada = Pulgada::all();
        $marca = Marca::all();
        $modelo = Modelo::all();

        return view('Productos.ProductosIndex', compact('producto','tipo', 'pulgada', 'marca', 'modelo'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tipo = Tipo::all();
        $pulgada = Pulgada::all();
        $marca = Marca::all();
        $modelo = Modelo::all();

        return view('Productos.ProductosForm', compact('tipo','pulgada','marca','modelo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'precio'   => 'required|numeric',
            'cantidad' => 'required|integer',
        ]);

        // Validar o crear Tipo
        if ($request->tipo_id === 'nuevo') {
            $tipoExistente = Tipo::whereRaw('LOWER(nombre) = ?', [strtolower($request->nuevo_tipo)])->first();
            if ($tipoExistente) {
                return back()->withInput()->with('error', 'El tipo ya existe.');
            }
            $tipo = Tipo::create(['nombre' => $request->nuevo_tipo]);
            $tipo_id = $tipo->id;
        } else {
            $tipo_id = $request->tipo_id;
        }

        // Validar o crear Pulgada
        if ($request->pulgada_id === 'nuevo') {
            $pulgadaExistente = Pulgada::whereRaw('LOWER(medida) = ?', [strtolower($request->nueva_pulgada)])->first();
            if ($pulgadaExistente) {
                return back()->withInput()->with('error', 'La medida de pulgadas ya existe.');
            }
            $pulgada = Pulgada::create(['medida' => $request->nueva_pulgada]);
            $pulgada_id = $pulgada->id;
        } else {
            $pulgada_id = $request->pulgada_id;
        }

        // Validar o crear Marca
        if ($request->marca_id === 'nueva') {
            $marcaExistente = Marca::whereRaw('LOWER(nombre) = ?', [strtolower($request->nueva_marca)])->first();
            if ($marcaExistente) {
                return back()->withInput()->with('error', 'La marca ya existe.');
            }
            $marca = Marca::create(['nombre' => $request->nueva_marca]);
            $marca_id = $marca->id;
        } else {
            $marca_id = $request->marca_id;
        }

        // Validar o crear Modelo
        if ($request->modelo_id === 'nuevo') {
            $modeloExistente = Modelo::whereRaw('LOWER(nombre) = ?', [strtolower($request->nuevo_modelo)])
                ->where('marca_id', $marca_id)
                ->first();
            if ($modeloExistente) {
                return back()->withInput()->with('error', 'El modelo ya existe para esta marca.');
            }
            $modelo = Modelo::create([
                'nombre'   => $request->nuevo_modelo,
                'marca_id' => $marca_id
            ]);
            $modelo_id = $modelo->id;
        } else {
            $modelo_id = $request->modelo_id;
        }

        // Crear producto
        Producto::create([
            'tipo_id'     => $tipo_id,
            'pulgada_id'  => $pulgada_id,
            'marca_id'    => $marca_id,
            'modelo_id'   => $modelo_id,
            'precio'      => $request->precio,
            'cantidad'    => $request->cantidad,
            'numero_pieza'=> $request->numero_pieza,
            'descripcion' => $request->descripcion
        ]);

        return redirect()->route('productos.index')->with('success', 'Producto registrado correctamente.');
    }



    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $producto = Producto::findOrFail($id);

        // Listas para selects
        $tipo = Tipo::orderBy('nombre')->get();
        $pulgada = Pulgada::orderBy('medida')->get();
        $marca = Marca::orderBy('nombre')->get();
        $modelo = Modelo::orderBy('nombre')->get();

        return view('Productos.ProductosEdit', compact('producto', 'tipo', 'pulgada', 'marca', 'modelo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'tipo_id'     => 'required|exists:tipos,id',
            'pulgada_id'  => 'required|exists:pulgadas,id',
            'marca_id'    => 'required|exists:marcas,id',
            'modelo_id'   => 'required|exists:modelos,id',
            'precio'      => 'required|numeric|min:0',
            'cantidad'    => 'required|integer|min:0',
            'numero_pieza'=> 'nullable|string|max:100',
            'descripcion' => 'nullable|string',
        ]);

        // Validar que no exista otro producto igual
        $duplicado = Producto::where('tipo_id', $request->tipo_id)
            ->where('pulgada_id', $request->pulgada_id)
            ->where('marca_id', $request->marca_id)
            ->where('modelo_id', $request->modelo_id)
            ->where('id', '!=', $producto->id) // ignorar el actual
            ->first();

        if ($duplicado) {
            return back()->withInput()->with('error', 'Ya existe un producto con estas características.');
        }

        // Actualizar producto
        $producto->update([
            'tipo_id'      => $request->tipo_id,
            'pulgada_id'   => $request->pulgada_id,
            'marca_id'     => $request->marca_id,
            'modelo_id'    => $request->modelo_id,
            'precio'       => $request->precio,
            'cantidad'     => $request->cantidad,
            'numero_pieza' => $request->numero_pieza,
            'descripcion'  => $request->descripcion,
        ]);

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto)
    {
        $producto->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente.');


    }
}
