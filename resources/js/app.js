
require('./bootstrap');

Vue.component('reply', require('./components/Reply.vue').default);

Vue.component('flash', require('./components/Flash.vue').default);
Vue.component('paginator', require('./components/Paginator.vue').default);

Vue.component('thread-view', require('./pages/Thread.vue').default);

Vue.component('user-notifications', require('./components/UserNotifications.vue').default);

Vue.component('avatar-form', require('./components/AvatarForm').default);

Vue.component('wysiwyg',require('./components/Wysiwyg.vue').default);

const app = new Vue({
    el: '#app',
});
