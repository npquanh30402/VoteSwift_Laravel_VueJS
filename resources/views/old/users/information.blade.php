@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col">
            <h1 class="display-6 text-center fw-bold">User Dashboard <a href="{{route('user.profile', $user->id)}}" class="btn btn-sm btn-primary">To Profile Page</a></h1>
        </div>
    </div>
    <div class="my-3">
        <div class="row justify-content-center">
            <div class="col-md-2">
                <div class="list-group shadow-sm small mb-3">
                    <div class="list-group-item text-bg-dark">Voting data</div>
                    <a href="#" class="list-group-item list-group-item-action" data-bs-toggle="modal"
                       data-bs-target="#add_employee">Create Room</a>
                </div>
                <div class="list-group shadow-sm small mb-3">
                    <div class="list-group-item text-bg-dark">User Actions</div>
                    <a href="#" class="list-group-item list-group-item-action active">Settings</a>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-3 overflow-auto">
                    <div class="card-header text-bg-dark text-center">User Information</div>
                    <div class="card-body">
                        <form action="{{route('store.information.user')}}" method="POST"
                              class="d-flex flex-column gap-3" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row g-4">
                                <div class="form-group col-md-6 d-flex flex-column">
                                    <label class="form-label" for="username">Username:</label>
                                    <input type="text" class="form-control" id="username" value="{{ $user->username }}"
                                           disabled>
                                </div>
                                <div class="form-group col-md-6 d-flex flex-column">
                                    <label class="form-label" for="email">Email:</label>
                                    <input type="email" class="form-control" id="email"
                                           value="{{ $user->email }}" disabled>
                                </div>
                                <div class="form-group col-md-6 d-flex flex-column">
                                    <label class="form-label" for="first_name">First Name:</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name"
                                           value="{{$user->first_name}}">
                                    @error('first_name')
                                    <p class="m-0 small text-danger">{{$message}}</p>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6 d-flex flex-column">
                                    <label class="form-label" for="last_name">Last Name:</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name"
                                           value="{{$user->last_name}}">
                                    @error('last_name')
                                    <p class="m-0 small text-danger">{{$message}}</p>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4 d-flex flex-column">
                                    <label class="form-label" for="phone">Phone:</label>
                                    <input type="text" class="form-control" id="phone" name="phone"
                                           value="{{$user->phone}}">
                                    @error('phone')
                                    <p class="m-0 small text-danger">{{$message}}</p>
                                    @enderror
                                </div>
                                <div class="form-group col-md-8 d-flex flex-column">
                                    <label class="form-label" for="address">Address:</label>
                                    <input type="text" class="form-control" id="address" name="address"
                                           value="{{$user->address}}">
                                    @error('address')
                                    <p class="m-0 small text-danger">{{$message}}</p>
                                    @enderror
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="form-group col-md-4 d-flex justify-content-center">
                                        <img src="{{$user->avatar}}"
                                             class="rounded-circle img-fluid"
                                             style="width: 10rem;"
                                             alt="Avatar"/>
                                    </div>
                                    <div class="form-group col-md-8 d-flex flex-column">
                                        <label class="form-label" for="avatar">Avatar:</label>
                                        <input type="file" class="form-control" id="avatar" name="avatar"
                                               value="">
                                        @error('avatar')
                                        <p class="m-0 small text-danger">{{$message}}</p>
                                        @enderror
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary w-25 ms-auto me-3">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
