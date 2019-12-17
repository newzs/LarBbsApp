window._ = require('lodash');

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Vue = require('vue');

let authorizations = require('./authorizations');

Vue.prototype.authorize = function (...params) {
    if (! window.App.signedIn) return false;

    if (typeof params[0] === 'string') {
        return authorizations[params[0]](params[1]);
    }
    return params[0](window.App.user);
};

Vue.prototype.signedIn = window.App.signedIn;

window.events = new Vue();

window.flash = function (message,level = 'success') {
    //子组件触发父组件
    window.events.$emit('flash',{ message,level });
};
