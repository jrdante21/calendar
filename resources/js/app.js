import { createApp } from 'vue'
import CalendarApp from './components/Calendar.vue'

const app = createApp({
    components: { CalendarApp },
})

app.mount('#app')