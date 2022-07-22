import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router);

import AccountListPage from './components/AccountListPage';
import UserProfilePage from './components/UserProfilePage';
import ContentPlanPage from './components/ContentPlanPage';
import OnePlanPage from './components/OnePlanPage';
import PasswordReset from './components/AuthPage.vue';

import store from './vuex/store'
const DEFAULT_TITLE = 'ABG-Media.com';
let router = new Router({
    mode: 'history',
    linkActiveClass: 'active',
    linkExactActiveClass: 'active',
    routes: [

        {
            path: '/',
            component: AccountListPage,
            name: "Home",
            meta: {
                title: 'Авторизация'
            }
        },
        {
            path: '/pages/password_reset/:token',
            component: PasswordReset,
            name: "PasswordReset",
            meta: {
                title: 'Восстановление пароля'
            }
        },
        {
            path: '/pages/accounts',
            component: AccountListPage,
            name: "AccountListPage",
            meta: {
                requiresAuth: true,
                title: 'Аккаунты'
            }
        },
        {
            path: '/pages/contentplan/:id',
            component: ContentPlanPage,
            name: "ContentPlanPage",
            meta: {
                requiresAuth: true,
                title: 'Контент план'
            },
        },
        {
            path: '/pages/oneplanpage/:id',
            component: OnePlanPage,
            name: "OnePlanPage",
            meta: {
                requiresAuth: true,
                title: 'Публикация'
            },
        },
        {
            path: '/pages/userprofile',
            component: UserProfilePage,
            name: "UserProfilePage",
            meta: {
                requiresAuth: true,
                title: 'Ваш профиль'
            },
            beforeEnter: (to, form, next) => {
                store.dispatch('GET_USER_FROM_API');
                next()
            }
        },

    ],
});

router.beforeEach((to, from, next) => {

    if (to.matched.some(record => record.meta.requiresAuth)) {
        store.dispatch('GET_AUTH_FROM_API_2').then(result => {
            //console.log(from);
            if (result) {
                next();
                return
            }
            else {
                localStorage.setItem('from', from.path);
                window.location = ('/');
            }
        });

    } else {
        store.dispatch('GET_AUTH_FROM_API_2').then(result => {
            //console.log(from);
            if (result) {
                router.push({name: 'AccountListPage'});
                return
            }
        });
        next()
    }
});
router.afterEach((to, from) => {
    Vue.nextTick(() => {
        document.title = to.meta.title+' | '+DEFAULT_TITLE || DEFAULT_TITLE;
    });
});
export default router;
