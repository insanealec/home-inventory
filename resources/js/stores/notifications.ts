import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import apiService from '../services/api.js'

export interface NotificationItem {
  id: string
  type: string
  data: {
    type: 'low_stock' | 'expiring_items'
    count: number
    items: Array<Record<string, unknown>>
    window_days?: number
  }
  read_at: string | null
  created_at: string
}

export interface NotificationPreferences {
  low_stock: boolean
  expiring_items: boolean
}

export const useNotificationStore = defineStore('notifications', () => {
  const notifications = ref<NotificationItem[]>([])
  const preferences = ref<NotificationPreferences>({ low_stock: true, expiring_items: true })
  const loading = ref(false)
  const preferencesLoading = ref(false)

  const unreadCount = computed(() =>
    notifications.value.filter((n) => n.read_at === null).length,
  )

  async function fetchNotifications(): Promise<void> {
    loading.value = true
    try {
      notifications.value = await apiService.get('/notifications')
    } finally {
      loading.value = false
    }
  }

  async function markRead(id: string): Promise<void> {
    await apiService.put(`/notifications/${id}`, {})
    const n = notifications.value.find((n) => n.id === id)
    if (n) {
      n.read_at = new Date().toISOString()
    }
  }

  async function dismiss(id: string): Promise<void> {
    await apiService.delete(`/notifications/${id}`)
    notifications.value = notifications.value.filter((n) => n.id !== id)
  }

  async function markAllRead(): Promise<void> {
    const unread = notifications.value.filter((n) => n.read_at === null)
    await Promise.all(unread.map((n) => markRead(n.id)))
  }

  async function fetchPreferences(): Promise<void> {
    preferencesLoading.value = true
    try {
      preferences.value = await apiService.get('/user/notification-preferences')
    } finally {
      preferencesLoading.value = false
    }
  }

  async function updatePreferences(updated: Partial<NotificationPreferences>): Promise<void> {
    preferencesLoading.value = true
    try {
      preferences.value = await apiService.put('/user/notification-preferences', {
        preferences: updated,
      })
    } finally {
      preferencesLoading.value = false
    }
  }

  return {
    notifications,
    preferences,
    loading,
    preferencesLoading,
    unreadCount,
    fetchNotifications,
    markRead,
    dismiss,
    markAllRead,
    fetchPreferences,
    updatePreferences,
  }
})
