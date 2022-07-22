<template>
    <div>
        <top-panel-auth></top-panel-auth>
        <!--<password-reset></password-reset>-->
        <div class="auth-page">
            <div class="auth-form" v-if="!linktoHome">
                <h3>Восстановление пароля</h3>
                <form novalidate class="md-layout">
                    <md-field>
                        <label>Email</label>
                        <md-input name="email" id="email" v-model="form.email" disabled />
                    </md-field>
                    <md-field :class="getValidationClass('newpassword')">
                        <label>Пароль</label>
                        <md-input name="password" id="newpassword"  type="newpassword" v-model="form.newpassword" />
                        <span class="md-error" v-if="!$v.form.newpassword.required">Поле обязательно для заполнения</span>
                        <span class="md-error" v-if="!$v.form.newpassword.minLength">Минимум 6 символов</span>
                    </md-field>
                    <md-field :class="getValidationClass('retrypassword')">
                        <label>Повторите пароль</label>
                        <md-input name="retrypassword" id="retrypassword"  type="password" v-model="form.retrypassword" />
                        <span class="md-error"
                              v-if="!$v.form.retrypassword.sameAsPassword">Пароли не совпадают</span>
                    </md-field>
                    <div class="ftr">
                        <md-button @click="resetPassword" class="btn btn-blue">Восстановить пароль</md-button>
                    </div>
                </form>
            </div>
            <div class="auth-form" v-if="linktoHome">
                <md-button @click="resetPassword" class="btn btn-blue">Перейти к авторизации</md-button>
            </div>
        </div>
        <md-snackbar :md-active.sync="showSnackbar" md-persistent>
            <span>{{SnackbarMessage}}</span>
        </md-snackbar>
    </div>
</template>

<script>
    import {mapActions, mapGetters} from 'vuex'
    import {validationMixin} from 'vuelidate'
    import {
        required,
        sameAs,
        minLength
    } from 'vuelidate/lib/validators'
    export default {
        name: 'AuthPage',
        mixins: [validationMixin],
        props: {
            email: {default: ''},
            token: {default: ''},

        },
        data() {
            return {
                linktoHome:false,
                SnackbarMessage:'',
                showSnackbar:false,
                form: {
                    email: this.email,
                    newpassword: null,
                    retrypassword: null,
                    token: this.token,
                }
            }
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
        methods: {
            ...mapActions([
                'GET_AUTH_FROM_API',
            ]),
            getValidationClass(fieldName) {
                const field = this.$v.form[fieldName]

                if (field) {
                    return {
                        'md-invalid': field.$invalid && field.$dirty
                    }
                }
            },
            resetPassword()
            {
                this.$v.$touch();
                if (!this.$v.$invalid) {
                    axios.post('/api/password_reset', this.form)
                        .then(res => {
                            if (!res.data.success) {
                                this.SnackbarMessage = res.data.message;
                                this.showSnackbar = true;
                            }
                            else {
                                this.SnackbarMessage = res.data.message;
                                this.showSnackbar = true;
                                return setTimeout(() => {
                                    window.location = ('/');
                                }, 3000);

                            }
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                }
            }

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
