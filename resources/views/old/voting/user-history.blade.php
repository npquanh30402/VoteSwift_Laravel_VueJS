@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col">
            <h1 class="display-6 text-center fw-bold">Voting History</h1>
        </div>
    </div>
    <div class="my-3">
        <div class="row justify-content-center">
            <div class="col-md-2">
                <div class="list-group shadow-sm small mb-3">
                    <div class="list-group-item text-bg-dark">Voting Actions</div>
                    <a href="#" class="list-group-item list-group-item-action" data-bs-toggle="modal"
                       data-bs-target="#add_employee">Create Room</a>
                </div>
                <div class="list-group shadow-sm small mb-3">
                    <div class="list-group-item text-bg-dark">User Actions</div>
                    <a href="{{route('user.friends')}}" class="list-group-item list-group-item-action">Friend List</a>
                    <a href="{{route('information.user')}}" class="list-group-item list-group-item-action">Settings</a>
                    <a href="{{route('user.history')}}" class="list-group-item list-group-item-action active">Voting
                        History</a>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="vstack gap-4">
                            @foreach($room_info as $room)
                                <div class="hstack justify-content-between">
                                    <h1>Room: {{ $room['room_title'] }}</h1>
                                    <a class="btn btn-primary"
                                       href="{{route('vote.main', $room['room_id'])}}">Details</a>
                                </div>

                                <div class="accordion" id="room_{{$room['room_id']}}">
                                    @foreach($organizedData as $data)

                                        @php
                                            $iterationCount = ($votingHistory->currentPage() - 1) * $votingHistory->perPage() + $loop->iteration;
                                        @endphp

                                        @if($data['room_id'] === $room['room_id'])
                                            <div class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#candidate_{{$data['candidate_id']}}"
                                                            aria-expanded="false"
                                                            aria-controls="collapseTwo">
                                                        Q{{$iterationCount}}: &nbsp; <span
                                                            class="fw-bold fs-5">{{ $data['question_title'] }}</span>
                                                    </button>
                                                </h2>
                                                <div id="candidate_{{$data['candidate_id']}}"
                                                     class="accordion-collapse collapse"
                                                     data-bs-parent="#room_{{$room['room_id']}}">
                                                    <div class="accordion-body">
                                                        You picked: <span
                                                            class="fw-bold">{{ $data['candidate_title'] }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        {{$votingHistory->links()}}
                    </div>
                </div>
@endsection
