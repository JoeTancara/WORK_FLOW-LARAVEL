@extends('layouts.app')
@section('title','Login')
@section('content')
<div class="container">
  <h1>Login</h1>
  <form method="POST" action="{{ route('login') }}">
    @csrf
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
    <div class="mb-3 form-check">
      <input type="checkbox" name="remember" class="form-check-input" id="remember">
      <label class="form-check-label" for="remember">Remember Me</label>
    </div>
    <button class="btn btn-primary">Login</button>
  </form>
</div>
@endsection
