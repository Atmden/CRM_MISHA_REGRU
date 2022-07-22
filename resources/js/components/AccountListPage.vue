<template>

    <div class="content-part">
        <div class="top-panel">
            <form novalidate class="md-layout search-form">
                <md-field>
                    <md-input v-model="search" placeholder="Поиск по аккаунтам"/>
                    <md-button class="btn btn-blue btn-search"><i class="demo-icon icon-magnifiying-glas"></i>
                    </md-button>
                </md-field>
            </form>
            <md-dialog :md-active.sync="newUserDialog">
                <h2>Создать новый аккаунт</h2>
                <form novalidate class="md-layout form-user-edit">
                    <div class="item-title-acc">
                        <md-field>
                            <label>Название аккаунта</label>
                            <md-input v-model="new_account.name"/>
                        </md-field>
                        <md-checkbox v-model="new_account.online" value="1">Активен</md-checkbox>
                    </div>
                    <div class="item-title-acc">
                        <md-checkbox v-model="new_account.auto_apply" true-value="1" false-value="0">
                            Автоматический статус контента
                        </md-checkbox>
                    </div>
                    <div class="soc-service-list">
                        <h3>Интеграция со сторонними сервисами</h3>
                        <div class="item-service" v-for="(item, index) in new_account.soc_net">
                            <h4><img :src="item.logo" alt="" style="width: 35px;"> {{ item.name }}</h4>
                            <md-field>
                                <md-input v-model="item.url" :placeholder="'ID страницы на '+item.name"/>
                            </md-field>
                            <md-checkbox v-model="item.online" true-value="1" false-value="0">{{
                                item.online == '1' ? 'Вкл.' : 'Выкл.' }}
                            </md-checkbox>
                        </div>
                    </div>
                    <md-dialog-actions>
                        <md-button class="btn btn-cancel" @click="cancelNewAccount">Отмена</md-button>
                        <md-button class="btn btn-blue" @click="saveNewAccount">Сохранить</md-button>
                    </md-dialog-actions>
                </form>
            </md-dialog>
            <md-button v-if="USER.can_edit" class="btn btn-blue btn-add-new-user" @click="showNewUserDialog"><i
                    class="demo-icon icon-add-user"></i> Создать аккаунт
            </md-button>
        </div>
        <div class="acc-list-page">
            <div class="item" v-for="(item, index) in filteredData">
                <div class="info">
                    <div class="title"><router-link :to="'/pages/contentplan/'+item.id" tag="a">{{ item.name }}</router-link></div>
                    <div class="status" :class=" item.online=='1'?'status-active':'status-unactive' ">
                        {{ item.online == '1' ? 'Активный' : 'Не активный' }}
                    </div>
                    <div v-if="item.auto_apply==1" class="status" :class=" item.auto_apply=='1'?'status-active':'status-unactive' ">
                        Автоматический статус контента
                    </div>
                    <div class="users-count"><i class="demo-icon icon-user3"></i> {{ item.users_count }}</div>
                </div>
                <md-dialog :md-active.sync="editAccountDialog">
                    <h2>Редактирование аккаунта</h2>
                    <form novalidate class="md-layout form-user-edit" @submit.prevent="validateUser">
                        <div class="item-title-acc">
                            <md-field>
                                <label>Название аккаунта</label>
                                <md-input v-model="edit_account.name"/>
                            </md-field>
                            <md-checkbox v-model="edit_account.online" true-value="1" false-value="0">{{
                                edit_account.online == '1' ? 'Активен' : 'Не активен' }}
                            </md-checkbox>
                        </div>
                        <div class="item-title-acc">
                            <md-checkbox v-model="edit_account.auto_apply" true-value="1" false-value="0">
                                Автоматический статус контента
                            </md-checkbox>
                        </div>
                        <div class="soc-service-list">
                            <h3>Интеграция со сторонними сервисами</h3>
                            <div class="item-service" v-for="(item, index) in edit_account.soc_net">
                                <h4><img :src="item.logo" alt="" style="width: 35px;"> {{ item.name }}</h4>
                                <md-field>
                                    <md-input v-model="item.url" :placeholder="'ID страницы на '+item.name"/>
                                </md-field>
                                <md-checkbox v-model="item.online" true-value="1" false-value="0">{{
                                    item.online == '1' ? 'Вкл.' : 'Выкл.' }}
                                </md-checkbox>
                            </div>
                        </div>
                        <div class="user-list">
                            <h3>Пользователи</h3>
                            <div class="item" v-for="(item, index) in edit_account.users">
                                <div v-if="!item.edit" class="title">{{ item.email }}</div>
                                <md-field v-if="item.edit">
                                    <md-input v-model="edit_user.email" placeholder="Email"/>
                                </md-field>
                                <md-field v-if="item.edit">
                                    <md-input v-model="edit_user.new_password" placeholder="Пароль"/>
                                </md-field>
                                <br>
                                <md-field v-if="item.edit">
                                    <md-checkbox v-model="edit_user.can_edit" true-value="1" false-value="0">Может редактировать</md-checkbox>
                                    <br>
                                    <md-checkbox v-model="edit_user.can_comment" true-value="1" false-value="0">Может создавать правки</md-checkbox>
                                </md-field>
                                <md-field v-if="item.edit">
                                <md-button v-if="item.edit" class="btn-edit-user" @click="saveEditUser(index)"><i
                                        class="demo-icon icon-title"></i></md-button>


                                <md-button v-if="item.edit" class="btn-edit-user" @click="deleteEditUser(index)"><i
                                        class="demo-icon icon-trash"></i></md-button>
                                <md-button v-if="item.edit" class="btn-edit-user" @click="cancelEditUser(index)"><i
                                    class="demo-icon icon-cancel"></i></md-button>
                                </md-field>
                                <md-button v-if="!item.edit" class="btn-edit-user" @click="editUser(index)"><i
                                        class="demo-icon icon-exchange"></i></md-button>

                            </div>
                            <div class="item" v-if="addUserInput">
                                <div class="md-layout md-gutter">
                                    <div class="md-layout-item md-small-size-100">
                                        <md-field>
                                            <label>Email</label>
                                            <md-input v-model="new_user.email" placeholder="Email"/>
                                        </md-field>
                                        <md-field>
                                            <md-checkbox v-model="new_user.send_email">Отправить приглашение
                                            </md-checkbox>
                                            <md-checkbox v-model="new_user.can_edit">Может редактировать</md-checkbox>
                                            <md-checkbox v-model="new_user.can_comment">Может создавать правки</md-checkbox>
                                        </md-field>
                                    </div>
                                    <div class="md-layout-item md-small-size-100">
                                        <md-button class="btn btn-blue" @click="cancelAddUser()">Отмена</md-button>
                                        <md-button class="btn btn-blue" @click="addUserToAccount(edit_account.id)">
                                            Сохранить
                                        </md-button>
                                    </div>
                                </div>

                            </div>
                            <md-button v-if="showbuttonadduser" class="btn btn-blue btn-add-new-user"
                                       @click="showAddUser()">
                                <i class="demo-icon icon-add-user"></i> Добавить пользователя
                            </md-button>
                        </div>
                        <md-dialog-actions>
                            <md-button class="btn btn-cancel" @click="editAccountDialog = false">Отмена</md-button>
                            <md-button class="btn btn-blue" @click="saveEditAccount(edit_account.id)">Сохранить
                            </md-button>
                        </md-dialog-actions>
                    </form>
                </md-dialog>

                <md-button v-if="USER.can_edit" class="btn-edit-user" @click="openEditDialog(item.id)"><i
                        class="demo-icon icon-exchange"></i></md-button>

            </div>


        </div>
    </div>
</template>
<style lang="scss" scoped>
    .md-dialog .md-dialog-container {
        max-width: 768px;
    }
</style>
<script>

    //import { Splide, SplideSlide } from '@splidejs/vue-splide';
    import {mapActions, mapGetters, mapMutations} from 'vuex'

    export default {
        name: 'AccountListPage',
        data() {

            return {
                search: '',
                submitStatus: null,
                // Данные для добавления нового аккаунта
                newUserDialog: false,
                new_account: {},
                // Данные аккаунта для редактирования
                edit_account: {},
                editAccountDialog: false,
                addUserInput: false,
                showbuttonadduser: true,

                // Данные для добавления нового пользователя
                new_user: {
                    email: null,
                    can_edit: false,
                    can_comment: false,
                    send_email: false
                },
                edit_user: {},


                options: {
                    rewind: true,
                    width: 800,
                    type: 'loop',
                    perPage: 1,
                    focus: 'center',
                },


            };
        },

        methods: {
            ...mapMutations([
                'SET_CONTENT_PLAN'
            ]),
            saveNewAccount() {
                axios.post('/api/add_new_account', this.new_account)
                    .then(res => {
                        this.GET_MY_ACCOUNTS();
                        this.newUserDialog = false;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            cancelNewAccount() {
                this.new_account = {};
                this.newUserDialog = false;
            },
            showNewUserDialog() {
                axios.get('/api/get_new_account').then((res) => {
                    this.new_account = res.data.data;
                    this.newUserDialog = true;
                }).catch((error) => {
                    return error;
                })
            },
            saveEditAccount(id) {
                let id_account = id;
                axios.post('/api/edit_account', this.edit_account)
                    .then(res => {
                        axios.get('/api/account_to_edit/' + id_account).then((res) => {
                            this.GET_MY_ACCOUNTS();
                            this.editAccountDialog = false;
                        });
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            },
            deleteEditUser(index) {
                let id_account = this.edit_account.id;
                axios.post('/api/delete_user/' + id_account, this.edit_account.users[index])
                    .then(res => {
                        axios.get('/api/account_to_edit/' + id_account).then((res) => {
                            this.edit_account.users[index].edit = false;
                            this.edit_account = res.data.data;

                        });
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            },
            cancelEditUser(index){
                this.edit_account.users[index].edit = false;
            },
            saveEditUser(index) {
                let id_account = this.edit_account.id;
                axios.post('/api/edit_user', this.edit_user)
                    .then(res => {
                        axios.get('/api/account_to_edit/' + id_account).then((res) => {
                            this.edit_account.users[index].edit = false;
                            this.edit_account = res.data.data;
                        });
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            },
            editUser(index) {
                this.edit_user = this.edit_account.users[index];
                this.edit_account.users[index].edit = true;
                console.log(this.edit_user);
            },
            cancelAddUser() {
                this.addUserInput = false;
                this.showbuttonadduser = true;
                this.new_user = {
                    email: null,
                    can_edit: false,
                    can_comment: false,
                    send_email: false
                };
            },
            showAddUser() {
                this.showbuttonadduser = false;
                this.addUserInput = true;
            },
            getValidationClass(fieldName) {
                const field = this.$v.form[fieldName];
                if (field) {
                    return {
                        'md-invalid': field.$invalid && field.$dirty
                    }
                }
            },
            addUserToAccount(id) {
                let id_account = id;
                axios.post('/api/add_new_user/' + id_account, this.new_user)
                    .then(res => {
                        axios.get('/api/account_to_edit/' + id_account).then((res) => {
                            this.edit_account = res.data.data;
                            this.addUserInput = false;
                            this.showbuttonadduser = true;
                            this.new_user = {
                                email: null,
                                can_edit: false,
                                can_comment: false,
                                send_email: false
                            };

                        });
                    })
                    .catch(function (error) {
                        console.log(error);
                    });


            },
            openEditDialog(id) {
                console.log(id);
                axios.get('/api/account_to_edit/' + id).then((res) => {
                    this.edit_account = res.data.data;
                    this.editAccountDialog = true;
                }).catch((error) => {
                    return error;
                })
            },
            ...mapActions([
                'GET_MY_ACCOUNTS',
                'GET_ALL_SOCNETS'
            ]),
            clearForm() {
                this.$v.$reset()
                this.form.firstName = null
                this.form.lastName = null
                this.form.age = null
                this.form.gender = null
                this.form.email = null
            },
            saveUser() {
                this.sending = true

                // Instead of this timeout, here you can call your API
                window.setTimeout(() => {
                    this.lastUser = `${this.form.firstName} ${this.form.lastName}`
                    this.userSaved = true
                    this.sending = false
                    this.clearForm()
                }, 1500)
            },
            validateUser() {
                this.$v.$touch()

                if (!this.$v.$invalid) {
                    this.saveUser()
                }
            }
        },
        computed: {
            ...mapGetters([
                'MY_ACCOUNTS',
                'ALL_SOCNETS',
                'USER'
            ]),
            filteredData() {
                return this.MY_ACCOUNTS.filter(account => account.name.toLowerCase().includes(this.search.toLowerCase()))
            }
        },
        beforeMount() {
            this.SET_CONTENT_PLAN(null);
            this.GET_MY_ACCOUNTS();
            this.GET_ALL_SOCNETS();
        },
    }
</script>
