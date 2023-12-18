<template>
        <div v-if="loading"  class="d-flex align-items-center justify-content-center" style="min-height: 90vh;">
            <div class="spinner-border text-primary" role="status"/>
            <span style="position: relative; top: 6px; left:6px">Loading...</span>
        </div>
        <div v-else>
            <create-user @user-created="handle"></create-user>
            <div class="mt-3" v-for="user in users" :key="user.id">
                <div class="card mb-3" style="max-width: 540px;">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img :src="user.photo" class="img-fluid rounded-start" alt="...">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="card-title">{{user.name }}</h5>
                                <p class="card-text">{{ user.email }}</p>
                                <p class="card-text"><small class="text-muted">{{ user.phone }}</small></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" @click="get_users(count + 6)" class="btn btn-primary mb-3 mt-3">Show More</button>
        </div>
</template>
<script>
  export default {
    data() {
      return {
        users: [],
        loading: true,
        count : 6,
      };
    },
    mounted() {
        this.get_users(this.count);
    },
    methods: {
        handle() {
            this.get_users(this.count);
        },

        get_users(count)
        {
            this.loading = true;

            const postData = {
                page: 1,
                count: count
            };

            this.count = count;

            const url = '/api/users';

            axios.get(url, {
                params: postData
                }).then(response => {
                    this.users = response.data.users;
                })
                .catch(error => {
                    console.error('Ошибка при отправке POST запроса:', error);
                }).finally(() => {
                    this.loading = false;
                })
        },
    },
}
</script>  