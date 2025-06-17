@extends('layouts.app')
@section('title', "Ticket #{$ticket->id}")
@section('content')
<div class="container">
  {{-- Mensajes flash --}}
  @if(session('status'))
    <div class="alert alert-info">
      {{ session('status') }}
    </div>
  @endif

  <h1>Ticket #{{ $ticket->id }}</h1>
  <p><strong>Proceso:</strong> {{ $ticket->flow->process->name }}</p>
  <p><strong>Actividad actual:</strong> {{ $ticket->currentActivity->name }}</p>

  @if($ticket->status === 'completed')
    <div class="alert alert-success">✅ Proceso completado.</div>
  @else
    <form action="{{ route('tickets.advance', $ticket->id) }}" method="POST">
      @csrf

      {{-- Campos dinámicos --}}
      @php
        $fields = is_array($ticket->currentActivity->config['fields'] ?? null)
                  ? $ticket->currentActivity->config['fields']
                  : [];
      @endphp
      @include('components.dynamic_fields', ['fields'=>$fields, 'ticket'=>$ticket])

      {{-- Dropdown de decisión --}}
      @if(! empty($ticket->currentActivity->config['decision']))
        <div class="mb-3">
          <label class="form-label">Decisión</label>
          <select name="approved" class="form-control">
            <option value="0" selected>En proceso</option>
            <option value="1">Aprobado</option>
          </select>
        </div>
      @endif

      <button class="btn btn-primary">Avanzar</button>
    </form>
  @endif
</div>
@endsection
