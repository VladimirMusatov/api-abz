<template>
<div>
<!-- Button trigger modal -->
<button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#UserStoreModal">Create User
</button>

<!-- Modal -->
<div class="modal fade" id="UserStoreModal" tabindex="-1" aria-labelledby="UserStoreLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Create User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div v-if="loading" class="text-center d-flex justify-content-center">
            <div class="spinner-border text-primary" role="status"/>
            <span style="position: relative; top: 6px; left:6px">Loading...</span>
        </div>
        <div v-else>
          <form id="storeUser" action="#" @submit.prevent="storeUser" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" v-model="email" class="form-control" :class="{'is-invalid': validationErrors && validationErrors.email }" id="email">
                <div v-if="validationErrors && validationErrors.email" class='text text-danger' >
                  {{ validationErrors.email.join(', ') }}
                </div>
            </div>
            <div class="mb-3">
              <label for="name" class="form-label">Name</label>
              <input type="text" v-model="name" class="form-control" :class="{'is-invalid': validationErrors && validationErrors.name }" id="name">
              <div v-if="validationErrors && validationErrors.name" class='text text-danger'>
                {{ validationErrors.name.join(', ') }}
              </div>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Phone</label>
                <input type="tel" v-model="phone" class="form-control" :class="{'is-invalid': validationErrors && validationErrors.phone }" id="phone" >
                <div v-if="validationErrors && validationErrors.phone" class='text text-danger' >
                  {{ validationErrors.phone.join(', ') }}
                </div>
            </div>
            <div class="mb-3">
              <label for="position_id" class="form-label">Position Id</label>
              <input class="form-control" v-model="position_id" type="text" :class="{'is-invalid': validationErrors && validationErrors.position_id }" id="position_id">
              <div v-if="validationErrors && validationErrors.position_id" class='text text-danger' >
                  {{ validationErrors.position_id.join(', ') }}
                </div>
          </div>
            <div class="mb-3">
              <label for="photo" class="form-label">Photo</label>
              <input class="form-control"  @change="handleFileChange" type="file" :class="{'is-invalid': validationErrors && validationErrors.photo }" id="photo">
              <div v-if="validationErrors && validationErrors.photo" class='text text-danger' >
                  {{ validationErrors.photo.join(', ') }}
                </div>
            </div>
          </form>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button form="storeUser" type="submit" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
</div>
</template>
<script>
  export default {
    data() {
      return {
        name:  '',
        email: '', 
        phone: '',
        position_id: '',
        photo: '',
        token: null,
        validationErrors: null,
        loading: false,
      };
    },
    mounted() {

    },
    methods: {
      async storeUser() {
        this.loading = true;

        try {
          let token = await this.get_token();
          const response = await this.sendUserData(token);

          if (response.success) {
            this.name  = null;
            this.email = null;
            this.phone = null;
            this.photo = null;
            this.position_id = null;

            this.$emit('user-created');

            $("#UserStoreModal").modal("hide");
          } else {
            const validationErrors = response.fails;
            this.validationErrors = validationErrors;
            console.log('responsesendUserData: ', response.fails);
          }
        } catch (error) {
            console.error('An error occurred:', error);
        } finally {
          this.loading = false;
        }
      },
      async get_token()
        {
          try {
              const response = await fetch('/api/token');

              if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
              }

              const data = await response.json();

              this.token = data.token;
              return data.token;

          } catch (error) {

            console.error('Fetch error:', error);
          }
        },
      handleFileChange(event) {
        this.photo = event.target.files[0];
      },
      async sendUserData(token)
      {
        try {
            const formData = new FormData();
            formData.append('name', this.name);
            formData.append('email', this.email);
            formData.append('phone', this.phone);
            formData.append('photo', this.photo);
            formData.append('position_id', this.position_id);

          const csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;

          const requestOptions = {
            method: 'POST',
            headers: {
              'Authorization': `Bearer ${token}`,
              'X-CSRF-TOKEN': csrfToken,
            },
            body: formData,
          };

          const response = await fetch('/api/users', requestOptions);

          if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
          }

          const responseData = await response.json();

          console.log('Response data:', responseData);

          return responseData;

        } catch (error) {
          console.error('Fetch error:', error);
        }
      },
    },
}
</script>  