@extends('layouts.master')

@php
    $date_format = ($room->timezone === 'UTC') ? 'd/m/Y' : 'm/d/Y';
    $time_format = ($room->timezone === 'UTC') ? 'H:i:s' : 'h:i A';
@endphp
@section('content')
    <div class="row">
        <div class="col">
            <h1 class="display-6 text-center fw-bold">Dashboard: {{$room->room_name}}</h1>
        </div>
    </div>
    <div class="my-3">
        <div class="row justify-content-center">
            <div class="col-md-2">
                <div class="list-group shadow-sm small mb-3">
                    <div class="list-group-item text-bg-dark">Actions</div>
                    <a href="{{route('room.main', $room->id)}}" class="list-group-item list-group-item-action active">Room
                        Details</a>
                    <a href="{{route('question.main', $room->id)}}" class="list-group-item list-group-item-action">Add
                        Questions</a>
                </div>
                <div class="list-group shadow-sm small mb-3">
                    <div class="list-group-item text-bg-dark">Room Actions</div>
                    <a href="#" class="list-group-item list-group-item-action" data-bs-toggle="modal"
                       data-bs-target="#update_room">Settings</a>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-3 overflow-auto">
                    <div class="card-header text-bg-dark text-center">Room Details</div>
                    <div class="card-body">
                        <h5 class="card-title">Room Name: {{$room->room_name}}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Room ID: {{$room->id}}</h6>
                        <p class="card-text">Room Owner: {{$room->user->username}} (You)</p>
                        <p class="card-text">Room
                            Date: {{date($date_format, strtotime($room->start_time))}}
                            - {{date($date_format, strtotime($room->end_time))}}
                        </p>
                        <p class="card-text">Room
                            Hours: {{date($time_format, strtotime($room->start_time))}}
                            - {{date($time_format, strtotime($room->end_time))}}
                        </p>
                        <p class="card-text">Room
                            Timezone: {{$room->timezone}}
                            (GMT{{(new DateTime('now', new DateTimeZone($room->timezone)))->format('P')}})
                        </p>
                        <p class="card-text">Room Link: <code><a
                                    href="{{ route('vote.main', $room->id) }}"
                                    target="_blank">{{ htmlspecialchars(route('vote.main', $room->id)) }}</a></code>
                        </p>
                        <p class="card-text">Result Link: <code><a
                                    href="{{ route('vote.result', $room->id) }}"
                                    target="_blank">{{ htmlspecialchars(route('vote.result', $room->id)) }}</a></code>
                        </p>
                    </div>
                </div>
                <div class="card shadow-sm border-0 mb-3 overflow-auto">
                    <div class="card-header text-bg-dark text-center">Room Description</div>
                    <div class="card-body">
                        <p>{{$room->room_description}}</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Employee list -->

        <!-- Create Room Modal -->
        <div class="modal fade" id="add_employee" data-bs-backdrop="static" tabindex="-1" aria-hidden="true"
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
                                                   value="{{old('start_time')}}" placeholder="Start Time"
                                                   class="form-control form-control-sm">
                                            <label for="start_time">Start Time</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3 form-floating">
                                            <input type="datetime-local" id="end_time" name="end_time"
                                                   value="{{old('end_time')}}" placeholder="End Time"
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
                                                <input type="checkbox" id="allow_multiple_votes"
                                                       class="form-check-input"
                                                       name="allow_multiple_votes">
                                                <label for="allow_multiple_votes">Allow Multiple Votes</label>
                                            </div>

                                            <div class="form-check">
                                                <label for="require_authentication">Require Authentication</label>
                                                <input type="checkbox" id="require_authentication"
                                                       class="form-check-input"
                                                       name="require_authentication">
                                            </div>

                                            <div class="form-check">
                                                <label for="public_visibility">Public Visibility</label>
                                                <input type="checkbox" id="public_visibility" class="form-check-input"
                                                       value="" name="public_visibility">
                                            </div>

                                            <div class="form-group">
                                                <label for="require_password">Password</label>
                                                <input type="text" id="require_password"
                                                       class="form-control"
                                                       name="require_password" placeholder="Null if no password">
                                            </div>

                                            <div class="form-group">
                                                <label for="results_visibility">Results Visibility</label>
                                                <select class="form-select" id="results_visibility"
                                                        name="results_visibility"
                                                        aria-label="Results Visibility">
                                                    <option selected>Open this select menu</option>
                                                    <option value="immediately">Immediately</option>
                                                    <option value="after_voting">After Voting</option>
                                                    <option value="participants_only">Participants Only</option>
                                                    <option value="restricted">Restricted</option>
                                                </select>
                                            </div>

                                            <div class="form-check">
                                                <label for="allow_voting">Public Visibility</label>
                                                <input type="checkbox" id="allow_voting" class="form-check-input"
                                                       value="" name="allow_voting">
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
                                            <button type="reset" class="btn btn-sm btn-secondary" aria-label="Clear">
                                                Clear
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

        <!-- Edit Modal -->
        <div class="modal fade" id="employee_edit" data-bs-backdrop="static" tabindex="-1" aria-hidden="true"
             style="backdrop-filter: blur(5px);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header text-bg-dark">
                        <h5 class="modal-title">Employee Details</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="container">
                                <div class="row gx-3">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <input type="date" class="form-control form-control-sm" required
                                                   value="2009-12-09">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <input type="text" class="form-control form-control-sm"
                                                   placeholder="First Name"
                                                   required value="John">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <input type="text" class="form-control form-control-sm"
                                                   placeholder="Last Name"
                                                   required value="Doe">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <input type="email" class="form-control form-control-sm"
                                                   placeholder="Email ID"
                                                   required value="johndoe@gmail.com">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <input type="tel" class="form-control form-control-sm"
                                                   placeholder="Phone Number" required value="7418529632">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <select class="form-select form-select-sm" aria-label="Job Assigned">
                                                <option>Select job for employee</option>
                                                <option value="Graphic Designer" selected>Graphic Designer</option>
                                                <option value="Web Designer">Web Designer</option>
                                                <option value="Web Developer">Web Developer</option>
                                                <option value="Digital Marketer">Digital Marketer</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-sm btn-success">Update Employee
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-sm btn-warning" data-bs-dismiss="modal"
                                                    aria-label="Close">Discard
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
        <!-- Edit Modal -->

        <!-- Update Room Modal -->
        <div class="modal fade" id="update_room" data-bs-backdrop="static" tabindex="-1" aria-hidden="true"
             style="backdrop-filter: blur(5px);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header text-bg-dark">
                        <h5 class="modal-title">Update the room settings</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('room.update', $room->id)}}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="container">
                                <div class="row gx-3">
                                    <div class="col-md-12">
                                        <div class="mb-3 form-floating">
                                            <input type="text" id="room_name" name="room_name"
                                                   class="form-control form-control-sm" value="{{$room->room_name}}"
                                                   placeholder="Room Name"
                                                   required>
                                            <label for="room_name">Room Name</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3 form-floating">
                                            <input type="datetime-local" id="start_time" name="start_time"
                                                   placeholder="Start Time"
                                                   value="{{$room->start_time}}"
                                                   class="form-control form-control-sm">
                                            <label for="start_time">Start Time</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3 form-floating">
                                            <input type="datetime-local" id="end_time" name="end_time"
                                                   placeholder="End Time"
                                                   value="{{$room->end_time}}"
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
                                                        value="{{$key}}" {{$key == $room->timezone ? 'selected' : ''}}>{{$key}}
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
                                                  style="height: 10rem">{{$room->room_description}}</textarea>
                                            <label for="room_description">Room Description</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" id="allow_multiple_votes"
                                                       class="form-check-input"
                                                       name="allow_multiple_votes" {{$settings->allow_multiple_votes ? 'checked' : ''}}>
                                                <label for="allow_multiple_votes">Allow Multiple Votes</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <label for="public_visibility">Public Visibility</label>
                                                <input type="checkbox" id="public_visibility" class="form-check-input"
                                                       name="public_visibility" {{$settings->public_visibility ? 'checked' : ''}}>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3 form-floating">
                                            <select class="form-select" id="results_visibility"
                                                    name="results_visibility" aria-label="Results Visibility">
                                                <option
                                                    value="after_voting" {{ $settings->results_visibility === 'after_voting' ? 'selected' : '' }}>
                                                    After Voting
                                                </option>
                                                <option
                                                    value="restricted" {{ $settings->results_visibility === 'restricted' ? 'selected' : '' }}>
                                                    Restricted
                                                </option>
                                            </select>
                                            <label for="results_visibility">Results Visibility</label>
                                        </div>

                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3 form-floating">
                                            <input type="password" id="require_password" name="require_password"
                                                   class="form-control form-control-sm" placeholder="Password">
                                            <label for="require_password">Password</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <label for="allow_voting">Allow Voting</label>
                                                <input type="checkbox" id="allow_voting" class="form-check-input"
                                                       name="allow_voting" {{$settings->allow_voting ? 'checked' : ''}}>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <label for="allow_skipping">Allow Skipping</label>
                                                <input type="checkbox" id="allow_skipping" class="form-check-input"
                                                       name="allow_skipping" {{$settings->allow_skipping ? 'checked' : ''}}>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <label for="allow_anonymous_voting">Allow Anonymous Voting</label>
                                                <input type="checkbox" id="allow_anonymous_voting"
                                                       class="form-check-input"
                                                       name="allow_anonymous_voting" {{$settings->allow_anonymous_voting ? 'checked' : ''}}>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-sm btn-success">Update Room</button>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="d-grid">
                                            <button type="reset" class="btn btn-sm btn-secondary" aria-label="Clear">
                                                Clear
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
        <!-- Update Room Modal -->

@endsection
