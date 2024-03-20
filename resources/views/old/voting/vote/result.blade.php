@extends('layouts.master')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <div class="row">
        <div class="col">
            <h1 class="display-6 text-center fw-bold">Result</h1>
        </div>
    </div>
    <div class="my-3">
        <div class="row justify-content-center">
            <div class="col-md-2">
                <div class="list-group shadow-sm small mb-3">
                    <div class="list-group-item text-bg-dark">Vote Option</div>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between"
                       data-bs-toggle="modal"
                       data-bs-target="#user_has_voted">Who has voted
                        @if($user_has_voted_ids->count())
                            <span
                                class="badge text-bg-danger ms-auto">{{$user_has_voted_ids->count()}}</span>
                        @endif
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between"
                       data-bs-toggle="modal"
                       data-bs-target="#user_has_skipped">Who has skipped
                        @if(false)
                            <span class="badge text-bg-danger ms-auto">1</span>
                        @endif
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between"
                       data-bs-toggle="modal"
                       data-bs-target="#user_choice">Your choices
                        @if($user_choices->count())
                            <span class="badge text-bg-danger ms-auto">{{$user_choices->count()}}</span>
                        @endif
                    </a>
                </div>
            </div>
            <div class="col-md-8 row">
                <h1>Room: {{$room->id}} - {{ $room->room_name }}</h1>
                @for($i = 0,$iMax = count($nestedResults); $i < $iMax; $i++)
                    <div class="col-md-6 mt-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Q{{$i + 1}}: {{$nestedResults[$i]['question_title']}}</h5>
                                <p class="card-text">So far, {{array_sum($nestedResults[$i]['vote_counts'])}} votes.</p>
                                @php
                                    $maxVotesIndex = array_search(max($nestedResults[$i]['vote_counts']), $nestedResults[$i]['vote_counts'], true);
                                    $winnerOption = $nestedResults[$i]['candidates'][$maxVotesIndex];
                                @endphp
                                <p class="card-text">Winner: {{$winnerOption}}</p>
                                <canvas id="myChart_{{$i}}"></canvas>
                            </div>
                        </div>
                    </div>
                    <script>
                        const ctx_{{$i}} = document.getElementById('myChart_{{$i}}');

                        new Chart(ctx_{{$i}}, {
                            type: 'bar',
                            data: {
                                labels: {!! json_encode($nestedResults[$i]['candidates'], JSON_THROW_ON_ERROR) !!},
                                datasets: [{
                                    label: '# of Votes',
                                    data: {!! json_encode($nestedResults[$i]['vote_counts'], JSON_THROW_ON_ERROR) !!},
                                    borderWidth: 1,
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.2)',
                                        'rgba(255, 159, 64, 0.2)',
                                        'rgba(255, 205, 86, 0.2)',
                                        'rgba(75, 192, 192, 0.2)',
                                        'rgba(54, 162, 235, 0.2)',
                                        'rgba(153, 102, 255, 0.2)',
                                        'rgba(201, 203, 207, 0.2)'
                                    ],
                                    borderColor: [
                                        'rgb(255, 99, 132)',
                                        'rgb(255, 159, 64)',
                                        'rgb(255, 205, 86)',
                                        'rgb(75, 192, 192)',
                                        'rgb(54, 162, 235)',
                                        'rgb(153, 102, 255)',
                                        'rgb(201, 203, 207)'
                                    ],
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    </script>
                @endfor
            </div>
        </div>
    </div>

    <!-- User Has Voted Modal -->
    <div class="modal fade" id="user_has_voted" data-bs-backdrop="static" tabindex="-1" aria-hidden="true"
         style="backdrop-filter: blur(5px);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-bg-dark">
                    <h5 class="modal-title">Users has Voted</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        @if(!$user_has_voted_ids->isEmpty())
                            @foreach($user_has_voted_ids as $user)
                                <div class="card shadow-sm border-0 mb-3 overflow-auto">
                                    <div
                                        class="card-header text-bg-dark text-center d-flex justify-content-between align-items-center">
                                        <div class="hstack justify-content-center">
                                            <img src="{{asset($user->avatar)}}" class="img-fluid rounded-circle me-2"
                                                 style="width: 50px" alt="">
                                            <span class="fw-bold fs-5">{{$user->username}}</span>
                                        </div>
                                        <div class="d-flex gap-3">
                                            <a href="{{route('user.profile', $user->id)}}"
                                               class="btn btn-sm btn-success">Profile</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="card shadow-sm border-0 mb-3 overflow-auto">
                                <div
                                    class="card-header text-bg-dark text-center d-flex justify-content-between align-items-center">
                                    <div class="hstack justify-content-center">
                                        <span
                                            class="fw-bold fs-5">The owner of this room has enabled anonymous voting</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- User Has Voted Modal -->

    <!-- User Choice Modal -->
    <div class="modal fade" id="user_choice" data-bs-backdrop="static" tabindex="-1" aria-hidden="true"
         style="backdrop-filter: blur(5px);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-bg-dark">
                    <h5 class="modal-title">Users has Voted</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        @if(!$user_choices->isEmpty())
                            @foreach($user_choices as $choice)
                                <div class="card shadow-sm border-0 mb-3 overflow-auto">
                                    <div
                                        class="card-header text-bg-dark text-center d-flex justify-content-between align-items-center">
                                        <div class="hstack justify-content-center">
                                            <span
                                                class="fw-bold fs-5">Q{{$loop->iteration}}: {{$choice['question']->question_title}}</span>
                                        </div>
                                        <div class="d-flex gap-3">
                                            @if(!empty($choice['user_vote']))
                                                @foreach($choice['user_vote'] as $vote)
                                                    <a class="btn btn-sm btn-success">{{$vote->candidate_title}}</a>
                                                @endforeach
                                            @else
                                                <span>No vote</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif


                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- User Choice Modal -->

@endsection
