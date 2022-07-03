import './styles/app.css';

import Vue from 'vue';

import App from './js/App.vue';

/* new Vue({
    el: '#app', 
    data: {
        branches: ["master", "dev"],
        currentBranch: "master",
        commits: null
    },
    template: 'eygfey fey fyef geygf yef'
}); 
*/

new Vue({
    el: '#app', 
    render (h) { return h(App) }
}); 
