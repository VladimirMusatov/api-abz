<template>
    <div v-if="token != null" class="card mt-3">
        <div class="card-header">
            API Token
        </div>
        <div class="card-body">
            <p class="card-text">{{ token }}</p>
        </div>
        </div>
</template>
<script>
  export default {
    data() {
      return {
        token: null,
      };
    },
    mounted() {
        this.get_token();
    },
    methods: {
        get_token()
        {
            fetch('/api/token')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
            })
            .then(data => {
                this.token = data.token;
            })
            .catch(error => {
                console.error('Fetch error:', error);
            });
        }
    },
}
</script>  