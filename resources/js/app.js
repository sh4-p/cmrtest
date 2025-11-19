import './bootstrap';
import { createApp } from 'vue';
import Welcome from './components/Welcome.vue';

// Create Vue app instance
const app = createApp({});

// Register components
app.component('Welcome', Welcome);

// Mount the app when DOM is ready
if (document.getElementById('app')) {
    app.mount('#app');
}
