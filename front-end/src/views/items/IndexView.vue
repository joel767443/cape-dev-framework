<template>
  <div class="card">

    <div class="card-header">
      <h5>
        Items
        <div class="float-end">
          <RouterLink class="btn btn-success btn-sm" to="/add-item">Add Item</RouterLink>
        </div>
      </h5>
    </div>

    <table class="table table-bordered table-hover">
      <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Description</th>
        <th>Color</th>
        <th>Checked</th>
        <th>Price</th>
        <th>&nbsp;</th>
      </tr>
      </thead>
      <tbody v-if="items.length > 0">
      <tr v-for="(item, index) in this.items" :key="index">
        <td>{{ item.id}}</td>
        <td>{{ item.name}}</td>
        <td>{{ item.description}}</td>
        <td>{{ item.color}}</td>
        <td>{{ item.checked}}</td>
        <td>{{ item.price}}</td>
        <td>
          <RouterLink class="btn btn-sm btn-success" :to="'items/' + item.id + '/edit'">Edit</RouterLink>
          &nbsp;
          <button class="btn btn-sm btn-danger" @click="deleteItem(item.id)" type="button">Delete</button>
        </td>
      </tr>
      </tbody>
      <tbody v-else>
      <tr>
        <td colspan="6">Loading...</td>
      </tr>
      </tbody>
    </table>
  </div>
</template>
<script>
import axios from "axios";
import config from "../../config.js";

export default {
  name: 'items',
  data() {
    return {
      items: []
    }
  },
  mounted() {
    this.getItems()
  },
  methods: {
    getItems() {
      axios.get(`${config.apiBaseUrl}/items`).then (res => {
        this.items = res.data.data
        console.log(res.data)
      });
    },
    deleteItem(itemId) {
      const apiUrl = `${config.apiBaseUrl}/items/delete?id=`+ itemId;

      // Make a DELETE request using Axios
      axios.get(apiUrl)
          .then(response => {
            alert(response.data.message)
            console.log('Item deleted successfully', response);
            window.location.reload();
          })
          .catch(error => {
            // Handle error, e.g., show an error message
            console.error('Error deleting item', error);
          });
    }
  }
}
</script>