import { Component, inject, OnInit, signal, computed } from '@angular/core'
import { CommonModule } from '@angular/common'
import { ActivatedRoute } from '@angular/router'
import { Pagination } from '../../types/common'
import {
  NotificationService,
  NotificationItem,
  LowStockItem,
  ExpiringItem,
} from '../../services/notification.service'
import { ContentComponent } from '../../components/common/content.component'
import { PaginatorComponent } from '../../components/common/paginator.component'

@Component({
  selector: 'app-notifications',
  standalone: true,
  imports: [CommonModule, ContentComponent, PaginatorComponent],
  template: `
    <app-content>
      <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notifications</h1>
          @if (hasUnread()) {
            <button
              type="button"
              class="text-sm text-indigo-600 hover:underline dark:text-indigo-400"
              (click)="markAllRead()"
            >
              Mark all read
            </button>
          }
        </div>

        @if (loading()) {
          <div class="py-16 text-center text-sm text-gray-400">Loading...</div>
        } @else if (paginator().data.length === 0) {
          <div class="py-16 text-center text-sm text-gray-400">
            You have no notifications.
          </div>
        } @else {
          <!-- Notification list -->
          <div class="divide-y divide-gray-200 rounded-lg bg-white shadow dark:divide-gray-700 dark:bg-gray-800">
            @for (n of paginator().data; track n.id) {
              <div
                [ngClass]="n.read_at ? 'bg-white dark:bg-gray-800' : 'bg-indigo-50 dark:bg-indigo-950/30'"
                class="p-4 sm:p-6"
              >
                <div class="flex items-start justify-between gap-4">
                  <div class="min-w-0 flex-1">
                    <!-- Title + unread dot -->
                    <div class="flex items-center gap-2">
                      @if (!n.read_at) {
                        <span class="h-2 w-2 flex-shrink-0 rounded-full bg-indigo-500"></span>
                      }
                      <p class="font-medium text-gray-900 dark:text-white">
                        {{ notificationTitle(n) }}
                      </p>
                    </div>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                      {{ formatDate(n.created_at) }}
                    </p>

                    <!-- Low stock item details -->
                    @if (n.data.type === 'low_stock') {
                      <ul class="mt-3 space-y-1">
                        @for (item of getLowStockItems(n); track item.id) {
                          <li class="text-sm text-gray-700 dark:text-gray-300">
                            <span class="font-medium">{{ item.name }}</span>
                            — {{ item.quantity }}{{ item.unit ? ' ' + item.unit : '' }} remaining
                            (reorder at {{ item.reorder_point }})
                          </li>
                        }
                      </ul>
                    }

                    <!-- Expiring item details -->
                    @if (n.data.type === 'expiring_items') {
                      <ul class="mt-3 space-y-1">
                        @for (item of getExpiringItems(n); track item.id) {
                          <li class="text-sm text-gray-700 dark:text-gray-300">
                            <span class="font-medium">{{ item.name }}</span>
                            — expires {{ formatItemDate(item.expiration_date) }}
                          </li>
                        }
                      </ul>
                    }
                  </div>

                  <!-- Actions -->
                  <div class="flex flex-shrink-0 flex-col items-end gap-2">
                    @if (!n.read_at) {
                      <button
                        type="button"
                        class="text-sm text-indigo-600 hover:underline dark:text-indigo-400"
                        (click)="markRead(n.id)"
                      >
                        Mark read
                      </button>
                    }
                    <button
                      type="button"
                      class="text-sm text-gray-400 hover:text-red-500"
                      (click)="dismiss(n.id)"
                    >
                      Dismiss
                    </button>
                  </div>
                </div>
              </div>
            }
          </div>

          <app-paginator [paginator]="paginator()" [currentPath]="'/notifications'" />
        }
      </div>
    </app-content>
  `,
})
export class NotificationsComponent implements OnInit {
  private notificationService = inject(NotificationService)
  private route = inject(ActivatedRoute)

  loading = signal(true)
  paginator = signal<Pagination<NotificationItem>>({
    data: [],
    current_page: 1,
    last_page: 1,
    per_page: 25,
    total: 0,
    links: [],
  })

  hasUnread = computed(() => this.paginator().data.some(n => n.read_at === null))

  ngOnInit(): void {
    this.route.queryParamMap.subscribe(params => {
      const page = parseInt(params.get('page') ?? '1')
      this.load(page)
    })
  }

  async load(page: number): Promise<void> {
    this.loading.set(true)
    try {
      const result = await this.notificationService.fetchAllNotifications(page)
      this.paginator.set(result)
    } finally {
      this.loading.set(false)
    }
  }

  async markRead(id: string): Promise<void> {
    await this.notificationService.markRead(id)
    this.paginator.update(p => ({
      ...p,
      data: p.data.map(n => n.id === id ? { ...n, read_at: new Date().toISOString() } : n),
    }))
  }

  async markAllRead(): Promise<void> {
    const unread = this.paginator().data.filter(n => n.read_at === null)
    await Promise.all(unread.map(n => this.markRead(n.id)))
  }

  async dismiss(id: string): Promise<void> {
    await this.notificationService.dismiss(id)
    this.paginator.update(p => ({
      ...p,
      data: p.data.filter(n => n.id !== id),
    }))
  }

  notificationTitle(n: NotificationItem): string {
    if (n.data.type === 'low_stock') {
      return `${n.data.count} item${n.data.count === 1 ? '' : 's'} running low`
    }
    if (n.data.type === 'expiring_items') {
      return `${n.data.count} item${n.data.count === 1 ? '' : 's'} expiring soon`
    }
    return 'Notification'
  }

  getLowStockItems(n: NotificationItem): LowStockItem[] {
    return n.data.items as LowStockItem[]
  }

  getExpiringItems(n: NotificationItem): ExpiringItem[] {
    return n.data.items as ExpiringItem[]
  }

  formatDate(iso: string): string {
    return new Date(iso).toLocaleString(undefined, {
      month: 'short',
      day: 'numeric',
      year: 'numeric',
      hour: 'numeric',
      minute: '2-digit',
    })
  }

  formatItemDate(iso: string): string {
    return new Date(iso).toLocaleDateString(undefined, {
      month: 'short',
      day: 'numeric',
      year: 'numeric',
    })
  }
}
