{{--<header>--}}
{{--    <nav class="navbar navbar-expand-md navbar-dark text-bg-dark">--}}
{{--        <div class="container">--}}
{{--            <a class="navbar-brand" href="{{route('homepage')}}">VoteSwift</a>--}}
{{--            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"--}}
{{--                    data-bs-target="#navbarSupportedContent"--}}
{{--                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">--}}
{{--                <span class="navbar-toggler-icon"></span>--}}
{{--            </button>--}}
{{--            <div class="collapse navbar-collapse" id="navbarSupportedContent">--}}
{{--                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">--}}
{{--                    @auth()--}}
{{--                        <li class="nav-item">--}}
{{--                            <form action="{{route('logout')}}" method="POST">--}}
{{--                                @csrf--}}
{{--                                <button class="btn btn-sm btn-secondary">Sign Out</button>--}}
{{--                            </form>--}}
{{--                        </li>--}}
{{--                    @else--}}

{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link" href="{{route('register')}}">Register</a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link" href="{{route('login')}}">Login</a>--}}
{{--                        </li>--}}

{{--                    @endauth--}}
{{--                </ul>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </nav>--}}

{{--</header>--}}

<header>
    <!-- Navbar -->
    <nav class="navbar navbar-dark navbar-expand-lg bg-dark shadow small mb-3">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{route('homepage')}}">
                <span class="brand-gradient">VoteSwift</span>
            </a>
            <button
                class="navbar-toggler border-0"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent"
                aria-expanded="false"
                aria-label="Toggle navigation">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list"
                     viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                          d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                </svg>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @auth()
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="{{route('dashboard.user')}}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="{{route('public.room')}}">Public
                                Rooms</a>
                        </li>
                    @endauth
                </ul>

                <div class="d-flex" role="search">
                    <div class="hstack gap-3">
                        <div class="input-group">
                            <input class="form-control form-control-sm me-auto" type="text" placeholder="Search for..."
                                   aria-label="Search for...">
                            <button type="button" class="btn btn-sm btn-success">
                                <!-- Search Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-search" viewBox="0 0 16 16">
                                    <path
                                        d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                                </svg>
                            </button>
                        </div>
                        @auth()
                            <div class="me-3">
                                <a href="{{route('dashboard.user')}}" class="d-flex align-items-center">
                                    <img src="{{auth()->user()->avatar}}" class="rounded-circle"
                                         style="width: 3rem;"
                                         alt="Avatar"/>
                                    <span class="fs-4 mx-3 text-white">{{auth()->user()->username}}</span>
                                </a>
                            </div>
                        @endauth
                        <div class="vr text-white"></div>
                        <div>
                            @auth()
                                <button type="submit" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#logout">
                                    <!-- Power Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                         fill="currentColor"
                                         class="bi bi-power" viewBox="0 0 16 16">
                                        <path d="M7.5 1v7h1V1h-1z"/>
                                        <path
                                            d="M3 8.812a4.999 4.999 0 0 1 2.578-4.375l-.485-.874A6 6 0 1 0 11 3.616l-.501.865A5 5 0 1 1 3 8.812z"/>
                                    </svg>
                                </button>
                            @else
                                <div class="d-flex gap-3">
                                    <a href="{{route('login')}}" class="btn btn-sm btn-success">

                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                             fill="currentColor"
                                             class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd"
                                                  d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0z"/>
                                            <path fill-rule="evenodd"
                                                  d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                                        </svg>

                                    </a>
                                    <a href="{{route('register')}}" class="btn btn-sm btn-secondary">

                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                             fill="currentColor"
                                             class="bi bi-door-open-fill" viewBox="0 0 16 16">
                                            <path
                                                d="M1.5 15a.5.5 0 0 0 0 1h13a.5.5 0 0 0 0-1H13V2.5A1.5 1.5 0 0 0 11.5 1H11V.5a.5.5 0 0 0-.57-.495l-7 1A.5.5 0 0 0 3 1.5V15zM11 2h.5a.5.5 0 0 1 .5.5V15h-1zm-2.5 8c-.276 0-.5-.448-.5-1s.224-1 .5-1 .5.448.5 1-.224 1-.5 1"/>
                                        </svg>

                                    </a>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <!-- Navbar -->

    @auth()
        <!-- Logout Modal -->
        <div class="modal fade" id="logout" data-bs-backdrop="static"
             tabindex="-1"
             aria-hidden="true"
             style="backdrop-filter: blur(5px);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div
                        class="modal-body text-center d-flex justify-content-between align-items-center p-3">
                        <h4 class="fw-light m-0">Do you want to logout?</h4>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                        <form action="{{route('logout')}}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger">YES!</button>
                        </form>
                        <button class="btn btn-sm btn-warning" data-bs-dismiss="modal"
                                aria-label="Close">
                            No
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Logout Modal -->
    @endauth
</header>
