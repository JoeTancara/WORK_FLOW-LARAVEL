@extends('layouts.app')
@section('title','Register')
@section('content')
<div class="container">
  <h1>Register</h1>
  <form method="POST" action="{{ route('register') }}">
    @csrf
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input name="name" value="{{ old('name') }}"
             class="form-control @error('name') is-invalid @enderror">
      @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" value="{{ old('email') }}"
             class="form-control @error('email') is-invalid @enderror">
      @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password"
             class="form-control @error('password') is-invalid @enderror">
      @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Confirm Password</label>
      <input type="password" name="password_confirmation" class="form-control">
    </div>
    <button class="btn btn-primary">Register</button>
  </form>
</div>
@endsection
