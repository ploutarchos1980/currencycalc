import './styles/dashboard.css';

import Vue from 'vue';

import Dashboard from './js/Dashboard.vue';

new Vue({
    el: '#dashboard', 
    render (h) { return h(Dashboard) }
}); 

