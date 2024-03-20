@extends('layouts.master')

@section('content')
    <div class="my-3">
        <div class="row justify-content-center mb-3">
            <div class="col-md-3 d-flex flex-column">
                <div>
                    <a href="{{route('question.main', $question->room)}}"
                       class="btn btn-sm btn-secondary">Back</a>
                </div>
            </div>
            <div class="col-md-8 gap-3 align-items-center"></div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-2">
                <div class="list-group shadow-sm small mb-3">
                    <div class="list-group-item text-bg-dark">Actions</div>
                    <a href="#"
                       class="list-group-item list-group-item-action active">Add
                        Candidates</a>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-3 overflow-auto">
                    <div class="card-header text-bg-dark text-center">{{$question->question_title}}</div>
                    <div class="card-body">
                        <a href="#" class="btn btn-primary" data-bs-toggle="modal"
                           data-bs-target="#add_question">Add</a>
                        <div class="mt-3 d-flex flex-column gap-3">
                            @foreach($candidates as $candidate)
                                <div class="card">
                                    <div class="card-header fw-bold d-flex align-items-center gap-2">
                                        ({{$loop->iteration}}) {{$candidate->candidate_title}}
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <p class="card-text text-truncate">{{$candidate->candidate_description}}</p>
                                        <div class="d-flex justify-content-between ms-auto">
                                            <a href="#" class="btn btn-secondary" data-bs-toggle="modal"
                                               data-bs-target="#candidate_details_{{$candidate->id}}">See more...</a>
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

                                <!-- Candidate Details Modal -->
                                <div class="modal fade" id="candidate_details_{{$candidate->id}}"
                                     data-bs-backdrop="static" tabindex="-1"
                                     aria-hidden="true"
                                     style="backdrop-filter: blur(5px);">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header text-bg-dark">
                                                <h5 class="modal-title">Candidate Details</h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-0">
                                                <table class="table table-bordered small mb-0">
                                                    <tr>
                                                        <th class="w-50">Candidate Title</th>
                                                        <td>{{$candidate->candidate_title}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Created At</th>
                                                        <td>{{date('d/m/Y', strtotime($candidate->created_at))}}
                                                            <span
                                                                class="fw-bold">on</span> {{date('H:i:s', strtotime($candidate->created_at))}}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Updated At</th>
                                                        <td>{{date('d/m/Y', strtotime($candidate->updated_at))}}
                                                            <span
                                                                class="fw-bold">on</span> {{date('H:i:s', strtotime($candidate->updated_at))}}
                                                        </td>
                                                    </tr>
                                                </table>
                                                <div class="d-flex flex-column justify-content-center m-0">
                                                    <h5 class="text-bg-dark text-center py-2">Description</h5>
                                                    <p class="card-text py-1 px-2">{{$candidate->candidate_description}}</p>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                   data-bs-target="#candidate_edit_{{$candidate->id}}">Edit Details</a>
                                                <form action="{{route('candidate.delete', $candidate->id)}}"
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
                                <!-- Candidate Details Modal -->

                                <!-- Edit Modal -->
                                <div class="modal fade" id="candidate_edit_{{$candidate->id}}" data-bs-backdrop="static"
                                     tabindex="-1"
                                     aria-hidden="true"
                                     style="backdrop-filter: blur(5px);">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header text-bg-dark">
                                                <h5 class="modal-title">Candidate Edit</h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{route('candidate.update', $candidate->id)}}"
                                                      method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="container">
                                                        <div class="row gx-3">
                                                            <div class="col-md-12">
                                                                <div class="mb-3">
                                                                    <input type="text"
                                                                           class="form-control form-control-sm fw-bold"
                                                                           name="candidate_title"
                                                                           value="{{$candidate->candidate_title}}"
                                                                           placeholder="Candidate Title">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="mb-3">
                                                                    <label
                                                                        class="form-label">Candidate Description</label>
                                                                    <textarea
                                                                        class="form-control form-control-sm"
                                                                        name="candidate_description"
                                                                        style="height: 10rem">{{$candidate->candidate_description}}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-10">
                                                                <div class="d-grid">
                                                                    <button type="submit"
                                                                            class="btn btn-sm btn-success">Update
                                                                        Candidate
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

    <!-- Create Candidate Modal -->
    <div class="modal fade" id="add_question" data-bs-backdrop="static" tabindex="-1" aria-hidden="true"
         style="backdrop-filter: blur(5px);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-bg-dark">
                    <h5 class="modal-title">Create a candidate</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('candidate.store', $question->id)}}" method="post">
                        @csrf
                        <div class="mb-3">
                            <label for="candidate_title" class="form-label">Candidate Title</label>
                            <input type="text" class="form-control" id="candidate_title"
                                   name="candidate_title" value="{{old('candidate_title')}}"
                                   placeholder="Enter Candidate Title">
                        </div>
                        <div class="mb-3">
                            <label for="candidate_description" class="form-label">Candidate Description</label>
                            <textarea id="candidate_description" name="candidate_description"
                                      class="form-control"
                                      style="height: 10rem">{{old('candidate_description')}}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Create Candidate Modal -->
@endsection
