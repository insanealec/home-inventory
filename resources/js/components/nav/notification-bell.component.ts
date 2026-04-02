import { Component, inject, OnInit } from '@angular/core'
import { CommonModule } from '@angular/common'
import { RouterLink } from '@angular/router'
import { signal } from '@angular/core'
import { NotificationService, NotificationItem } from '../../services/notification.service'

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
                <!-- Unread dot -->
                <div class="mt-1.5 flex-shrink-0">
                  @if (!n.read_at) {
                    <span class="block h-2 w-2 rounded-full bg-indigo-500"></span>
                  } @else {
                    <span class="block h-2 w-2"></span>
                  }
                </div>
                <div class="min-w-0 flex-1">
                  <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ notificationTitle(n) }}
                  </p>
                  <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                    {{ relativeTime(n.created_at) }}
                  </p>
                </div>
                <button
                  type="button"
                  class="flex-shrink-0 text-xs text-gray-400 hover:text-red-500"
                  (click)="dismiss(n.id)"
                >
                  Dismiss
                </button>
              </li>
            }
          </ul>

          <!-- Footer -->
          <div class="flex items-center justify-between border-t px-4 py-2 dark:border-gray-700">
            <a
              routerLink="/notifications"
              (click)="close()"
              class="text-xs font-medium text-indigo-600 hover:underline dark:text-indigo-400"
            >
              View all notifications
            </a>
            <a
              routerLink="/settings/notifications"
              (click)="close()"
              class="text-xs text-gray-400 hover:underline dark:text-gray-500"
            >
              Settings
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

  markAllRead(): void {
    this.notificationService.markAllRead()
  }

  dismiss(id: string): void {
    this.notificationService.dismiss(id)
  }

  relativeTime(iso: string): string {
    const now = new Date()
    const date = new Date(iso)
    const diffMs = now.getTime() - date.getTime()
    const minutes = Math.floor(diffMs / 60000)
    const hours = Math.floor(minutes / 60)
    const days = Math.floor(hours / 24)

    if (minutes < 1) { return 'Just now' }
    if (minutes < 60) { return `${minutes}m ago` }
    if (hours < 24) { return `${hours}h ago` }
    if (days === 1) { return 'Yesterday' }
    if (days < 7) { return `${days} days ago` }
    return date.toLocaleDateString(undefined, { month: 'short', day: 'numeric' })
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
}
