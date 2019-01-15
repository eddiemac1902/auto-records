
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

window.Fire = new Vue();

import VueRouter from 'vue-router'
import { Form, HasError, AlertError } from 'vform'
import moment from 'moment'
import VueProgressBar from 'vue-progressbar'
import swal from 'sweetalert2'

window.swal = swal

const toast = swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
})

window.toast = toast;

Vue.use(VueProgressBar, {
    // color: 'rgb(143,255,199)',
    color: 'yellow',
    failedColor: 'red',
    height: '6px'
})

window.Form = Form;
Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)

Vue.use(VueRouter)

//filter
Vue.filter("upText", function (text) {
    return text.charAt(0).toUpperCase() + text.slice(1);
});

Vue.filter("myDate", function (created_at) {
    return moment(created_at).format("MMMM Do YYYY");
})

let routes = [
    { path: '/home', name: 'home', component: require('./components/HomeComponent.vue') },
    { path: '/home/dashboard', name: 'dashboard', component: require('./components/DashboardComponent.vue') },
    { path: '/profile', name: 'profile', component: require('./components/ProfileComponent.vue') },
    { path: '/home/users', name: 'users', component: require('./components/Users/UsersComponent.vue') },
    { path: '/developer', name: 'developer', component: require('./components/Developer.vue') },

]

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example-component', require('./components/ExampleComponent.vue'));
//can also do it this way
// Vue.component('example-component', require('./components/DashboardComponent.vue'));
// Vue.component('example-component', require('./components/ProfileComponent.vue'));

Vue.component(
    'passport-clients',
    require('./components/passport/Clients.vue')
);

Vue.component(
    'passport-authorized-clients',
    require('./components/passport/AuthorizedClients.vue')
);

Vue.component(
    'passport-personal-access-tokens',
    require('./components/passport/PersonalAccessTokens.vue')
);


const router = new VueRouter({
    mode: 'history',
    routes // short for `routes: routes`
})

const app = new Vue({
    el: '#app',
    router
});
