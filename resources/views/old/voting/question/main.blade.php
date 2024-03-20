@extends('layouts.master')

@section('content')
    <div class="my-3">
        <div class="row justify-content-center mb-3">
            <div class="col-md-3">
                <span class="btn btn-success">Room</span>
                <a href="{{route('room.main', $room->id)}}" class="btn btn-info">{{$room->id}}
                    - {{$room->room_name}}</a>
            </div>
            <div class="col-md-7 gap-3 align-items-center"></div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-2">
                <div class="list-group shadow-sm small mb-3">
                    <div class="list-group-item text-bg-dark">Actions</div>
                    <a href="{{route('room.main', $room->id)}}" class="list-group-item list-group-item-action">Room
                        Details</a>
                    <a href="{{route('question.main', $room->id)}}"
                       class="list-group-item list-group-item-action active">Add
                        Questions</a>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-3 overflow-auto">
                    <div class="card-header text-bg-dark text-center">Add Question</div>
                    <div class="card-body">
                        <a href="#" class="btn btn-primary" data-bs-toggle="modal"
                           data-bs-target="#add_question">Add</a>
                        <div class="mt-3 d-flex flex-column gap-3">
                            @foreach($questions as $question)
                                <div class="card">
                                    <div class="card-header fw-bold d-flex align-items-center gap-2">
                                        {{$question->question_title}} (Number of
                                        candidates: {{$question->candidates->count()}})
                                        <a href="{{route('candidate.main', ['room' => $room->id,'question' => $question->id])}}"
                                           class="text-success">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                 fill="currentColor" class="bi bi-arrow-up-right-square"
                                                 viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                      d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm5.854 8.803a.5.5 0 1 1-.708-.707L9.243 6H6.475a.5.5 0 1 1 0-1h3.975a.5.5 0 0 1 .5.5v3.975a.5.5 0 1 1-1 0V6.707z"/>
                                            </svg>
                                        </a>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <p class="card-text text-truncate">{{$question->question_description}}</p>
                                        <div class="d-flex justify-content-between">
                                            <a href="#" class="btn btn-primary" data-bs-toggle="modal"
                                               data-bs-target="#add_candidate_{{$question->id}}">Add Candidates</a>
                                            <a href="#" class="btn btn-secondary" data-bs-toggle="modal"
                                               data-bs-target="#question_details_{{$question->id}}">See more...</a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Create Candidate Modal -->
                                <div class="modal fade" id="add_candidate_{{$question->id}}" data-bs-backdrop="static"
                                     tabindex="-1"
                                     aria-hidden="true"
                                     style="backdrop-filter: blur(5px);">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header text-bg-dark">
                                                <h5 class="modal-title">Add a candidate</h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{route('candidate.store', $question->id)}}"
                                                      method="post">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label for="candidate_title" class="form-label">Candidate
                                                            Title</label>
                                                        <input type="text" class="form-control" id="candidate_title"
                                                               name="candidate_title" value="{{old('candidate_title')}}"
                                                               placeholder="Enter Candidate Title">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="candidate_description" class="form-label">Candidate
                                                            Description</label>
                                                        <textarea id="candidate_description"
                                                                  name="candidate_description"
                                                                  class="form-control"
                                                                  style="height: 10rem">{{old('candidate_description')}}</textarea>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Add</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Create Candidate Modal -->

                                <!-- Question Details Modal -->
                                <div class="modal fade" id="question_details_{{$question->id}}"
                                     data-bs-backdrop="static" tabindex="-1"
                                     aria-hidden="true"
                                     style="backdrop-filter: blur(5px);">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header text-bg-dark">
                                                <h5 class="modal-title">Question Details</h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-0">
                                                <table class="table table-bordered small mb-0">
                                                    <tr>
                                                        <th class="w-50">Question Title</th>
                                                        <td>{{$question->question_title}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Created At</th>
                                                        <td>{{date('d/m/Y', strtotime($question->created_at))}}
                                                            <span
                                                                class="fw-bold">on</span> {{date('H:i:s', strtotime($question->created_at))}}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Updated At</th>
                                                        <td>{{date('d/m/Y', strtotime($question->updated_at))}}
                                                            <span
                                                                class="fw-bold">on</span> {{date('H:i:s', strtotime($question->updated_at))}}
                                                        </td>
                                                    </tr>
                                                </table>
                                                <div class="d-flex flex-column justify-content-center m-0">
                                                    <h5 class="text-bg-dark text-center py-2">Description</h5>
                                                    <p class="card-text py-1 px-2">{{$question->question_description}}</p>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                   data-bs-target="#question_edit_{{$question->id}}">Edit Details</a>
                                                <form action="{{route('question.delete', $question->id)}}"
                                                      method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                                <a href="#" class="btn btn-sm btn-primary" data-bs-dismiss="modal"
                                                   aria-label="Close">Close</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Question Details Modal -->

                                <!-- Edit Modal -->
                                <div class="modal fade" id="question_edit_{{$question->id}}" data-bs-backdrop="static"
                                     tabindex="-1"
                                     aria-hidden="true"
                                     style="backdrop-filter: blur(5px);">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header text-bg-dark">
                                                <h5 class="modal-title">Question Edit</h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{route('question.update', $question->id)}}"
                                                      method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="container">
                                                        <div class="row gx-3">
                                                            <div class="col-md-12">
                                                                <div class="mb-3">
                                                                    <input type="text"
                                                                           class="form-control form-control-sm fw-bold"
                                                                           name="question_name"
                                                                           value="{{$question->question_title}}"
                                                                           placeholder="Question Title">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="mb-3">
                                                                    <label
                                                                        class="form-label">Question Description</label>
                                                                    <textarea
                                                                        class="form-control form-control-sm"
                                                                        name="question_description"
                                                                        style="height: 10rem">{{$question->question_description}}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-10">
                                                                <div class="d-grid">
                                                                    <button type="submit"
                                                                            class="btn btn-sm btn-success">Update
                                                                        Question
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="d-grid">
                                                                    <button type="button" class="btn btn-sm btn-warning"
                                                                            data-bs-dismiss="modal"
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
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Question Modal -->
    <div class="modal fade" id="add_question" data-bs-backdrop="static" tabindex="-1" aria-hidden="true"
         style="backdrop-filter: blur(5px);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-bg-dark">
                    <h5 class="modal-title">Create a question</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('question.store', $room->id)}}" method="post">
                        @csrf
                        <div class="mb-3">
                            <label for="question_title" class="form-label">Question Title</label>
                            <input type="text" class="form-control" id="question_title"
                                   name="question_title" value="{{old('question_title')}}"
                                   placeholder="Enter Question Title">
                        </div>
                        <div class="mb-3">
                            <label for="question_description" class="form-label">Question Description</label>
                            <textarea id="question_description" name="question_description"
                                      class="form-control"
                                      style="height: 10rem">{{old('question_description')}}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Create Question Modal -->
@endsection
