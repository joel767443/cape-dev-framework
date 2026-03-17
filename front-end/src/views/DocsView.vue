<script setup>
import { computed, ref } from 'vue'

import installation from '../docs/installation-and-quickstart.md?raw'
import routing from '../docs/routing-and-middleware.md?raw'
import controllers from '../docs/controllers-requests-responses.md?raw'
import validation from '../docs/validation.md?raw'
import database from '../docs/database-and-migrations.md?raw'
import di from '../docs/di-container-and-providers.md?raw'
import auth from '../docs/auth.md?raw'
import queue from '../docs/queue.md?raw'
import testing from '../docs/testing.md?raw'
import upgrade from '../docs/upgrade-guide.md?raw'

const pages = [
  { id: 'installation', title: 'Installation + quickstart', body: installation },
  { id: 'routing', title: 'Routing + middleware', body: routing },
  { id: 'controllers', title: 'Controllers/requests/responses', body: controllers },
  { id: 'validation', title: 'Validation', body: validation },
  { id: 'database', title: 'Database + migrations', body: database },
  { id: 'di', title: 'DI container + providers', body: di },
  { id: 'auth', title: 'Authentication (JWT)', body: auth },
  { id: 'queue', title: 'Queue', body: queue },
  { id: 'testing', title: 'Testing', body: testing },
  { id: 'upgrade', title: 'Upgrade guide', body: upgrade },
]

const selectedId = ref(pages[0].id)
const selected = computed(() => pages.find(p => p.id === selectedId.value) ?? pages[0])
</script>

<template>
  <div class="py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h1 class="h3 mb-0">Docs</h1>
      <RouterLink class="btn btn-outline-secondary btn-sm" to="/">Back</RouterLink>
    </div>

    <div class="row g-3">
      <div class="col-12 col-lg-4">
        <div class="list-group">
          <button
            v-for="p in pages"
            :key="p.id"
            type="button"
            class="list-group-item list-group-item-action"
            :class="{ active: p.id === selectedId }"
            @click="selectedId = p.id"
          >
            {{ p.title }}
          </button>
        </div>
      </div>

      <div class="col-12 col-lg-8">
        <div class="card">
          <div class="card-header fw-semibold">
            {{ selected.title }}
          </div>
          <div class="card-body">
            <pre class="mb-0"><code>{{ selected.body }}</code></pre>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
pre {
  background: #0b1020;
  color: #e8edf6;
  border-radius: 0.5rem;
  padding: 1rem;
  overflow: auto;
  max-height: 70vh;
}
</style>

