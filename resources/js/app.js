require('./bootstrap');

// Import modules...
import Vue from 'vue';
import Vuelidate from 'vuelidate'
import { App as InertiaApp, plugin as InertiaPlugin } from '@inertiajs/inertia-vue';

// Add vuetify to project
import Vuetify from 'vuetify';
import 'vuetify/dist/vuetify.min.css';

Vue.mixin({ methods: { route } });
Vue.use(InertiaPlugin);
Vue.use(Vuetify);
Vue.use(Vuelidate);

const app = document.getElementById('app');

new Vue({
    vuetify: new Vuetify({
        theme: {
            themes: {
                light: {
                    primary: '#1976D2',
                    secondary: '#424242',
                    accent: '#82B1FF',
                    error: '#FF5252',
                    info: '#2196F3',
                    success: '#4CAF50',
                    warning: '#FFC107',
                }
            }
        },
        treeShake: true,
    }),
    render: (h) =>
        h(InertiaApp, {
            props: {
                initialPage: JSON.parse(app.dataset.page),
                resolveComponent: (name) => require(`./Pages/${name}`).default,
            },
        }),
}).$mount(app);
