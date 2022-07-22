import Vue from 'vue'
import Vuex from 'vuex'
import router from '../router'

Vue.use(Vuex)
let store = new Vuex.Store({
    state: {
        user: [],
        auth: false,
        myaccounts: [],
        all_socnets: [],
        content_plan: [],
        one_plan_page: [],
        statuses: [],
        account_socnets: [],
        account_tags: [],

    },
    mutations: {
        SET_ACCOUNT_SOCNETS: (state, account_socnets) => {
            state.account_socnets = account_socnets;
        },
        SET_ACCOUNT_TAGS: (state, account_tags) => {
            state.account_tags = account_tags;
        },
        SET_AUTH_TO_STATE: (state, auth) => {
            state.auth = auth;
        },
        SET_USER_TO_STATE: (state, user) => {
            state.user = user;
        },
        SET_MY_ACCOUNTS_TO_STATE: (state, accounts) => {
            state.myaccounts = accounts;
        },
        SET_ALL_SOCNETS_TO_STATE: (state, socnets) => {
            state.all_socnets = socnets;
        },
        SET_CONTENT_PLAN: (state, content_plan) => {
            state.content_plan = content_plan;
        },
        SET_ONE_PLAN_PAGE: (state, one_plan_page) => {
            state.one_plan_page = one_plan_page;
        },
        SET_STATUSES: (state, statuses) => {
            state.statuses = statuses;
        },

    },
    actions: {
        LOGIN({commit}) {

            //console.log('RUN LOGIN');
            commit('SET_AUTH_TO_STATE', 1);
            //console.log('LOGIN commit complete');
            if (localStorage.getItem('from').length > 3) {
                router.push(localStorage.getItem('from'));
            } else {
                router.push({name: 'AccountListPage'});
            }


        },
        GET_AUTH_FROM_API_2({commit}) {
            return new Promise((resolve, reject) => {
                //console.log('RUN GET_AUTH_FROM_API_2');
                return axios.get('/api/athenticated').then((res) => {
                    commit('SET_AUTH_TO_STATE', res.data);

                    resolve(res.data);
                }).catch((error) => {
                    //console.log(error);
                    if (error.response.status == 401) {

                        store.commit('SET_AUTH_TO_STATE', false);
                        resolve(false);
                    }
                    return error;
                })
            })
        },
        GET_AUTH_FROM_API({commit}) {
            //console.log('RUN GET_AUTH_FROM_API');
            return axios.get('/api/athenticated').then((res) => {
                commit('SET_AUTH_TO_STATE', res.data);

                return 1;
            }).catch((error) => {
                if (error.response.status == 401) {

                    store.commit('SET_AUTH_TO_STATE', false);
                    return 0;
                }
                return error;
            })
        },
        GET_USER_FROM_API({commit}) {
            return axios.get('/api/user').then((res) => {
                commit('SET_USER_TO_STATE', res.data);
                return res.data;
            }).catch((error) => {
                //console.log(error);
                return error;
            })
        },
        GET_MY_ACCOUNTS({commit}) {
            return axios.get('/api/my_accounts').then((res) => {
                commit('SET_MY_ACCOUNTS_TO_STATE', res.data.data);
                return res.data.data;
            }).catch((error) => {
                return error;
            })
        },
        GET_ALL_SOCNETS({commit}) {
            return axios.get('/api/socnets').then((res) => {
                commit('SET_ALL_SOCNETS_TO_STATE', res.data.data);
                return res.data.data;
            }).catch((error) => {
                return error;
            })
        },
        GET_CONTENT_PLAN({commit}, param) {
            //commit('SET_CONTENT_PLAN', null);
            return axios.get('/api/account_plan/' + param).then((res) => {
                commit('SET_CONTENT_PLAN', res.data.data);
                return res.data.data;
            }).catch((error) => {
                return error;
            })
        },
        GET_ONE_PLAN_PAGE({commit}, param) {
            //commit('SET_CONTENT_PLAN', null);
            return axios.get('/api/one_plan_page/' + param).then((res) => {
                commit('SET_ONE_PLAN_PAGE', res.data.data);
                return res.data.data;
            }).catch((error) => {
                return error;
            })
        },
        GET_CONTENT_PLAN_WITH_NULL({commit}, param) {
            commit('SET_CONTENT_PLAN', null);
            return axios.get('/api/account_plan/' + param).then((res) => {
                commit('SET_CONTENT_PLAN', res.data.data);
                return res.data.data;
            }).catch((error) => {
                return error;
            })
        },
        GET_CONTENT_PLAN_WITH_FILTER({commit}, param) {
            commit('SET_CONTENT_PLAN', null);
            return axios.post('/api/account_plan_filter', param).then((res) => {
                commit('SET_CONTENT_PLAN', res.data.data);
                return res.data.data;
            }).catch((error) => {
                return error;
            })
        },
        GET_ACCOUNT_SOCNETS({commit}, param) {
            return axios.get('/api/account_socnets/' + param).then((res) => {
                commit('SET_ACCOUNT_SOCNETS', res.data.data);
                return res.data.data;
            }).catch((error) => {
                return error;
            })
        },
        GET_PAGE_SOCNETS({commit}, param) {
            return axios.get('/api/page_socnets/' + param).then((res) => {
                commit('SET_ACCOUNT_SOCNETS', res.data.data);
                return res.data.data;
            }).catch((error) => {
                return error;
            })
        },
        GET_ACCOUNT_TAGS({commit}, param) {
            return axios.get('/api/account_tags/' + param).then((res) => {
                commit('SET_ACCOUNT_TAGS', res.data.data);
                return res.data.data;
            }).catch((error) => {
                return error;
            })
        },
        GET_STATUSES({commit}) {
            return axios.get('/api/statuses').then((res) => {
                commit('SET_STATUSES', res.data.data);
                return res.data.data;
            }).catch((error) => {
                return error;
            })
        },
    },
    getters: {
        USER(state) {
            return state.user;
        },
        AUTH(state) {
            return state.auth;
        },
        MY_ACCOUNTS(state) {
            return state.myaccounts;
        },
        ALL_SOCNETS(state) {
            return state.all_socnets;
        },
        CONTENT_PLAN(state) {
            return state.content_plan;
        },
        ONE_PLAN_PAGE(state) {
            return state.one_plan_page;
        },
        STATUSES(state) {
            return state.statuses;
        },
        ACCOUNT_SOCNETS(state) {
            return state.account_socnets;
        },
        ACCOUNT_TAGS(state) {
            return state.account_tags;
        },
    },
});

export default store;
