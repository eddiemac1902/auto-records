<template>
  <div class="container">
    <div class="row justify-content-center mt-1">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Users</h3>

            <div class="card-tools">
              <div class="input-group input-group-sm" style="width: 300px;">
                <button class="btn btn-info" data-toggle="modal" data-target="#addUserModal">
                  <i class="fa fa-user-plus">Add User</i>
                </button>
                <input
                  type="text"
                  name="table_search"
                  class="form-control float-right"
                  placeholder="Search"
                >

                <div class="input-group-append">
                  <button type="submit" class="btn btn-default">
                    <i class="fa fa-search"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body table-responsive p-0">
            <table class="table table-hover">
              <tbody>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Type</th>
                  <th>Registered On</th>
                  <th>Modify</th>
                </tr>

                <tr v-for="user in users" :key="user.id">
                  <td>{{user.id}}</td>
                  <td>{{user.name | upText}}</td>
                  <td>{{user.email}}</td>
                  <td>{{user.type}}</td>
                  <td>{{user.created_at | myDate }}</td>
                  <td>
                    <a href="#">
                      <i class="fa fa-edit text-green"></i>
                    </a>
                    <a @click="deleteUser(user.id,user.name)">
                      <i class="fa fa-trash text-red"></i>
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
    </div>

    <div
      class="modal fade"
      id="addUserModal"
      tabindex="-1"
      role="dialog"
      aria-labelledby="addUserModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <!-- <form @submit.prevent="createUser" @keydown="form.onKeydown($event)" id="createUser-form" > -->
            <form @keydown="form.onKeydown($event)" id="createUser-form">
              <div class="form-group">
                <input
                  v-model="form.name"
                  placeholder="Name"
                  type="text"
                  name="name"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('name') }"
                >
                <has-error :form="form" field="name"></has-error>
              </div>

              <div class="form-group">
                <input
                  v-model="form.email"
                  placeholder="Email"
                  type="email"
                  name="email"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('email') }"
                >
                <has-error :form="form" field="email"></has-error>
              </div>

              <div class="form-group">
                <input
                  v-model="form.password"
                  placeholder="Password"
                  type="password"
                  name="password"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('password') }"
                >
                <has-error :form="form" field="password"></has-error>
              </div>

              <div class="form-group">
                <input
                  v-model="form.password_confirmation"
                  placeholder="Confirm Password"
                  type="password"
                  name="password_confirmation"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('password_confirmation') }"
                >
                <has-error :form="form" field="password_confirmation"></has-error>
              </div>

              <div class="form-group">
                <select
                  v-model="form.type"
                  placeholder="Type"
                  type="text"
                  name="type"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('type') }"
                >
                  <option value="admin">Admin</option>
                </select>
                <has-error :form="form" field="type"></has-error>
              </div>

              <div class="form-group">
                <textarea
                  v-model="form.bio"
                  placeholder="Bio (optional)"
                  type="text"
                  name="bio"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('bio') }"
                ></textarea>
                <has-error :form="form" field="bio"></has-error>
              </div>

              <!-- <button :disabled="form.busy" type="submit" class="btn btn-primary">Log In</button> -->
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger go-left" data-dismiss="modal">Close</button>
            <button
              type="button"
              :disabled="form.busy"
              class="btn btn-primary"
              @click="createUser"
            >Create</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
/* .go-left
    {
        margin-left:-100px;
    } */
</style>

<script>
export default {
  data() {
    return {
      users: {},
      // Create a new form instance
      form: new Form({
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
        type: "",
        bio: "",
        photo: "",
        remember: false
      })
    };
  },
  created() {
    this.loadUsers();
    // setInterval(() => this.loadUsers(), 3000);
    Fire.$on("userCreatedEvent", data => {
      //   alert(data);
      this.loadUsers();
    });
  },
  methods: {
    loadUsers() {
      axios.get("/api/users").then(({ data }) => (this.users = data.data));
      //   axios
      //     .get("/api/users")
      //     .then(function(response) {
      //       // handle success
      //       this.users = response.data;
      //       console.log(response.data);
      //     })
      //     .catch(function(error) {
      //       // handle error
      //       console.log(error);
      //     })
      //     .then(function() {
      //       // always executed
      //     });
    },
    createUser() {
      this.$Progress.start();
      // Submit the form via a POST request
      this.form
        .post("/api/users")
        .then(({ data }) => {
          console.log(data);

          Fire.$emit("userCreatedEvent", "new user created");

          $("#addUserModal").modal("hide");
          toast({
            type: "success",
            title: "User created successfully"
          });

          this.loadUsers();
        })
        .catch(error => {
          console.log(error);
        })
        .then(() => {
          this.$Progress.finish();
        });
      //fire event
    },
    deleteUser(id, name) {
      swal({
        title: "Are you sure?",
        html: "Do you really want to delete user <b>" + name + "</b>!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
      }).then(result => {
        if (result.value) {
          this.$Progress.start();
          this.form
            .delete("/api/users/" + id)
            .then(response => {
              toast({
                type: "success",
                title: "User deleted successfully"
              });
              Fire.$emit("userCreatedEvent", "user deleted");
              console.log(response);
            })
            .catch(error => {})
            .then(response => {
              this.$Progress.finish();
            });
        }
      });
    }
  }
};
</script>
