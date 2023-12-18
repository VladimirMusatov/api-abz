import './bootstrap';
import ApiToken from './components/api-token.vue'
import Users from './components/users.vue'
import CreateUser from './components/create-user.vue'

import { createApp } from 'vue'

const app = createApp({})

app.component('api-token', ApiToken)
app.component('users', Users)
app.component('create-user', CreateUser)

app.mount('#app')