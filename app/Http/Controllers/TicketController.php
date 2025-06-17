<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Services\BPM\BpmEngine;

class TicketController extends Controller
{
    protected BpmEngine $engine;

    public function __construct(BpmEngine $engine)
    {
        //$this->middleware('auth');
        $this->engine = $engine;
    }

    // Listado de tickets
    public function index()
    {
        $user = auth()->user();
        $tickets = $user->hasRole('admin')
            ? Ticket::with('flow.process','currentActivity')->get()
            : $user->tickets()->with('flow.process','currentActivity')->get();

        return view('tickets.index', compact('tickets'));
    }

    // Formulario para iniciar un ticket (lista procesos activos)
    public function create()
    {
        $processes = \App\Models\Process::with('flows')
                          ->whereHas('flows', fn($q)=> $q->where('active', true))
                          ->get();
        return view('tickets.create', compact('processes'));
    }

    // Iniciar ticket
    public function store(Request $request)
    {
        $request->validate(['flow_id'=>'required|exists:flows,id']);
        $ticket = $this->engine->start($request->flow_id, auth()->id());
        return redirect()->route('tickets.show', $ticket->id);
    }

    // Ver estado y formulario de la actividad actual
    public function show(int $id)
    {
        $ticket = Ticket::with('flow.process','currentActivity')->findOrFail($id);
        return view('tickets.show', compact('ticket'));
    }

    // Avanzar ticket con datos del formulario
    // app/Http/Controllers/TicketController.php

    public function advance(Request $request, int $id)
    {
        $ticket = Ticket::findOrFail($id);
        $config = $ticket->currentActivity->config;
        $fields = is_array($config['fields'] ?? null) ? $config['fields'] : [];

        // 1) Construir reglas dinámicas
        $rules = collect($fields)
            ->filter(fn($f) => is_array($f) && isset($f['name']))
            ->mapWithKeys(fn($f) => [
                $f['name'] => $f['rules']
                    ?? (($f['required'] ?? false) ? 'required' : 'nullable')
            ])->toArray();

        if (! empty($config['decision'])) {
            $rules['approved'] = 'required|boolean';
        }

        $validated = $request->validate($rules);

        // --- AQUÍ: si selecciona "En proceso" redirigimos a tickets.index ---
        if (isset($validated['approved']) && $validated['approved'] === false) {
            $ticket->update([
                'data'   => array_merge($ticket->data ?? [], $validated),
                'status' => 'open',
            ]);

            return redirect()
                ->route('tickets.index')
                ->with('status', '✅ Ticket guardado en proceso.');
        }

        // 2) Si es aprobado, delegamos al BPM
        $ticket = $this->engine->advance($ticket, $validated);

        $message = $ticket->status === 'completed'
            ? '🎉 Proceso completado exitosamente.'
            : '✅ Ticket avanzado a la siguiente actividad.';

        return redirect()
            ->route('tickets.show', $ticket->id)
            ->with('status', $message);
    }

}
