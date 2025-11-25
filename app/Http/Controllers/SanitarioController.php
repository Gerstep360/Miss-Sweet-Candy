<?php

namespace App\Http\Controllers;

use App\Models\SanitarioLista;
use App\Models\SanitarioRespuesta;
use App\Models\SanitarioTemperatura;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SanitarioController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        try {
            $this->authorize('ver-sanitario');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }
        $listas = SanitarioLista::all();
        return view('sanitario.index', compact('listas'));
    }

    public function show(SanitarioLista $lista)
    {
        try {
            $this->authorize('ver-sanitario');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }
        $lista->load('items');
        return view('sanitario.show', compact('lista'));
    }

    public function store(Request $request, SanitarioLista $lista)
    {
        try {
            $this->authorize('registrar-sanitario');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }
        $request->validate([
            'respuestas' => 'required|array',
            'respuestas.*.item_id' => 'required|exists:sanitario_items,id',
            // Validation for values depends on item type, handled loosely here or could be more specific
        ]);

        DB::beginTransaction();
        try {
            $userId = Auth::id();

            foreach ($request->respuestas as $itemId => $data) {
                $respuesta = new SanitarioRespuesta;
                $respuesta->lista_id = $lista->id;
                $respuesta->item_id = $itemId;
                $respuesta->usuario_id = $userId;

                if (isset($data['check'])) {
                    $respuesta->valor_check = $data['check'] == '1' || $data['check'] == 'on';
                }
                if (isset($data['numero'])) {
                    $respuesta->valor_numero = $data['numero'];
                }
                if (isset($data['texto'])) {
                    $respuesta->valor_texto = $data['texto'];
                }

                // Handle file upload
                if ($request->hasFile("respuestas.{$itemId}.foto")) {
                    $path = $request->file("respuestas.{$itemId}.foto")->store('sanitario_evidencias', 'public');
                    $respuesta->foto_ruta = $path;
                }

                $respuesta->save();
            }

            BitacoraController::registrar(
                'Completó lista sanitaria',
                'SanitarioLista',
                $lista->id,
                $userId,
                $request
            );

            DB::commit();

            return redirect()->route('sanitario.index')->with('success', 'Lista guardada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error al guardar: '.$e->getMessage());
        }
    }

    public function temperaturas()
    {
        try {
            $this->authorize('ver-sanitario');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }
        $ultimas = SanitarioTemperatura::with('usuario')->latest('id')->take(10)->get();

        return view('sanitario.temperaturas', compact('ultimas'));
    }

    public function storeTemperatura(Request $request)
    {
        try {
            $this->authorize('registrar-sanitario');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }
        $request->validate([
            'equipo' => 'required|string|max:80',
            'temperatura' => 'required|numeric|between:-50,100',
        ]);

        $temp = SanitarioTemperatura::create([
            'equipo' => $request->equipo,
            'temperatura' => $request->temperatura,
            'usuario_id' => Auth::id(),
        ]);

        BitacoraController::registrar(
            'Registró temperatura',
            'SanitarioTemperatura',
            $temp->id,
            Auth::id(),
            $request
        );

        return redirect()->route('sanitario.temperaturas')->with('success', 'Temperatura registrada.');
    }

    public function publico()
    {
        $listas = SanitarioLista::with(['items', 'respuestas' => function($q) {
            $q->latest()->limit(1);
        }])->get();

        $evidencias = SanitarioRespuesta::whereNotNull('foto_ruta')
            ->with(['lista', 'usuario'])
            ->latest()
            ->take(6)
            ->get();

        $temperaturas = SanitarioTemperatura::with('usuario')->latest()->take(5)->get();

        return view('sanitario.publico', compact('listas', 'evidencias', 'temperaturas'));
    }

    public function historial(SanitarioLista $lista)
    {
        // Group responses by created_at (approximate submission time) to show as "records"
        // This is a bit tricky without a submission_id, but grouping by minute should work for now
        $registros = $lista->respuestas()
            ->with('usuario')
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d %H:%i") as fecha_hora'), 'usuario_id', 'created_at')
            ->groupBy('fecha_hora', 'usuario_id', 'created_at')
            ->orderByDesc('fecha_hora')
            ->get()
            ->unique('fecha_hora'); // Get unique submission instances

        return view('sanitario.historial', compact('lista', 'registros'));
    }
}
