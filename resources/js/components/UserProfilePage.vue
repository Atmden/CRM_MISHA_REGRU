<template>
    <div class="content-part">
        <div class="user-profile-page">
            <h1 class="title">Профиль пользователя</h1>
            <div class="wrapper">
                <div class="profile-info-part">
                    <form novalidate class="md-layout form-post-edit" @submit.prevent="validateUser">
                        <div class="profile-info">

                            <div class="avatar">
                                <div class="image">
                                    <img :src="'/user_avatars/'+USER.avatar" alt="">
                                </div>
                                <div class="input-file">
                                    <input type="file" ref="file" id="file" @change="avatarUpload">
                                    <label for="file" class="text-center"><i class="demo-icon icon-add"></i></label>
                                </div>
                            </div>
                            <div class="desc">
                                <div class="title">
                                    <div class="name">{{ USER.family }} {{ USER.name }}</div>
                                    <div class="email">{{ USER.email }}</div>
                                </div>
<!--                                <md-field>-->
<!--                                    <md-checkbox value="1">Уведомлять о комментариях в разделе <b>Правки</b>-->
<!--                                    </md-checkbox>-->
<!--                                </md-field>-->
                            </div>
                            <md-button class="btn">Сменить фото пользователя</md-button>
                        </div>
                        <div class="change-password">
                            <h3>Сменить пароль</h3>
                            <md-field :class="getValidationClass('newpassword')">
                                <label>Пароль</label>
                                <md-input v-model="form.newpassword" type="password"/>
                                <span class="md-error" v-if="!$v.form.newpassword.required">Поле обязательно для заполнения</span>
                                <span class="md-error" v-if="!$v.form.newpassword.minLength">Минимум 6 символов</span>
                            </md-field>
                            <md-field :class="getValidationClass('retrypassword')">
                                <label>Повторите Пароль</label>
                                <md-input v-model="form.retrypassword" type="password"/>
                                <span class="md-error"
                                      v-if="!$v.form.retrypassword.sameAsPassword">Пароли не совпадают</span>
                            </md-field>
                            <md-button class="btn btn-blue" @click="changePassword">Изменить пароль</md-button>
                        </div>
                    </form>
                </div>
                <div class="accounts-notifications-check">
                    <h3>Уведомлять о событиях в аккаунте</h3>
                    <md-content class="md-scrollbar">
                        <md-field>
                            <div v-for="(item, index) in MY_ACCOUNTS">
                                <md-checkbox v-model="item.notify" @change="change_notify = true">{{ item.name }}
                                </md-checkbox>
                            </div>
                        </md-field>
                        <md-button v-if="change_notify" class="btn btn-blue" @click="saveNotify">Применить</md-button>
                    </md-content>
                </div>
            </div>


        </div>
        <md-snackbar :md-active.sync="showSnackbar" md-persistent>
            <span>{{SnackbarMessage}}</span>
        </md-snackbar>
    </div>
</template>
<style lang="scss" scoped>
    .input-file input {
        display: none;
    }
    .input-file {
        position: relative;
        left: 38%;
        margin-top: -50px;
        display: none;
        font-size: 25px;
        color: white;
        background-color: cornflowerblue;
    }
    .avatar:hover > .input-file {
        display: block;
    }

    .md-dialog .md-dialog-container {
        max-width: 768px;
    }

    .md-content {
        max-height: 50vh;
        overflow: auto;
    }
</style>
<script>
    import {mapActions, mapGetters} from 'vuex'
    import {validationMixin} from 'vuelidate'
    import {
        required,
        sameAs,
        minLength
    } from 'vuelidate/lib/validators'

    export default {
        name: 'UserProfilePage',
        mixins: [validationMixin],
        data() {
            return {
                SnackbarMessage:'',
                showSnackbar: false,
                change_notify: false,
                file: '',
                form: {
                    newpassword: null,
                    retrypassword: null,
                },
            };
        },
        validations: {
            form: {
                newpassword: {
                    required,
                    minLength: minLength(6)
                },
                retrypassword: {
                    sameAsPassword: sameAs('newpassword')
                },
            }
        },
        computed: {
            ...mapGetters([
                'USER',
                'MY_ACCOUNTS'
            ]),
        },

        methods: {
            avatarUpload(){
                this.file = this.$refs.file.files[0];

                let formData = new FormData();

                formData.append('user_id', this.USER.id);
                formData.append('file', this.file);

                axios.post( '/api/upload_avatar',
                    formData
                ).finally(()=>{
                    this.GET_USER_FROM_API();
                    this.SnackbarMessage='Аватар успешно изменен!';
                    this.showSnackbar = true;
                })
            },


            saveNotify() {
                axios.post('/api/save_accounts_notify', this.MY_ACCOUNTS)
                    .then(res => {
                        this.change_notify = false;
                        this.SnackbarMessage='Уведомления успешно изменены!';
                        this.showSnackbar = true;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            },

            ...mapActions([
                'GET_USER_FROM_API',
                'GET_MY_ACCOUNTS'
            ]),


            getValidationClass(fieldName) {
                const field = this.$v.form[fieldName]

                if (field) {
                    return {
                        'md-invalid': field.$invalid && field.$dirty
                    }
                }
            },

            changePassword() {
                this.$v.$touch();

                if (!this.$v.$invalid) {
                    axios.post('/api/change_password', this.form)
                        .then(res => {
                            this.SnackbarMessage='Пароль успешно изменен!';
                            this.showSnackbar = true;
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                }
            }


        },
        mounted() {
            this.GET_USER_FROM_API();
            this.GET_MY_ACCOUNTS();
        }
    }
</script>
