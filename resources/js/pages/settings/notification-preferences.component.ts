import { Component, OnInit, inject, signal } from '@angular/core'
import { CommonModule } from '@angular/common'
import { RouterLink } from '@angular/router'
import { NotificationService } from '../../services/notification.service'
import { ContentComponent } from '../../components/common/content.component'

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

@Component({
  selector: 'app-notification-preferences',
  standalone: true,
  imports: [CommonModule, RouterLink, ContentComponent],
  template: `
    <app-content>
    <div class="mx-auto max-w-2xl px-4 py-8">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notification preferences</h1>
      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
        Choose which notifications you receive. Notifications are sent by email and appear in the
        notification bell above.
      </p>

      <div class="mt-8 space-y-4">
        @for (meta of preferenceKeys; track meta.key) {
          <div class="flex items-start justify-between rounded-lg border bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <div class="min-w-0 flex-1 pr-4">
              <p class="text-sm font-medium text-gray-900 dark:text-white">{{ meta.label.title }}</p>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ meta.label.description }}</p>
            </div>
            <button
              type="button"
              (click)="toggle(meta.key)"
              class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
              [class]="
                isEnabled(meta.key) === true
                  ? 'bg-indigo-600'
                  : 'bg-gray-200 dark:bg-gray-600'
              "
              [disabled]="notificationService.preferencesLoading()"
              [attr.aria-checked]="isEnabled(meta.key)"
              role="switch"
            >
              <span
                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                [class]="
                  isEnabled(meta.key) === true
                    ? 'translate-x-5'
                    : 'translate-x-0'
                "
              ></span>
            </button>
          </div>
        }
      </div>

      @if (saved()) {
        <p class="mt-4 text-sm text-green-600 dark:text-green-400">
          Preferences saved.
        </p>
      }

      <p class="mt-6 text-sm text-gray-500 dark:text-gray-400">
        Notifications are sent daily at 8:00 AM. You can manage your inventory items and reorder
        points in the
        <a routerLink="/inventory" class="text-indigo-600 hover:underline dark:text-indigo-400">
          Inventory
        </a>
        section.
      </p>
    </div>
    </app-content>
  `,
})
export class NotificationPreferencesComponent implements OnInit {
  notificationService = inject(NotificationService)

  preferences = signal({
    low_stock: false,
    expiring_items: false,
  })

  saved = signal(false)

  preferenceKeys = [
    { key: 'low_stock', label: LABELS.low_stock },
    { key: 'expiring_items', label: LABELS.expiring_items },
  ]

  ngOnInit(): void {
    this.notificationService.fetchPreferences()
    const prefs = this.notificationService.preferences()
    this.preferences.set({
      low_stock: prefs.low_stock,
      expiring_items: prefs.expiring_items,
    })
  }

  isEnabled(key: string): boolean {
    return this.preferences()[key as keyof ReturnType<typeof this.preferences>] === true
  }

  async toggle(key: 'low_stock' | 'expiring_items'): Promise<void> {
    this.saved.set(false)
    const currentPrefs = this.preferences()
    const newVal = !currentPrefs[key]

    this.preferences.update(p => ({
      ...p,
      [key]: newVal,
    }))

    await this.notificationService.updatePreferences({ [key]: newVal })

    this.saved.set(true)
    setTimeout(() => this.saved.set(false), 2000)
  }
}
