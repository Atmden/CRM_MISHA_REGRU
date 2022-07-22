<template>
        <div class="sticky sticky-left-menu" :class="{active: isActive}">
            <div class="left-menu" :class="{active: isActive}">
                <div class="header">
                    <div class="logo-box"><i class="demo-icon icon-social-media"></i> ABG - Media</div>
                    <button @click="toogleMenu" class="btn-open-panel"><i class="demo-icon icon-list"></i></button>
                </div>
                <div class="user-info-box">
                    <div class="wr">
                        <div class="image"><img :src="'/user_avatars/'+USER.avatar" alt=""></div>
                        <div class="info">
                            <div class="name"><a href="">{{ USER.family }} {{ USER.name }}</a></div>
                            <div class="email">{{ USER.email }}</div>
                        </div>
                    </div>
                    <div class="exit"><button @click.prevent="logout" class="btn-exit"><i class="demo-icon icon-logout"></i></button><md-tooltip md-direction="right">Выйти из системы</md-tooltip></div>
                </div>
                <nav>
                    <ul>
                        <router-link to='/pages/accounts' tag="li"><span><i class="demo-icon icon-list2"></i> Аккаунты</span> <md-tooltip md-direction="right">Аккаунты</md-tooltip></router-link>
                        <router-link to='/pages/userprofile' tag="li"><span><i class="demo-icon icon-user2"></i> Ваш профиль</span> <md-tooltip md-direction="right">Ваш профиль</md-tooltip></router-link>
<!--                        <router-link to='/pages/contentplan' tag="li"><span><i class="demo-icon icon-user2"></i> Контент план</span> <md-tooltip md-direction="right">Контент план</md-tooltip></router-link>-->
                    </ul>
                </nav>
            </div>
        </div>
</template>
<style>
    nav ul li {
        cursor: pointer;
    }
    nav ul li:hover {
        background-color: #f6f6f6;
    }
</style>
<script>
    import {mapActions, mapGetters} from 'vuex'

    export default {
        name: 'MainMenu',
        data: () => ({
            showDialog: false,
            isActive: true,

        }),
        methods:{
            ...mapActions([
               'GET_USER_FROM_API',
               'GET_AUTH_FROM_API',

            ]),
            logout(){
                axios.post('/api/logout').then(()=>{
                    window.location = '/';
                })
            },
            toogleMenu: function() {
                this.isActive = !this.isActive;
                // some code to filter users
            }
        },
        computed: {
          ...mapGetters([
              'USER'
          ]),
        },
        mounted() {
            this.GET_USER_FROM_API();
            //this.GET_AUTH_FROM_API();
        }
    }
</script>

