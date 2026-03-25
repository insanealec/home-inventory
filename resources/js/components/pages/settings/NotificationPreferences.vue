<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useNotificationStore } from '../../../stores/notifications'

const store = useNotificationStore()
const saved = ref(false)

onMounted(() => store.fetchPreferences())

const LABELS: Record<string, { title: string; description: string }> = {
  low_stock: {
    title: 'Low stock alerts',
    description:
      'Get notified when an item falls at or below its reorder point. Useful for keeping consumables and essentials stocked.',
  },
  expiring_items: {
    title: 'Expiry alerts',
    description:
      'Get notified when items are approaching their expiration date (within 30 days by default). Useful for food, medicine, and perishables.',
  },
}

async function toggle(key: 'low_stock' | 'expiring_items') {
  saved.value = false
  await store.updatePreferences({ [key]: !store.preferences[key] })
  saved.value = true
  setTimeout(() => (saved.value = false), 2000)
}
</script>

<template>
  <div class="mx-auto max-w-2xl px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notification preferences</h1>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
      Choose which notifications you receive. Notifications are sent by email and appear in the
      notification bell above.
    </p>

    <div class="mt-8 space-y-4">
      <div
        v-for="(meta, key) in LABELS"
        :key="key"
        class="flex items-start justify-between rounded-lg border bg-white p-4 dark:border-gray-700 dark:bg-gray-800"
      >
        <div class="min-w-0 flex-1 pr-4">
          <p class="text-sm font-medium text-gray-900 dark:text-white">{{ meta.title }}</p>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ meta.description }}</p>
        </div>
        <button
          type="button"
          class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
          :class="store.preferences[key as keyof typeof store.preferences] ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-600'"
          :disabled="store.preferencesLoading"
          :aria-checked="store.preferences[key as keyof typeof store.preferences]"
          role="switch"
          @click="toggle(key as 'low_stock' | 'expiring_items')"
        >
          <span
            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
            :class="store.preferences[key as keyof typeof store.preferences] ? 'translate-x-5' : 'translate-x-0'"
          />
        </button>
      </div>
    </div>

    <p v-if="saved" class="mt-4 text-sm text-green-600 dark:text-green-400">
      Preferences saved.
    </p>

    <p class="mt-6 text-sm text-gray-500 dark:text-gray-400">
      Notifications are sent daily at 8:00 AM. You can manage your inventory items and reorder
      points in the
      <router-link to="/inventory" class="text-indigo-600 hover:underline dark:text-indigo-400">
        Inventory
      </router-link>
      section.
    </p>
  </div>
</template>
