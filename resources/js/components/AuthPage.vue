<template>
    <div>
        <top-panel-auth v-if="!AUTH"></top-panel-auth>
        <auth-block v-if="!AUTH"></auth-block>
        <main-component v-if="AUTH"></main-component>
    </div>
</template>

<script>
    import {mapActions, mapGetters} from 'vuex'

    export default {
        name: 'AuthPage',
        methods:{
            ...mapActions([
                'GET_AUTH_FROM_API',
            ]),

        },
        computed: {
            ...mapGetters([
                'AUTH'
            ]),
        },
        mounted() {

            this.GET_AUTH_FROM_API();
        }
    }
</script>
