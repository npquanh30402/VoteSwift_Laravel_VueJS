<template>
    <h1 class="text-center mt-3">Login</h1>
    <div class="row justify-content-center my-5">
        <form class="col-md-6 shadow p-5 border rounded">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" value="" name="username" id="username">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" value="" name="password" id="password">
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" name="remember_me" id="remember_me">
                <label class="form-check-label" for="remember_me">Remember me</label>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</template>
