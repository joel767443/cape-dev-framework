import { createRouter, createWebHistory } from 'vue-router'
import IndexView from '../views/items/IndexView.vue'
import CreateView from '../views/items/CreateView.vue'
import EditView from '../views/items/EditView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'items',
      component: IndexView
    },
    {
      path: '/add-item',
      name: 'addItem',
      component: CreateView
    },
    {
      path: '/items/:id/edit',
      name: 'add-item',
      component: EditView
    },
  ]
})

export default router
