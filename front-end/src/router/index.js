import { createRouter, createWebHistory } from 'vue-router'
import LandingView from '../views/LandingView.vue'
import DocsView from '../views/DocsView.vue'
import IndexView from '../views/items/IndexView.vue'
import CreateView from '../views/items/CreateView.vue'
import EditView from '../views/items/EditView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'landing',
      component: LandingView
    },
    {
      path: '/docs',
      name: 'docs',
      component: DocsView
    },
    {
      path: '/items',
      name: 'items',
      component: IndexView
    },
    {
      path: '/items/new',
      name: 'itemCreate',
      component: CreateView
    },
    {
      path: '/items/:id/edit',
      name: 'itemEdit',
      component: EditView
    },
  ]
})

export default router
