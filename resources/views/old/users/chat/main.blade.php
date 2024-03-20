@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col">
            <h1 class="display-6 text-center fw-bold">Private Chat</h1>
        </div>
    </div>
    <div class="my-3">
        <div class="row justify-content-center">
            <div class="col-md-2">
                <div class="list-group shadow-sm small mb-3">
                    <div class="list-group-item text-bg-dark">Friend lists</div>
                    @foreach($friends as $friend)
                        <a href="{{route('chat.main', $friend->id)}}"
                           class="list-group-item list-group-item-action">{{$friend->username}}</a>
                    @endforeach
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-3 overflow-auto">
                    <div class="card-header text-bg-dark text-center">Chat with {{$user->username}}</div>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="card-body">
                                <div class="row p-2">
                                    <div class="col-md-10">
                                        <div class="vstack">
                                            <div class="col-md-12 border rounded-lg p-3">
                                                <ul id="messages" class="list-unstyled overflow-auto"
                                                    style="height: 45vh">
                                                    @foreach($decryptedMessages as $message)
                                                        <li class="vstack list-group-item mb-2">
                                                            <div>
                                                                <img src="{{$message['avatar']}}" alt="Avatar"
                                                                     class="img-fluid rounded-circle me-2"
                                                                     style="width: 30px; height: auto;">
                                                                <strong>{{$message['sender']}}</strong>
                                                                : {{$message['message']}}
                                                            </div>
                                                            <div class="d-block ms-auto">
                                                                <span class="text-muted small">({{ $message['send_date']->format('d/m/y H:i') }})</span>
                                                            </div>
                                                            @if($message['file'] != null)
                                                                @php
                                                                    $extension = pathinfo($message['file'], PATHINFO_EXTENSION);
                                                                @endphp

                                                                @if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']))
                                                                    <br>
                                                                    <img
                                                                        src="{{ $message['file'] }}"
                                                                        class="img-fluid"
                                                                        style="width: 200px; height: auto;">
                                                                @else
                                                                    <br>
                                                                    <a href="{{ $message['file'] }}"
                                                                       class="btn btn-sm btn-primary w-50"
                                                                       download>Download: {{basename($message['file'])}}</a>
                                                                @endif
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div>
                                                <form class="row py-3" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="col-md-6">
                                                        <input id="message" name="message" class="form-control"
                                                               type="text">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input id="file" name="file" class="form-control" type="file">
                                                    </div>
                                                    <div class="col-md-2 d-grid">
                                                        <button id="send" type="submit" class="btn btn-primary">Send
                                                        </button>
                                                    </div>

                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <p><strong>Currently Online</strong></p>
                                        <ul id="online-users"
                                            class="list-unstyled overflow-auto text-info" style="height: 45vh">
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
<script>
    const userId = {{ auth()->user()->id }};
    const recipientId = {{ $user->id }};
</script>

@vite(['resources/js/chat.js'])
