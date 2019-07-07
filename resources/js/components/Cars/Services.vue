<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-10">
        <div class="card card-default">
          <div class="card-header">
            <div class="the-services-class">Services</div>
            <div class="the-button-class">
              <button
                type="button"
                class="btn btn-block btn-primary btn-flat pull-right"
                @click="newServiceModal"
              >Add new Car Service</button>
            </div>
          </div>

          <div class="card-body">
            <table class="table table-hover">
              <tbody>
                <tr>
                  <th>ID</th>
                  <th>Service Name</th>
                  <th>Price</th>
                  <th>Created At</th>
                  <th>Action</th>
                </tr>
                <tr v-for="service in services" :key="service.id">
                  <td>{{service.id}}</td>
                  <td>{{service.name}}</td>
                  <td>{{service.price}}</td>
                  <td>{{service.created_at}}</td>
                  <td>
                    <button
                      type="button"
                      class="btn btn-primary"
                      @click="editServiceModal(service)"
                    >
                      <i class="fa fa-edit text-white"></i>
                      Edit
                    </button>
                    <button
                      type="button"
                      class="btn btn-danger"
                      @click="deleteService(service.id,service.name)"
                    >
                      <i class="fa fa-trash text-white"></i>
                      Delete
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- modals -->
      <div
        class="modal fade"
        id="addServiceModal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="addUserModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 v-show="!editMode" class="modal-title" id="addUserModalLabel">Add New Service</h5>
              <h5 v-show="editMode" class="modal-title" id="addUserModalLabel">Edit Service</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form @keydown="form.onKeydown($event)" id="createService-form">
                <div class="form-group row">
                  <label class="col-sm-2 col-form-label">Name</label>
                  <div class="col-sm-10">
                    <input
                      v-model="form.name"
                      placeholder="Service Name"
                      type="text"
                      name="name"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.has('name') }"
                    >
                  </div>

                  <has-error :form="form" field="name"></has-error>
                </div>

                <div class="form-group row">
                  <label class="col-sm-2 col-form-label">Price(GHS)</label>
                  <div class="col-sm-10">
                    <input
                      v-model="form.price"
                      placeholder="Service price"
                      type="text"
                      name="price"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.has('price') }"
                    >
                  </div>

                  <has-error :form="form" field="price"></has-error>
                </div>

                <!-- <button :disabled="form.busy" type="submit" class="btn btn-primary">Log In</button> -->
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger go-left" data-dismiss="modal">Close</button>
              <button
                type="button"
                :disabled="form.busy"
                class="btn"
                :class="!editMode ? 'btn-primary':'btn-warning'"
                @click="!editMode ? createService():updateService(form.id)"
              >{{editMode ? 'Update':'Create'}}</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  mounted() {
    this.loadServices();
    Fire.$on("serviceCreatedEvent", data => {
      //   alert(data);
      // this.loadUsers();
      this.loadServices();
    });
  },
  methods: {
    loadServices() {
      this.$Progress.start();
      axios
        .get("/api/services")
        .then(response => {
          this.services = response.data;
          this.$Progress.finish();
        })
        .catch(() => {
          this.$Progress.finish();
        });
    },
    createService() {
      this.$Progress.start();
      // Submit the form via a POST request
      this.form
        .post("/api/services")
        .then(response => {
          console.log(response);

          Fire.$emit("serviceCreatedEvent", "new service created");

          $("#addServiceModal").modal("hide");
          toast({
            type: "success",
            title: "Service created successfully"
          });
        })
        .catch(error => {
          console.log(error);
        })
        .then(() => {
          this.$Progress.finish();
        });
      //fire event
    },
    newServiceModal() {
      this.form.reset();
      this.editMode = false;
      $("#addServiceModal").modal("show");
    },
    editServiceModal(service) {
      this.editMode = true;
      this.form.errors.errors = {};
      $("#addServiceModal").modal("show");
      this.form.reset();
      this.form.fill(service);
    },
    deleteService(id, name) {
      swal({
        title: "Are you sure?",
        html: "Do you really want to delete this service",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
      }).then(result => {
        if (result.value) {
          this.$Progress.start();
          this.form
            .delete("/api/services/" + id)
            .then(response => {
              toast({
                type: "success",
                title: "Service deleted successfully"
              });
              Fire.$emit("serviceCreatedEvent", "service deleted");
              console.log(response);
            })
            .catch(error => {
              toast({
                type: "error",
                title: "Something went wrong, unable to delete User"
              });
            })
            .then(response => {
              this.$Progress.finish();
            });
        }
      });
    },
    updateService(service_id) {
      this.$Progress.start();
      this.form
        .put("/api/services/" + service_id)
        .then(response => {
          toast({
            type: "success",
            title: "Service updated successfully"
          });
          $("#addServiceModal").modal("hide");
          Fire.$emit("serviceCreatedEvent", "service updated");
        })
        .catch(error => {
          //   this.$Progress.error();
        })
        .then(() => {
          this.$Progress.finish();
        });
    }
  },
  data() {
    return {
      editMode: false,
      services: [],
      form: new Form({
        id: "",
        name: "",
        price: "",
        created_at: ""
      })
    };
  }
};
</script>

<style>
.card-header {
  display: flex;
}
.the-services-class {
  flex: 5;
}
.the-button-class {
  flex: 1;
}
</style>

