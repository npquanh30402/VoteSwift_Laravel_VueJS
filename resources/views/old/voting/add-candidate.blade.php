@extends('layouts.master')

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
                    <div class="list-group-item active">Voting Actions</div>
                    <a href="{{route('room.main', $room->id)}}" class="list-group-item list-group-item-action">Room
                        Details</a>
                    <a href="#" class="list-group-item list-group-item-action">Candidates</a>
                </div>
                <div class="list-group shadow-sm small mb-3">
                    <div class="list-group-item active">Related Links</div>
                    <a href="#" class="list-group-item list-group-item-action">Dummy</a>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-3 overflow-auto">
                    <div class="card-header text-bg-dark text-center">Add Candidates</div>
                    <div class="card-body">
                        <a href="#" class="btn btn-primary" data-bs-toggle="modal"
                           data-bs-target="#add_candidate">Add</a>
                        <div class="mt-3 d-flex flex-column gap-3">
                            @foreach($room->candidates as $candidate)
                                <div class="card">
                                    <div class="card-header fw-bold">
                                        {{$candidate->candidate_name}}
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <p class="card-text text-truncate">{{$candidate->candidate_description}}</p>
                                        <a href="#" class="btn btn-secondary ms-auto" data-bs-toggle="modal"
                                           data-bs-target="#candidate_details_{{$candidate->id}}">See more...</a>
                                    </div>
                                </div>

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
                                                        <th class="w-50">Candidate Name</th>
                                                        <td>{{$candidate->candidate_name}}</td>
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
                                                <form action="{{route('candidate.remove', $candidate->id)}}"
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
                                                                           name="candidate_name"
                                                                           value="{{$candidate->candidate_name}}"
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

        <!-- Create Room Modal -->
        <div class="modal fade" id="add_candidate" data-bs-backdrop="static" tabindex="-1" aria-hidden="true"
             style="backdrop-filter: blur(5px);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header text-bg-dark">
                        <h5 class="modal-title">Create a candidate</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('candidate.store', $room->id)}}" method="post">
                            @csrf
                            <div class="mb-3">
                                <label for="candidate_name" class="form-label">Candidate Name</label>
                                <input type="text" class="form-control" id="candidate_name"
                                       name="candidate_name" value="{{old('candidate_name')}}"
                                       placeholder="Enter Candidate Name">
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
        <!-- Create Room Modal -->

@endsection
