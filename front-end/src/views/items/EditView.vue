<template>
  <div class="card">
    <div class="card-header">
      <h4>
        Edit Item
        <router-link to="/" class="btn float-end btn-success">Back</router-link>
      </h4>
    </div>
    <div class="card-body">
      <div v-if="errorList" class="text-danger">
        <ul>
          <li v-for="(error, field) in errorList" :key="field">{{ error[0] }}</li>
        </ul>
      </div>
      <div class="mb-3">
        <label for="name">Name</label>
        <input type="text" class="form-control" v-model="model.item.name"/>
      </div>
      <div class="mb-3">
        <label for="description">Description</label>
        <input type="text" class="form-control" v-model="model.item.description"/>
      </div>
      <div class="mb-3">
        <label for="brand">Brand</label>
        <input type="text" class="form-control" v-model="model.item.brand"/>
      </div>
      <div class="mb-3">
        <label for="color">Color</label>
        <input type="text" class="form-control" v-model="model.item.color"/>
      </div>
      <div class="mb-3">
        <label for="checked">Checked</label>
        <input type="checkbox" class="form-check-input" v-model="model.item.checked"/>
        <input type="hidden" :value="model.item.checked ? '1' : '0'" name="checked" />
      </div>
      <div class="mb-3">
        <label for="availability">Availability</label>
        <input type="text" class="form-control" v-model="model.item.availability"/>
      </div>
      <div class="mb-3">
        <label for="price">Price</label>
        <input type="text" class="form-control" v-model="model.item.price"/>
      </div>
    </div>
    <div class="card-footer">
      <button type="button" @click="saveItem" class="btn btn-success form-control">Save</button>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import config from "../../config.js";

export default {
  name: 'itemEdit',
  data() {
    return {
      errorList: '',
      model: {
        item: {
          name: '',
          description: '',
          brand: '',
          color: '',
          checked: '',
          price: '',
          availability: '',
        }
      }
    }
  },
  watch: {
    '$route.params.id': 'loadItemData'
  },
  created() {
    this.loadItemData();
  },
  methods: {
    loadItemData() {
      const itemId = this.$route.params.id;
      if (itemId) {

        axios.get(`${config.apiBaseUrl}/item?id=${itemId}`)
            .then(response => {
              console.log(response)
              this.model.item = response.data;
            })
            .catch(error => {
              console.error('Error loading item data:', error);
            });
      }
    },
    saveItem() {
      const $this = this;
      axios.post(`${config.apiBaseUrl}/items/update`, this.model.item)
          .then(res => {
            alert(res.data.message);
          })
          .catch(function (error) {
            if (error.response.status === 422) {
              $this.errorList = error.response.data.errors;
              // Handle input validation errors
            }
          });
    }
  }
}
</script>
