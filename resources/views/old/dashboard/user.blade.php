@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col">
            <h1 class="display-6 text-center fw-bold">User Dashboard</h1>
        </div>
    </div>
    <div class="my-3">
        <div class="row justify-content-center">
            <div class="col-md-2">
                <div class="list-group shadow-sm small mb-3">
                    <div class="list-group-item text-bg-dark">Voting Actions</div>
                    <a href="#" class="list-group-item list-group-item-action" data-bs-toggle="modal"
                       data-bs-target="#add_room">Create Room</a>
                </div>
                <div class="list-group shadow-sm small mb-3">
                    <div class="list-group-item text-bg-dark">User Actions</div>
                    <a href="{{route('user.friends')}}" class="list-group-item list-group-item-action">Friend List</a>
                    <a href="{{route('information.user')}}" class="list-group-item list-group-item-action">Settings</a>
                    <a href="{{route('user.history')}}" class="list-group-item list-group-item-action">Voting
                        History</a>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-3 overflow-auto">
                    <div class="card-header text-bg-dark text-center">Room List</div>
                    <table class="table table-sm small table-bordered table align-middle mb-0">
                        <tr class="table-secondary">
                            <th>ID</th>
                            <th>Room Name</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Timezone</th>
                            <th>Details</th>
                            <th>Delete</th>
                        </tr>
                        @foreach($rooms as $room)
                            <tr>
                                <td>{{$room->id}}</td>
                                <td>{{$room->room_name}}</td>
                                <td>{{$room->start_time}}</td>
                                <td>{{$room->end_time}}</td>
                                <td>{{$room->timezone}}
                                    (GMT{{(new DateTime('now', new DateTimeZone($room->timezone)))->format('P')}})
                                </td>
                                <td>
                                    <div class="d-grid">
                                        <a href="{{route('room.main', $room->id)}}" class="btn btn-sm btn-primary">Details</a>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-grid">
                                        <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                           data-bs-target="#room_delete_{{$room->id}}">Delete</a>
                                    </div>
                                </td>
                            </tr>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="room_delete_{{$room->id}}" data-bs-backdrop="static"
                                 tabindex="-1"
                                 aria-hidden="true"
                                 style="backdrop-filter: blur(5px);">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div
                                            class="modal-body text-center d-flex justify-content-between align-items-center p-3">
                                            <h4 class="fw-light m-0">Do you want to delete
                                                <strong><q>{{$room->room_name}}</q></strong> ?</h4>
                                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                        </div>
                                        <div class="modal-footer d-flex justify-content-center">
                                            <form action="{{route('room.delete', $room->id)}}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">YES!</button>
                                            </form>
                                            <button class="btn btn-sm btn-warning" data-bs-dismiss="modal"
                                                    aria-label="Close">
                                                Nope, Go back
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </table>

                </div>
                <div class="mt-3 ms-auto d-flex justify-content-end">
                    {{$rooms->links()}}
                </div>
            </div>
        </div>
    </div>

    <!-- Create Room Modal -->
    <div class="modal fade" id="add_room" data-bs-backdrop="static" tabindex="-1" aria-hidden="true"
         style="backdrop-filter: blur(5px);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-bg-dark">
                    <h5 class="modal-title">Create a Voting Room</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('room.store')}}" method="POST">
                        @csrf
                        <div class="container">
                            <div class="row gx-3">
                                <div class="col-md-12">
                                    <div class="mb-3 form-floating">
                                        <input type="text" id="room_name" name="room_name"
                                               class="form-control form-control-sm" value="{{old('room_name')}}"
                                               placeholder="Room Name"
                                               required>
                                        <label for="room_name">Room Name</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 form-floating">
                                        <input type="datetime-local" id="start_time" name="start_time"
                                               placeholder="Start Time"
                                               class="form-control form-control-sm">
                                        <label for="start_time">Start Time</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 form-floating">
                                        <input type="datetime-local" id="end_time" name="end_time"
                                               placeholder="End Time"
                                               class="form-control form-control-sm">
                                        <label for="start_time">End Time</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3 form-floating">
                                        <select class="form-select" id="timezone"
                                                name="timezone"
                                                aria-label="Timezone" placeholder=Timezone">
                                            @foreach($timezones_with_offset as $key => $timezone)
                                                <option
                                                    value="{{$key}}" {{$key == old('timezone') ? 'selected' : ''}}>{{$key}}
                                                    : GMT{{$timezone}}</option>
                                            @endforeach
                                        </select>
                                        <label for="Timezone">Timezone</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3 form-floating">
                                        <textarea id="room_description" name="room_description"
                                                  class="form-control form-control-sm"
                                                  style="height: 10rem">{{old('room_description')}}</textarea>
                                        <label for="room_description">Room Description</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" id="allow_multiple_votes" class="form-check-input"
                                                   name="allow_multiple_votes">
                                            <label for="allow_multiple_votes">Allow Multiple Votes</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <label for="public_visibility">Public Visibility</label>
                                            <input type="checkbox" id="public_visibility" class="form-check-input"
                                                   name="public_visibility">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3 form-floating">
                                        <select class="form-select" id="results_visibility"
                                                name="results_visibility"
                                                aria-label="Results Visibility" placeholder="Results Visibility">
                                            <option value="after_voting">After Voting</option>
                                            <option value="restricted" selected>Restricted</option>
                                        </select>
                                        <label for="results_visibility">Results Visibility</label>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3 form-floating">
                                        <input type="text" id="require_password" name="require_password"
                                               class="form-control form-control-sm" placeholder="Password">
                                        <label for="require_password">Password</label>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <label for="allow_voting">Allow Voting</label>
                                            <input type="checkbox" id="allow_voting" class="form-check-input"
                                                   name="allow_voting" checked>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <label for="allow_skipping">Allow Skipping</label>
                                            <input type="checkbox" id="allow_skipping" class="form-check-input"
                                                   name="allow_skipping">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <label for="allow_anonymous_voting">Allow Anonymous Voting</label>
                                            <input type="checkbox" id="allow_anonymous_voting" class="form-check-input"
                                                   name="allow_anonymous_voting">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-sm btn-success">Create Room</button>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="d-grid">
                                        <button type="reset" class="btn btn-sm btn-secondary" aria-label="Clear">Clear
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-sm btn-warning" data-bs-dismiss="modal"
                                                aria-label="Close">Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Create Room Modal -->

@endsection
