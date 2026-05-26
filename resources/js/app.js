import { createApp } from 'vue';
import './bootstrap';
import Meteorologia from './componentes/Meteorologia.vue';

const app = createApp(Meteorologia);
app.mount('#app');