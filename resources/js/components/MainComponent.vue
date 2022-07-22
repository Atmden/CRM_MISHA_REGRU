<template>

    <div class="body-wrapper">
        <main-menu></main-menu>
        <router-view></router-view>
        <vue-progress-bar></vue-progress-bar>
    </div>

</template>

<script>
    export default {
        name: "MainComponent",
        created: function () {
            axios.interceptors.response.use(undefined, function (err) {
                return new Promise(function (resolve, reject) {
                    if (err.status === 401 && err.config && !err.config.__isRetryRequest) {
                        // if you ever get an unauthorized, logout the user
                        //console.log('401!!!');
                        // you can also redirect to /login if needed !
                    }
                    throw err;
                });
            });
        },
    }
</script>
