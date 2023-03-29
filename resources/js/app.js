// import './bootstrap';
import { createApp } from 'vue/dist/vue.esm-bundler.js'
import App from'./App.vue'
import store from './store/index.js'

const app = createApp(App)

app.use(store)
app.mount('#app')