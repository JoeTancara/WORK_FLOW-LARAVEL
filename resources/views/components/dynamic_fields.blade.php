{{-- resources/views/components/dynamic_fields.blade.php --}}

@php
  // Asegurarnos siempre de que $fields sea un array
  $fields = is_array($fields) ? $fields : [];
@endphp

@foreach($fields as $f)
  {{-- Si no es un array con las claves mínimas, lo saltamos --}}
  @if(!is_array($f) || ! isset($f['name'], $f['label']))
    @continue
  @endif

  <div class="mb-3">
    <label class="form-label">{{ $f['label'] }}</label>

    @if(($f['type'] ?? '') === 'textarea')
      <textarea
        name="{{ $f['name'] }}"
        class="form-control @error($f['name']) is-invalid @enderror"
        @if(!empty($f['required'])) required @endif
      >{{ old($f['name'], $ticket->data[$f['name']] ?? '') }}</textarea>
    @else
      <input
        type="{{ $f['type'] ?? 'text' }}"
        name="{{ $f['name'] }}"
        value="{{ old($f['name'], $ticket->data[$f['name']] ?? '') }}"
        class="form-control @error($f['name']) is-invalid @enderror"
        @if(!empty($f['required'])) required @endif
      >
    @endif

    @error($f['name'])
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>
@endforeach
