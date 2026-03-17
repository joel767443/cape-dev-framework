<template>
  <div class="card">
    <div class="card-header">
      <h4>
        Add Item
        <RouterLink to="/" class="btn float-end btn-success">Back</RouterLink>
      </h4>
    </div>
    <div class="card-body">
      <div v-for="(value, key) in model.item" :key="key" class="mb-3">
        <label :for="key">{{ capitalize(key) }}</label>
        <input
            v-if="key !== 'checked'"
            type="text"
            class="form-control"
            v-model="model.item[key]"
        />
        <input
            v-else
            type="checkbox"
            class="form-check-input"
            v-model="model.item.checked"
        />
      </div>
      <div v-if="errorList" class="alert alert-danger" role="alert">
        <p v-for="(errors, field) in errorList" :key="field">
          <strong>{{ capitalize(field) }}:</strong>
          <span v-for="error in errors" :key="error">{{ error }}</span>
        </p>
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
  name: 'itemCreate',
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
  methods: {
    capitalize(key) {
      return key.charAt(0).toUpperCase() + key.slice(1);
    },
    saveItem() {
      axios.post(`${config.apiBaseUrl}/items/create`, this.model.item, {
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json;charset=UTF-8",
        },
      })
          .then(res => {
            alert(res.data.message);
            this.clearForm();
          })
          .then(erro => {
            console.log(erro)
          })
          .catch(error => {
            if (error.response.status === 422) {
              this.errorList = error.response.data.errors;
              // input validation error
            }
          });
    },
    clearForm() {
      this.model.item = {
        name: '',
        description: '',
        brand: '',
        color: '',
        checked: '',
        price: '',
        availability: '',
      };
    }
  }
}
</script>
