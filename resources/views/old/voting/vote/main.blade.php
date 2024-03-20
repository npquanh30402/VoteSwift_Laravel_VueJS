@extends('layouts.master')

@section('content')
    <div class="container my-5">
        @if (!$hasEnded && !$hasVoted)
            <p id="countdown"></p>
            <h2 class="mb-4">Cast Your Vote</h2>
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{route('vote.store', $room->id)}}" method="POST" class="vstack gap-5"
                          id="voteForm">
                        @csrf
                        <div class="vstack gap-4">
                            @foreach($questions as $question)
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="card-title fw-bold">{{$loop->iteration}}
                                        . {{$question->question_title}}</h5>
                                    <a href="#" class="btn btn-primary" data-bs-toggle="modal"
                                       data-bs-target="#question_description_{{$question->id}}">Description</a>
                                </div>
                                <div class="accordion" id="question_{{$question->id}}">
                                    @foreach($question->candidates as $candidate)
                                        <div class="accordion-item">
                                            <div class="accordion-header">
                                                <div class="accordion-button collapsed" data-bs-toggle="collapse"
                                                     data-bs-target="#candidate_{{$candidate->id}}"
                                                     aria-expanded="false"
                                                     aria-controls="candidate_{{$candidate->id}}">
                                                    <div class="form-check">
                                                        <input class="form-check-input"
                                                               type="{{$isMultipleChoice ? 'checkbox' : 'radio'}}"
                                                               id="question_{{$question->id}}"
                                                               name="question_{{ $question->id }}{{ $isMultipleChoice ? '[]' : '' }}"
                                                               value="{{$candidate->id}}" {{$isMultipleChoice ? '' : 'required'}}>
                                                        <label class="form-check-label"
                                                               for="question_{{$question->id}}">
                                                            {{$candidate->candidate_title}}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="candidate_{{$candidate->id}}"
                                                 class="accordion-collapse collapse"
                                                 data-bs-parent="#question_{{$question->id}}">
                                                <div class="accordion-body">
                                                    {{$candidate->candidate_description}}
                                                </div>
                                            </div>
                                        </div>

                                    @endforeach
                                </div>

                                <!-- Question Description Modal -->
                                <div class="modal fade" id="question_description_{{$question->id}}"
                                     data-bs-backdrop="static" tabindex="-1"
                                     aria-hidden="true"
                                     style="backdrop-filter: blur(5px);">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header text-bg-dark">
                                                <h5 class="modal-title">Question Description</h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div>
                                                    <div class="mb-3">
                                                        <label for="question_title" class="form-label">Question
                                                            Title</label>
                                                        <input type="text" class="form-control" id="question_title"
                                                               name="question_title"
                                                               value="{{$question->question_title}}"
                                                               placeholder="Enter Question Title" disabled>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="question_description" class="form-label">Question
                                                            Description</label>
                                                        <textarea id="question_description"
                                                                  name="question_description"
                                                                  class="form-control"
                                                                  style="height: 10rem"
                                                                  disabled>{{$question->question_description}}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Question Description Modal -->
                            @endforeach
                        </div>

                        @if($questions->count() >= 1)
                            @if($questions[0]->candidates->count() >= 1)
                                <div class="d-grid mt-3">
                                    <button type="submit" class="btn btn-primary py-3">Vote
                                    </button>
                                </div>
                            @endif
                        @endif

                    </form>
                </div>
            </div>
        @elseif ($hasEnded && !$isResultHidden)
            <h1 class="display-6 text-center fw-bold">Room has ended</h1>
        @elseif ($isResultHidden || $hasEnded)
            <a class="btn btn-primary" href="{{ route('vote.result', $room->id) }}">To the result page</a>
        @elseif ($hasVoted && !$hasEnded)
            <p class="alert alert-danger">You have already voted for this voting room.</p>
        @elseif (!$isResultHidden && $hasEnded)
            <a class="btn btn-primary" href="{{ route('vote.result', $room->id) }}">To the result page</a>
        @endif
    </div>
@endsection

<script>
    const endTime = new Date('{{ $room->end_time }}');

    function updateCountdown() {
        const currentTime = new Date();

        let remainingTime = endTime - currentTime;

        if (remainingTime < 0) {
            return;
        }

        const days = Math.floor(remainingTime / (1000 * 60 * 60 * 24));
        remainingTime %= (1000 * 60 * 60 * 24);
        const hours = Math.floor(remainingTime / (1000 * 60 * 60));
        remainingTime %= (1000 * 60 * 60);
        const minutes = Math.floor(remainingTime / (1000 * 60));
        remainingTime %= (1000 * 60);
        const seconds = Math.floor(remainingTime / 1000);

        document.getElementById('countdown').innerText = `${days} days ${hours} hours ${minutes} minutes ${seconds} seconds left`;
    }

    setInterval(updateCountdown, 1000);
</script>


