@extends('layouts.app')

@section('title','Mis Tickets')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1>Mis Tickets</h1>
  <a href="{{ route('tickets.create') }}" class="btn btn-primary">Iniciar nuevo</a>
</div>

<table class="table table-bordered">
  <thead>
    <tr>
      <th>ID</th>
      <th>Proceso</th>
      <th>Actividad</th>
      <th>Estado</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    @forelse($tickets as $t)
    <tr>
      <td>{{ $t->id }}</td>
      <td>{{ $t->flow->process->name }}</td>
      <td>{{ $t->currentActivity->name }}</td>
      <td>{{ ucfirst($t->status) }}</td>
      <td>
        <a href="{{ route('tickets.show', $t->id) }}" class="btn btn-sm btn-secondary">Ver</a>
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="5" class="text-center">No hay tickets aún</td>
    </tr>
    @endforelse
  </tbody>
</table>
@endsection
