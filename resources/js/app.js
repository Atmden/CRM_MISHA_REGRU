require('./bootstrap');

window.Vue = require('vue');


import VueMaterial from 'vue-material'
Vue.use(VueMaterial, {
    iconfont: 'md',
});



// import vSelect from 'vue-select'
// Vue.component('v-select', vSelect);

import Multiselect from 'vue-multiselect'

Vue.component('multiselect', Multiselect)

import VueMce from 'vue-mce';
Vue.use(VueMce);
import VueProgressBar from 'vue-progressbar'
Vue.use(VueProgressBar, {
    color: 'rgb(143, 255, 199)',
    failedColor: 'red',
    height: '2px'
})
import router from './router';





import store from './vuex/store'


Vue.component('main-component', require('./components/MainComponent.vue').default); // Главный компнент
Vue.component('reset-password-page', require('./components/ResetPasswordPage.vue').default); // Страница сброса пароля
Vue.component('password-reset', require('./components/PasswordReset.vue').default); // Страница сброса пароля

Vue.component('main-menu', require('./components/MainMenu.vue').default); // Левое меню
Vue.component('auth-block', require('./components/AuthBlock.vue').default); //Блок с формой авторизации
Vue.component('top-panel-auth', require('./components/TopPanelAuth.vue').default); //Верхняя панель на странице Авторизация
Vue.component('input-file', require('./components/InputFile.vue').default);
Vue.component('edit-file', require('./components/EditFile.vue').default);

//Pages
Vue.component('auth-page', require('./components/AuthPage.vue').default); //Страница авторизации
// Vue.component('acc-list-page', require('./components/AccountListPage.vue').default); //Страница со списком аккаунтов - Стартовая после авторизации
Vue.component('content-plan-page', require('./components/ContentPlanPage.vue').default); // Контент-план
Vue.component('one-plan-page', require('./components/OnePlanPage.vue').default); // Страниа одной публикации
// Vue.component('user-profile-page', require('./components/UserProfilePage.vue').default); // Профиль
window.axios.interceptors.request.use(config => {
    app.$Progress.start(); // for every request start the progress
    return config;
});

window.axios.interceptors.response.use(function (response) {
    app.$Progress.finish();
    return response;
}, function (error) {
    if (401 === error.response.status && store.getters.AUTH == true) {
        store.commit('SET_AUTH_TO_STATE', false);
        return false;
    } else {
        return Promise.reject(error);
    }
});

// axios.interceptors.response.use(function (response) {
//     return response
// }, function (error) {
//     if (error.response.status === 401) {
//         store.commit('SET_AUTH_TO_STATE',false);
//     }
//     return Promise.reject(error)
// });



Vue.config.silent = true;
const app = new Vue({
    el: '#app',
    store,
    router,

});

