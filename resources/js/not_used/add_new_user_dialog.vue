<!--                Диалог добавления пользователя к аккаунту-->
<md-dialog :md-active.sync="addUserToAccountDialog">
    <div class="user-list">
        <h3>Добавить пользователя</h3>
        <div class="item">
            <md-field :class="getValidationNewUser('email')">
                <label>Email</label>
                <md-input v-model.trim="new_user.email" required></md-input>
                <span class="md-error" v-if="!$v.new_user.email.required">Поле обязательно для заполнения</span>
                <span class="md-error"
                      v-else-if="!$v.new_user.email.email">Введен не верный Email.</span>
            </md-field>
            <md-field :class="getValidationNewUser('name')">
                <label>Имя</label>
                <md-input v-model.trim="new_user.name" required></md-input>
                <span class="md-error"
                      v-if="!$v.new_user.name.required">Поле обязательно для заполнения</span>
                <span class="md-error" v-else-if="!$v.new_user.name.minLength">Минимальная длина - 2 буквы</span>
            </md-field>
            <md-field :class="getValidationNewUser('family')">
                <label>Фамилия</label>
                <md-input v-model.trim="new_user.family" required></md-input>
                <span class="md-error" v-if="!$v.new_user.family.required">Поле обязательно для заполнения</span>
                <span class="md-error" v-else-if="!$v.new_user.family.minLength">Минимальная длина - 2 буквы</span>
            </md-field>
            <md-field :class="getValidationNewUser('phone')">
                <label>Телефон</label>
                <md-input v-model.trim="new_user.phone" required></md-input>
                <span class="md-error" v-if="!$v.new_user.phone.required">Поле обязательно для заполнения</span>
                <span class="md-error"
                      v-else-if="!$v.new_user.phone.numeric">Допускаются только цифры</span>
            </md-field>
        </div>
        <pre>{{ $v.new_user }}</pre>

        <md-button class="btn btn-blue btn-add-new-user" @click="addNewUser(edit_account.id)"><i
            class="demo-icon icon-add-user"></i> Добавить пользователя
        </md-button>
    </div>
</md-dialog>
<!--                -->
import {validationMixin} from 'vuelidate'
import {
required,
email,
minLength,
maxLength,
numeric
} from 'vuelidate/lib/validators'


validations: {
new_user: {
email: {required, email},
name: {required, minLength: minLength(2)},
family: {required, minLength: minLength(2)},
phone: {required, numeric},
}
},

methods: {
getValidationNewUser(fieldName) {
const field = this.$v.new_user[fieldName];
if (field) {
return {
'md-invalid': field.$invalid && field.$dirty
}
}
},
addNewUser(id) {
console.log(id);

if (!this.$v.$invalid) {
axios.post('/api/add_new_user/' + id, this.new_user)
.then(response => {
console.log(response);
})

.catch(function (error) {

console.log(error);

});
;
}
//
// this.addUserToAccountDialog = false;
},
