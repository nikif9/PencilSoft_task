import { createApp } from 'vue'
import Toast, { POSITION } from 'vue-toastification';
import 'vue-toastification/dist/index.css';
import App from './App.vue'

const application = createApp(App)
application.use(Toast, {
    position: POSITION.TOP_RIGHT
  });
  application.mount('#app');