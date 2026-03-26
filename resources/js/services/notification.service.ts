import { Injectable, signal, computed, inject } from '@angular/core'
import { HttpClient } from '@angular/common/http'
import { firstValueFrom } from 'rxjs'

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

@Injectable({ providedIn: 'root' })
export class NotificationService {
  private http = inject(HttpClient)

  readonly notifications = signal<NotificationItem[]>([])
  readonly preferences = signal<NotificationPreferences>({
    low_stock: true,
    expiring_items: true,
  })
  readonly loading = signal(false)
  readonly preferencesLoading = signal(false)

  readonly unreadCount = computed(() =>
    this.notifications().filter(n => n.read_at === null).length
  )

  async fetchNotifications(): Promise<void> {
    this.loading.set(true)
    try {
      const data = await firstValueFrom(
        this.http.get<NotificationItem[]>('/api/notifications')
      )
      this.notifications.set(data)
    } finally {
      this.loading.set(false)
    }
  }

  async markRead(id: string): Promise<void> {
    await firstValueFrom(this.http.put(`/api/notifications/${id}`, {}))
    this.notifications.update(list =>
      list.map(n =>
        n.id === id ? { ...n, read_at: new Date().toISOString() } : n
      )
    )
  }

  async dismiss(id: string): Promise<void> {
    await firstValueFrom(this.http.delete(`/api/notifications/${id}`))
    this.notifications.update(list => list.filter(n => n.id !== id))
  }

  async markAllRead(): Promise<void> {
    const unread = this.notifications().filter(n => n.read_at === null)
    await Promise.all(unread.map(n => this.markRead(n.id)))
  }

  async fetchPreferences(): Promise<void> {
    this.preferencesLoading.set(true)
    try {
      const data = await firstValueFrom(
        this.http.get<NotificationPreferences>('/api/user/notification-preferences')
      )
      this.preferences.set(data)
    } finally {
      this.preferencesLoading.set(false)
    }
  }

  async updatePreferences(
    updated: Partial<NotificationPreferences>
  ): Promise<void> {
    this.preferencesLoading.set(true)
    try {
      const data = await firstValueFrom(
        this.http.put<NotificationPreferences>(
          '/api/user/notification-preferences',
          { preferences: updated }
        )
      )
      this.preferences.set(data)
    } finally {
      this.preferencesLoading.set(false)
    }
  }
}
