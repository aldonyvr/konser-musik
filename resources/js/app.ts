import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
// ...other imports...

const app = createApp(App)
app.use(router)
// ...rest of configuration...
app.mount('#app')
