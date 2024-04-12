<template>
    <div class="card">
        <div class="card-body text-center">
            <div class="mt-3 mb-4">
                <img
                    :src="user.avatar"
                    alt=""
                    class="rounded-circle img-fluid"
                    style="width: 100px"/>
            </div>
            <h4 class="mb-2">{{ user.username }}</h4>
            <p class="text-muted mb-4">
                @Programmer <span class="mx-2">|</span>
                <a href="#!">Website</a>
            </p>
            <div class="mb-4 pb-2 hstack gap-3 justify-content-center">
                <button
                    class="btn btn-outline-primary btn-floating"
                    type="button"
                >
                    <i class="bi bi-facebook"></i>
                </button>
                <button class="btn btn-outline-dark btn-floating" type="button">
                    <i class="bi bi-twitter-x"></i>
                </button>
                <button
                    class="btn btn-outline-primary btn-floating"
                    style="color: #ff0050"
                    type="button"
                >
                    <i class="bi bi-tiktok"></i>
                </button>
            </div>
            <button class="btn btn-primary btn-rounded btn-lg" type="button">
                Message
            </button>
            <!--            <div class="d-flex justify-content-between text-center mt-5 mb-2">-->
            <!--                <div>-->
            <!--                    <p class="mb-2 h5">8471</p>-->
            <!--                    <p class="text-muted mb-0">Wallets Balance</p>-->
            <!--                </div>-->
            <!--                <div class="px-3">-->
            <!--                    <p class="mb-2 h5">8512</p>-->
            <!--                    <p class="text-muted mb-0">Income amounts</p>-->
            <!--                </div>-->
            <!--                <div>-->
            <!--                    <p class="mb-2 h5">4751</p>-->
            <!--                    <p class="text-muted mb-0">Total Transactions</p>-->
            <!--                </div>-->
            <!--            </div>-->
        </div>
    </div>
</template>
<script setup>
const props = defineProps(["user"]);
</script>
