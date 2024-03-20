@php use Carbon\Carbon; @endphp
@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col mb-3">
            <h1 class="display-6 text-center fw-bold">Public Voting Room</h1>
        </div>

    </div>
    @php
        $startRoomNumber = ($paginator->currentPage() - 1) * $paginator->perPage() + 1;
    @endphp
    <div class="row row-cols-3 gy-4 mb-4">
        @foreach($public_rooms as $room)
            <div class="d-flex align-items-stretch">
                <div class="card text-center">
                    <div class="card-header">
                        Room #{{ $startRoomNumber++ }}
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{$room->room_name}}</h5>
                        <p class="card-text text-truncate">{{$room->room_description}}</p>
                        <a href="{{route('vote.main', $room->id)}}" class="btn btn-primary">Enter</a>
                    </div>
                    <div class="card-footer text-body-secondary">
                        {{Carbon::parse($room->created_at)->diffForHumans()}}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="d-flex justify-content-end">
        {{ $paginator->links() }}
    </div>
@endsection
