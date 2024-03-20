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
                    <div class="list-group-item text-bg-dark">Friends Option</div>
                    <a href="#" class="list-group-item list-group-item-action active">Friend List</a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between"
                       data-bs-toggle="modal"
                       data-bs-target="#friend_request">Friend Requests
                        @if($userData['friendRequests']->count())
                            <span
                                class="badge text-bg-danger ms-auto">{{$userData['friendRequests']->count()}}</span>
                        @endif
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between"
                       data-bs-toggle="modal"
                       data-bs-target="#request_sent">Request Sent
                        @if($userData['requestSent']->count() > 0)
                            <span class="badge text-bg-danger ms-auto">{{$userData['requestSent']->count()}}</span>
                        @endif
                    </a>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-3 overflow-auto">
                    <div class="card-header text-bg-dark text-center">Friend List</div>
                    <div class="card-body">
                        <div class="row row-cols-2 d-flex align-items-center h-100">
                            @foreach($userData['friends'] as $friend)
                                <div class="col">
                                    <div class="card" style="border-radius: 15px;">
                                        <div class="card-body p-4">
                                            <div class="d-flex text-black">
                                                <div class="flex-shrink-0">
                                                    <img
                                                        src="{{asset($friend->avatar)}}"
                                                        alt="Generic placeholder image" class="img-fluid"
                                                        style="width: 100px; border-radius: 10px;">
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h5 class="mb-1">{{$friend->username}}</h5>
                                                    <p class="mb-2 pb-1"
                                                       style="color: #2b2a2a;">{{$friend->first_name}} {{$friend->last_name}}</p>
                                                    <div class="d-flex pt-1">
                                                        <a href="{{route('chat.main', $friend->id)}}"
                                                           class="btn btn-outline-primary me-1 flex-grow-1">
                                                            Chat
                                                        </a>
                                                        <a href="{{route('user.profile', $friend->id)}}"
                                                           class="btn btn-primary me-1 flex-grow-1">
                                                            Profile
                                                        </a>
                                                        <a href="#"
                                                           class="btn btn-secondary flex-grow-1"
                                                           data-bs-toggle="modal"
                                                           data-bs-target="#friend_option_{{$friend->id}}">Options
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Friend Request Modal -->
                                <div class="modal fade" id="friend_option_{{$friend->id}}" data-bs-backdrop="static"
                                     tabindex="-1"
                                     aria-hidden="true"
                                     style="backdrop-filter: blur(5px);">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header text-bg-dark">
                                                <h5 class="modal-title">Friend Details</h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-0">
                                                <table class="table table-bordered small mb-0">
                                                    <tr>
                                                        <th class="w-50">Friend Username</th>
                                                        <td>{{$friend->username}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="w-50">Friend Fullname</th>
                                                        <td>{{$friend->first_name}} {{$friend->last_name}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Friend Added</th>
                                                        <td>{{date('d/m/Y', strtotime($friend->pivot->created_at))}}
                                                            <span
                                                                class="fw-bold">on</span> {{date('H:i:s', strtotime($friend->pivot->created_at))}}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="modal-footer">
                                                <form action="{{route('user.unfriend', $friend->id)}}"
                                                      method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger">Unfriend
                                                    </button>
                                                </form>
                                                <a href="#" class="btn btn-sm btn-primary" data-bs-dismiss="modal"
                                                   aria-label="Close">Close</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Friend Request Modal -->
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Friend Request Modal -->
    <div class="modal fade" id="friend_request" data-bs-backdrop="static" tabindex="-1" aria-hidden="true"
         style="backdrop-filter: blur(5px);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-bg-dark">
                    <h5 class="modal-title">Friend Requests</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        @foreach($userData['friendRequests'] as $friend_request)
                            <div class="card shadow-sm border-0 mb-3 overflow-auto">
                                <div
                                    class="card-header text-bg-dark text-center d-flex justify-content-between align-items-center">
                                    <span>{{$friend_request->username}}</span>
                                    <div class="d-flex gap-3">
                                        <form action="{{route('user.reject-friend-request', $friend_request->id)}}"
                                              method="post">
                                            @csrf
                                            <button type="submit" class="btn btn-danger">Reject</button>
                                        </form>
                                        <form action="{{route('user.accept-friend-request', $friend_request->id)}}"
                                              method="post">
                                            @csrf
                                            <button type="submit" class="btn btn-success">Accept</button>
                                        </form>
                                    </div>
                                </div>

                                @endforeach
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Friend Request Modal -->

    <!-- Request Sent Modal -->
    <div class="modal fade" id="request_sent" data-bs-backdrop="static" tabindex="-1" aria-hidden="true"
         style="backdrop-filter: blur(5px);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-bg-dark">
                    <h5 class="modal-title">Request Sent</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        @foreach($userData['requestSent'] as $sent)
                            <div class="card shadow-sm border-0 mb-3 overflow-auto">
                                <div
                                    class="card-header text-bg-dark text-center d-flex justify-content-between align-items-center">
                                    <span>{{$sent->username}}</span>
                                    <div class="d-flex gap-3">
                                        <form
                                            action="{{route('user.abort-request-sent', $sent->id)}}"
                                            method="post">
                                            @csrf
                                            <button type="submit" class="btn btn-danger">Abort</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Request Sent Modal -->
@endsection
