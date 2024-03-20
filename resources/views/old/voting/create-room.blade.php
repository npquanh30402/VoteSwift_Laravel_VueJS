@extends('layouts.master')

@section('content')
    <h1 class="text-center mt-3">Create a Voting Room</h1>

    <div class="row justify-content-center my-5">
        <form action="{{route('room.store')}}" method="POST"
              class="col-md-6 shadow p-5 border rounded d-flex flex-column gap-3">
            @csrf

            <div class="form-group">
                <label for="room_name">Room Name:</label>
                <input type="text" id="room_name" name="room_name" class="form-control" value="{{old('room_name')}}"
                       required>
                @error('room_name')
                <p class="m-0 small text-danger">{{$message}}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="room_description">Room Description:</label>
                <textarea id="room_description" name="room_description" rows="4"
                          class="form-control">{{old('room_description')}}</textarea>
            </div>

            <div class="row">
                <div class="form-group col-md-6">
                    <label for="start_time">Start Time:</label>
                    <input type="datetime-local" id="start_time" name="start_time" value="{{old('start_time')}}"
                           class="form-control">
                </div>

                <div class="form-group col-md-6">
                    <label for="end_time">End Time:</label>
                    <input type="datetime-local" id="end_time" name="end_time" value="{{old('end_time')}}"
                           class="form-control">
                </div>
            </div>

            <div class="form-check">
                <label for="allow_multiple_votes">Allow Multiple Votes:</label>
                <input type="checkbox" id="allow_multiple_votes" class="form-check-input" name="allow_multiple_votes">
            </div>

            <div class="form-check">
                <label for="public_visibility">Public Visibility:</label>
                <input type="checkbox" id="public_visibility" class="form-check-input"
                       value="{{old('public_visibility')}}" name="public_visibility">
            </div>

            <div class="form-check">
                <label for="require_authentication">Require Authentication:</label>
                <input type="checkbox" id="require_authentication" class="form-check-input"
                       name="require_authentication">
            </div>

            <button type="submit" class="btn btn-primary">Create Room</button>
        </form>
    </div>
@endsection
