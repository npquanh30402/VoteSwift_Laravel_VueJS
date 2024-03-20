@extends('layouts.master')

@section('content')
    <h1 class="text-center mt-3">Register</h1>
    <div class="row justify-content-center my-5">
        <form action="{{route('register.store')}}" method="POST" class="col-md-6 shadow p-5 border rounded">
            @csrf
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" value="{{old('username')}}" name="username" id="username">
                @error('username')
                <p class="m-0 small text-danger">{{$message}}</p>
                @enderror
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" value="{{old('email')}}" name="email" id="email">
                <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
                @error('email')
                <p class="m-0 small text-danger">{{$message}}</p>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" name="password" id="password">
                @error('password')
                <p class="m-0 small text-danger">{{$message}}</p>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation">
                @error('password_confirmation')
                <p class="m-0 small text-danger">{{$message}}</p>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>
@endsection
