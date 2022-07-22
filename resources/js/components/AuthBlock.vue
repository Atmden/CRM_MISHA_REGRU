<template>
    <div>
        <div class="auth-page">
            <div class="auth-form">
                <h3>Авторизация</h3>
                <form novalidate class="md-layout" @submit.prevent="validateUser">
                    <md-field :class="getValidationClass('firstName')">
                        <label for="email">Email</label>
                        <md-input name="email" id="email" autocomplete="given-name" v-model="form.email"
                                  :disabled="sending"/>
                        <span class="md-error" v-if="!$v.form.email.required">Поле обязательно для заполнения</span>
                        <span class="md-error" v-else-if="!$v.form.email.email">Логин не верный</span>
                    </md-field>
                    <md-field :class="getValidationClass('Password')">
                        <label for="password">Пароль</label>
                        <md-input name="password" id="password" autocomplete="given-name" v-model="form.password"
                                  :disabled="sending" type="password"/>
                        <span class="md-error" v-if="!$v.form.password.required">Поле обязательно для заполнения</span>
                    </md-field>
                    <md-checkbox v-model="array" value="1">Запомнить меня</md-checkbox>
                    <div class="ftr">
                        <md-button type="submit" class="btn btn-blue" :disabled="sending">Войти</md-button>
                        <a href="#" @click="forgetPassword = true">Забыли пароль?</a>
                    </div>
                </form>
            </div>
        </div>
        <md-dialog :md-active.sync="forgetPassword">
            <h2>Забыли пароль</h2>
            <form novalidate class="md-layout form-user-edit">
                <md-field>
                    <label>Email пользователя</label>
                    <md-input v-model="resetEmail"></md-input>
                </md-field>
                <md-dialog-actions>
                    <md-button class="btn btn-cancel" @click="forgetPassword = false">Отмена</md-button>
                    <md-button class="btn btn-blue" @click="sendEmail">Отправить письмо</md-button>
                </md-dialog-actions>
            </form>
        </md-dialog>
        <md-snackbar :md-active.sync="showSnackbar" md-persistent>
            <span>{{SnackbarMessage}}</span>
        </md-snackbar>
    </div>
</template>
<script>


    import {validationMixin} from 'vuelidate'
    import {
        required,
        email,
        minLength,
        maxLength
    } from 'vuelidate/lib/validators'
    import {mapActions, mapGetters} from 'vuex'

    export default {
        name: 'FormValidation',
        mixins: [validationMixin],
        data: () => ({
            showSnackbar: false,
            SnackbarMessage: '',
            forgetPassword: false,
            resetEmail: null,
            form: {
                email: null,
                password: null,
            },
            userSaved: false,
            sending: false,
            lastUser: null,
            array: null
        }),
        validations: {
            form: {

                email: {
                    required,
                    email
                },
                password: {
                    required

                }
            }
        },
        methods: {
            sendEmail() {
                const formData = new FormData();
                formData.append('user_email', this.resetEmail);
                axios.post('/api/send_email', formData).then(res => {
                    if (!res.data.success) {
                        this.SnackbarMessage = res.data.message;
                        this.showSnackbar = true;
                    }
                    else {
                        this.SnackbarMessage = res.data.message;
                        this.showSnackbar = true;
                        this.forgetPassword = false;
                    }

                }).catch((error) => {
                    console.log(error)
                })

            },
            ...mapActions([
                'LOGIN',
                'GET_USER_FROM_API',
                'GET_AUTH_FROM_API',

            ]),
            loginUser() {
                axios.get('/sanctum/csrf-cookie').then(response => {
                    axios.post('/api/login', this.form).then((res) => {
                        // this.GET_AUTH_FROM_API();
                        //this.$router.push({ name: 'AccountListPage'})
                        this.LOGIN();
                    }).catch((error) => {
                        //console.log(error)
                    })
                });

            },
            getValidationClass(fieldName) {
                const field = this.$v.form[fieldName]

                if (field) {
                    return {
                        'md-invalid': field.$invalid && field.$dirty
                    }
                }
            },
            validateUser() {
                this.$v.$touch()

                if (!this.$v.$invalid) {
                    this.loginUser()
                }
            }
        }
    }
</script>
