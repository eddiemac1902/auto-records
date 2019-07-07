<template>
  <div class="container">
    <div v-if="!$gate.isAdminOrAuthor()">
      <not-found></not-found>
    </div>
    <div class="row justify-content-center mt-1" v-else>
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <div class="the-services-class">Arrivals</div>
            <div class="the-button-class">
              <button type="button" class="btn btn-info float-right" @click="newArrival">New Arrival</button>
            </div>
          </div>

          <!-- /.card-header -->
          <div class="card-body p-0">
            <ul class="nav nav-pills mb-3 overall-pills" id="pills-tab" role="tablist">
              <li class="nav-item">
                <a
                  class="nav-link active"
                  id="arrivals-tab"
                  data-toggle="pill"
                  href="#pills-arrivals"
                  role="tab"
                  aria-controls="pills-arrivals"
                  aria-selected="true"
                >Arrivals</a>
              </li>
              <li class="nav-item disabledTab">
                <a
                  class="nav-link"
                  id="services-tab"
                  data-toggle="pill"
                  href="#services-pill"
                  role="tab"
                  aria-controls="services-pill"
                  aria-selected="false"
                >Services Delivered</a>
              </li>
              <li class="nav-item disabledTab">
                <a
                  class="nav-link"
                  id="purchased-items-tab"
                  data-toggle="pill"
                  href="#purchased-pill"
                  role="tab"
                  aria-controls="purchased-pill"
                  aria-selected="false"
                >Purchased Items</a>
              </li>
            </ul>
            <div class="tab-content" id="pills-tabContent">
              <div
                class="tab-pane fade show active"
                id="pills-arrivals"
                role="tabpanel"
                aria-labelledby="arrivals-tab"
              >
                <div class="table-responsive">
                  <table class="table table-hover">
                    <tbody>
                      <tr>
                        <th>Car Brand</th>
                        <th>Model</th>
                        <th>Customer Name</th>
                        <th>Car Number</th>
                        <th>Year of Manufacture</th>
                        <th>Brought In</th>
                        <th>Created At</th>
                        <th>Services</th>
                        <th>Purchased Items</th>
                      </tr>
                      <tr v-for="arrival in arrivals" :key="arrival.id">
                        <td>{{arrival.brand_name}}</td>
                        <td>{{arrival.model_name}}</td>
                        <td>{{arrival.customer_name}}</td>
                        <td>{{arrival.car_number}}</td>
                        <td>{{arrival.manufac_year}}</td>
                        <td>{{arrival.in_date | myDate}}</td>
                        <td>{{arrival.created_at | myDate }}</td>
                        <td>
                          <button
                            type="submit"
                            class="btn btn-primary"
                            @click="ServicesPillToggle(arrival)"
                          >Services</button>
                        </td>
                        <td>
                          <button
                            type="submit"
                            class="btn btn-warning"
                            @click="PurchasesPillToggle(arrival)"
                          >Purchased Items</button>
                        </td>

                        <!-- <td>
                    <a @click="editUserModal(user)">
                      <i class="fa fa-edit text-green"></i>
                    </a>
                    <a @click="deleteUser(user.id,user.name)">
                      <i class="fa fa-trash text-red"></i>
                    </a>
                        </td>-->
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div
                class="tab-pane fade"
                id="services-pill"
                role="tabpanel"
                aria-labelledby="services-tab"
              >
                <div class="row car-details-header">
                  <div class="col-3">
                    <label>Car Brand</label>
                    <input
                      type="text"
                      v-model="current_arrival.brand_name"
                      class="form-control"
                      disabled
                      placeholder=".col-3"
                    >
                  </div>
                  <div class="col-3">
                    <label>Car Number</label>
                    <input
                      type="text"
                      v-model="current_arrival.car_number"
                      class="form-control"
                      disabled
                      placeholder=".col-4"
                    >
                  </div>
                  <div class="col-3">
                    <label>Brought In Date:</label>
                    <input
                      type="text"
                      v-model="current_arrival.in_date"
                      class="form-control"
                      disabled
                      placeholder
                    >
                  </div>
                  <div class="col-3">
                    <button
                      type="button"
                      :disabled="form_service.busy"
                      class="btn btn-success"
                      @click="addService"
                    >Add Service</button>
                  </div>
                </div>

                <div class="table-responsive">
                  <table class="table table-hover">
                    <tbody>
                      <tr>
                        <th>Service Name</th>
                        <th>Price</th>
                        <th>Date</th>
                      </tr>
                      <tr v-for="arr_service in current_services" :key="arr_service.id">
                        <td>{{arr_service.service_name}}</td>
                        <td>{{arr_service.service_price}}</td>
                        <td>{{arr_service.created_at}}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div
                class="tab-pane fade"
                id="purchased-pill"
                role="tabpanel"
                aria-labelledby="services-tab"
              >Purchased Items</div>
            </div>
          </div>
          <!-- /.card-body -->
          <div class="card-footer">
            <!-- <pagination :data="arrivals" @pagination-change-page="getResults"></pagination> -->
          </div>
        </div>
        <!-- /.card -->
      </div>
    </div>

    <div
      class="modal fade"
      id="addArrivalModal"
      tabindex="-1"
      role="dialog"
      aria-labelledby="addArrivalModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 v-show="!editMode" class="modal-title" id="addArrivalModal">Add New Arrival</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <!-- <form @submit.prevent="createUser" @keydown="form.onKeydown($event)" id="createUser-form" > -->
            <div class="alert alert-danger" v-if="form.errors.any()">All fields are required</div>
            <form @keydown="form.onKeydown($event)" id="createArrival-form">
              <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">Car Brand:</label>
                <div class="col-sm-10">
                  <vue-select
                    label="label"
                    v-model="form.selected_brand"
                    :options="car_brands"
                    :on-change="loadModels"
                    :class="{ 'is-invalid': form.errors.has('selected_brand.id') }"
                  ></vue-select>
                  <has-error :form="form" field="selected_brand.id"></has-error>
                </div>
              </div>

              <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">Car Model:</label>
                <div class="col-sm-10">
                  <vue-select
                    label="label"
                    v-model="form.selected_model"
                    :options="car_models"
                    :class="{ 'is-invalid': form.errors.has('selected_model.id') }"
                  ></vue-select>
                  <has-error :form="form" field="selected_model.id"></has-error>
                </div>
              </div>

              <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">Manuf. Year.:</label>
                <div class="col-sm-10">
                  <vue-select
                    label="label"
                    v-model="form.manuf_year"
                    :options="manuf_years"
                    :class="{ 'is-invalid': form.errors.has('manuf_year') }"
                  ></vue-select>
                  <has-error :form="form" field="manuf_year"></has-error>
                </div>
              </div>

              <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">In Date:</label>
                <div class="col-sm-10">
                  <!-- <input type="email" class="form-control" id="inputEmail3" placeholder="Email"> -->
                  <!-- <date-picker
                    v-model="form.in_date"
                    lang="en"
                    type="datetime"
                    format="MM-DD-YYYY [at] HH:mm"
                    :class="{ 'is-invalid': form.errors.has('in_date') }"
                  ></date-picker>-->
                  <!-- <datetime v-model=""></datetime> -->
                  <datetime
                    type="datetime"
                    v-model="form.in_date"
                    input-class="form-control"
                    value-zone="Africa/Accra"
                    zone="Africa/Accra"
                    :format="{ year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: '2-digit', timeZoneName: 'short' }"
                    :week-start="7"
                    use12-hour
                    auto
                  ></datetime>
                  <has-error :form="form" field="in_date"></has-error>
                </div>
              </div>

              <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">Mileage:</label>
                <div class="col-sm-10">
                  <input
                    type="number"
                    v-model="form.mileage"
                    class="form-control"
                    placeholder="Car Mileage"
                  >
                  <!-- <has-error :form="form" field="mileage"></has-error> -->
                </div>
              </div>

              <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">Number Plate:</label>
                <div class="col-sm-10">
                  <input
                    type="text"
                    v-model="form.number_plate"
                    class="form-control"
                    id="form.number_plate"
                    placeholder="Number Plate"
                  >
                  <has-error :form="form" field="number_plate"></has-error>
                </div>
              </div>

              <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">Customer:</label>
                <div class="col-sm-10">
                  <vue-select
                    label="label"
                    v-model="form.customer"
                    :options="customers"
                    :class="{ 'is-invalid': form.errors.has('customer') }"
                  ></vue-select>
                  <has-error :form="form" field="customer"></has-error>
                </div>
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
              @click="createArrival"
            >{{editMode ? 'Edit':'Create'}}</button>
          </div>
        </div>
      </div>
    </div>

    <div
      class="modal fade"
      id="addServiceModal"
      tabindex="-1"
      role="dialog"
      aria-labelledby="addServiceModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5
              v-show="!editModeService"
              class="modal-title"
              id="addServiceModal"
            >Add New Arrival Service</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="alert alert-danger" v-if="form_service.errors.any()">All fields are required</div>
            <form @keydown="form_service.onKeydown($event)" id="createArrival-form">
              <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">Service Type:</label>
                <div class="col-sm-10">
                  <vue-select
                    label="label"
                    v-model="form_service.selected_arrival_service"
                    :options="car_services"
                  ></vue-select>
                  <input type="hidden" v-model="form_service.arrival_id">
                  <!-- <has-error :form="form" field="selected_brand.id"></has-error> -->
                  <div v-if="form_service.errors.has('selected_arrival_service.id')">
                    <span>{{form_service.errors.get('selected_arrival_service.id')}}</span>
                  </div>
                </div>
              </div>

              <!-- <button :disabled="form.busy" type="submit" class="btn btn-primary">Log In</button> -->
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger go-left" data-dismiss="modal">Close</button>
            <button
              type="button"
              :disabled="form_service.busy"
              class="btn"
              :class="!editModeService ? 'btn-primary':'btn-warning'"
              @click="createService"
            >{{editModeService ? 'Edit':'Create'}}</button>
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
import Vue from "vue";
// import DatePicker from "vue2-datepicker";
import VueSelect from "vue-select";
// import moment from "moment";
import Datetime from "vue-datetime";
// You need a specific loader for CSS files
import "vue-datetime/dist/vue-datetime.css";
Vue.use(Datetime);
// import VeeValidate from "vee-validate";
// Vue.use(VeeValidate);
// Vue.component("v-select", VueSelect.VueSelect);

export default {
  components: { VueSelect },
  data() {
    return {
      arrival: {
        customer: { id: 1, label: "Guest" }
      },
      car_services: [],
      form: new Form({
        customer: { id: 1, label: "Guest" },
        selected_brand: {},
        selected_model: {},
        in_date: "",
        mileage: "",
        number_plate: ""
      }),
      form_service: new Form({
        arrival_id: ""
      }),
      editMode: false,
      editModeService: false,
      arrivals: [],
      //   selected_brand: {},
      car_brands: [],
      car_models: [],
      manuf_years: [],
      customers: [],
      current_arrival: {
        services: []
      },
      current_services: []
    };
  },
  created() {
    this.loadUsers();

    // Fire.on("arrivalServiceCreated",()=>{
    //   this.loadServicesForParticularArrival(arrival);
    // });

    axios
      .get("/api/loadCarBrands")
      .then(response => {
        console.log(response);
        this.car_brands = response.data.brands;
        this.manuf_years = response.data.manuf_years;
        this.customers = response.data.customers;
        this.car_services = response.data.car_services;
        // this.$Progress.finish();
      })
      .catch(() => {
        // this.$Progress.finish();
      });
  },

  methods: {
    // Our method to GET results from a Laravel endpoint
    loadModels(value) {
      this.car_models = [];
      this.form.selected_brand = value;
      this.form.selected_model = {};
      if (value) {
        this.$Progress.start();
        console.log(value.id);
        axios
          .get("/api/loadModels/" + value.id)
          .then(response => {
            this.car_models = response.data;
            this.$Progress.finish();
            // console.log(response);
          })
          .catch(() => {
            this.$Progress.finish();
          });
      } else {
      }
    },
    createArrival() {
      this.$Progress.start();
      // this.form.in_date = moment(this.form.in_date).format("YYYY-MM-DD");
      // Submit the form via a POST request
      this.form
        .post("/api/arrivals")
        .then(({ data }) => {
          console.log(data);

          Fire.$emit("userCreatedEvent", "new user created");

          $("#addArrivalModal").modal("hide");
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
    },
    newArrival() {
      this.form.reset();
      $("#addArrivalModal").modal("show");
    },
    editUserModal(user) {
      this.editMode = true;
      this.form.errors.errors = {};
      $("#addUserModal").modal("show");
      this.form.reset();
      this.form.fill(user);
    },
    loadUsers() {
      axios.get("/api/arrivals").then(response => {
        console.log(response);
        this.arrivals = response.data.data;
      });
    },
    ServicesPillToggle(arrival) {
      this.current_services = [];
      $('.nav-pills a[href="#services-pill"]').tab("show");
      this.current_arrival = arrival;
      this.$Progress.start();
      this.loadServicesForParticularArrival(arrival);
    },
    loadServicesForParticularArrival(arrival) {
      axios
        .get("/api/arrivals/services/" + arrival.id)
        .then(response => {
          this.current_services = response.data;
          this.$Progress.finish();
          console.log(response);
        })
        .catch(() => {
          this.$Progress.finish();
        });
    },
    PurchasesPillToggle(arrival) {
      $('.nav-pills a[href="#purchased-pill"]').tab("show");
    },
    addService() {
      this.form_service.reset();
      $("#addServiceModal").modal("show");
    },
    createService() {
      this.form_service.arrival_id = this.current_arrival.id;
      this.form_service
        .post("/api/arrivals/services")
        .then(({ data }) => {
          console.log(data);

          // Fire.$emit("arrivalServiceCreated", "new arrivalservice created");
          this.loadServicesForParticularArrival(this.current_arrival);

          $("#addServiceModal").modal("hide");
          toast({
            type: "success",
            title: "Service added to arrival successfully"
          });
        })
        .catch(error => {
          console.log(error);
        })
        .then(() => {
          this.$Progress.finish();
        });
    }
  },

  computed: {}
};
</script>

<style>
.v-select input[type="search"] {
  height: 25px;
}
.overall-pills {
  padding: 20px;
}
.card-header {
  display: flex;
}
.the-services-class {
  flex: 5;
}
.the-button-class {
  flex: 1;
}
.car-details-header {
  padding-left: 20px;
  padding-right: 20px;
  padding-bottom: 20px;
}
</style>
