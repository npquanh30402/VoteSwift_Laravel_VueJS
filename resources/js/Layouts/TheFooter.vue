<template>
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <div class="container">
            <p>&copy; {{ getCurrentYear() }} VoteSwift. All rights reserved.</p>
            <p class="fs-6 fw-light">
                Powered by
                <a
                    class="link-info link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover"
                    href="https://laravel.com/"
                    target="_blank"
                    >Laravel 10</a
                >
                &
                <a
                    class="link-info link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover"
                    href="https://vuejs.org/"
                    target="_blank"
                    >VueJS 3</a
                >
            </p>
            <small class="text-small text-end text-light d-block ms-auto"
                >Đồ án UNETI</small
            >
        </div>
    </footer>
</template>

<script setup>
function getCurrentYear() {
    return new Date().getFullYear();
}
</script>
