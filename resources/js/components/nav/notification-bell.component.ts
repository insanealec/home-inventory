import { Component, inject, OnInit } from '@angular/core'
import { CommonModule } from '@angular/common'
import { RouterLink } from '@angular/router'
import { signal } from '@angular/core'
import { NotificationService } from '../../services/notification.service'

interface Notification {
  id: number
  read_at: string | null
  created_at: string
  data: {
    type: string
    count: number
  }
}

@Component({
  selector: 'app-notification-bell',
  standalone: true,
  imports: [CommonModule, RouterLink],
  template: `
    <div class="relative">
      <!-- Bell button -->
      <button
        type="button"
        class="relative rounded-full p-1 text-gray-500 hover:text-gray-700 focus:outline-none dark:text-gray-400 dark:hover:text-gray-200"
        aria-label="Notifications"
        (click)="toggle()"
      >
        <!-- Bell icon -->
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"
          />
        </svg>
        <!-- Unread badge -->
        @if (notificationService.unreadCount() > 0) {
          <span
            class="absolute -right-0.5 -top-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white"
          >
            {{ notificationService.unreadCount() > 9 ? '9+' : notificationService.unreadCount() }}
          </span>
        }
      </button>

      <!-- Dropdown -->
      @if (open()) {
        <div
          class="absolute right-0 z-50 mt-2 w-80 rounded-lg bg-white shadow-lg ring-1 ring-black/5 dark:bg-gray-800 dark:ring-white/10"
        >
          <!-- Header -->
          <div class="flex items-center justify-between border-b px-4 py-3 dark:border-gray-700">
            <span class="text-sm font-semibold text-gray-900 dark:text-white">Notifications</span>
            @if (notificationService.unreadCount() > 0) {
              <button
                type="button"
                class="text-xs text-indigo-600 hover:underline dark:text-indigo-400"
                (click)="markAllRead()"
              >
                Mark all read
              </button>
            }
          </div>

          <!-- List -->
          <ul class="max-h-96 divide-y overflow-y-auto dark:divide-gray-700">
            @if (notificationService.notifications().length === 0) {
              <li class="px-4 py-6 text-center text-sm text-gray-400">
                No notifications
              </li>
            }
            @for (n of notificationService.notifications(); track n.id) {
              <li
                [ngClass]="n.read_at ? 'bg-white dark:bg-gray-800' : 'bg-indigo-50 dark:bg-indigo-950/30'"
                class="flex items-start gap-3 px-4 py-3 transition-colors"
              >
                <div class="min-w-0 flex-1">
                  <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ notificationTitle(n) }}
                  </p>
                  <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                    {{ formatDate(n.created_at) }}
                  </p>
                </div>
                <div class="flex flex-col items-end gap-1">
                  @if (!n.read_at) {
                    <button
                      type="button"
                      class="text-xs text-indigo-600 hover:underline dark:text-indigo-400"
                      (click)="markRead(n.id)"
                    >
                      Read
                    </button>
                  }
                  <button
                    type="button"
                    class="text-xs text-gray-400 hover:text-red-500"
                    (click)="dismiss(n.id)"
                  >
                    Dismiss
                  </button>
                </div>
              </li>
            }
          </ul>

          <!-- Footer -->
          <div class="border-t px-4 py-2 dark:border-gray-700">
            <a
              routerLink="/settings/notifications"
              (click)="close()"
              class="text-xs text-indigo-600 hover:underline dark:text-indigo-400"
            >
              Notification settings
            </a>
          </div>
        </div>
      }

      <!-- Click-outside overlay -->
      @if (open()) {
        <div class="fixed inset-0 z-40" (click)="close()"></div>
      }
    </div>
  `,
})
export class NotificationBellComponent implements OnInit {
  notificationService = inject(NotificationService)
  open = signal(false)

  ngOnInit(): void {
    this.notificationService.fetchNotifications()
  }

  toggle(): void {
    this.open.set(!this.open())
  }

  close(): void {
    this.open.set(false)
  }

  markRead(id: number): void {
    this.notificationService.markRead(id)
  }

  markAllRead(): void {
    this.notificationService.markAllRead()
  }

  dismiss(id: number): void {
    this.notificationService.dismiss(id)
  }

  formatDate(iso: string): string {
    return new Date(iso).toLocaleDateString(undefined, { month: 'short', day: 'numeric' })
  }

  notificationTitle(n: Notification): string {
    if (n.data.type === 'low_stock') {
      return `${n.data.count} item${n.data.count === 1 ? '' : 's'} running low`
    }
    if (n.data.type === 'expiring_items') {
      return `${n.data.count} item${n.data.count === 1 ? '' : 's'} expiring soon`
    }
    return 'Notification'
  }
}
