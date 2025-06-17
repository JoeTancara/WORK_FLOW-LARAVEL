@extends('layouts.app')
@section('content')
<div class="container">
  <h1>Iniciar Ticket</h1>
  <form action="{{ route('tickets.store') }}" method="POST">
    @csrf
    <div class="mb-3">
      <label>Proceso</label>
      <select name="flow_id" class="form-control" required>
        @foreach($processes as $p)
          @foreach($p->flows as $f)
            @if($f->active)
              <option value="{{ $f->id }}">
                {{ $p->name }} (v{{ $f->version }})
              </option>
            @endif
          @endforeach
        @endforeach
      </select>
    </div>
    <button class="btn btn-success">Iniciar</button>
  </form>
</div>
@endsection
