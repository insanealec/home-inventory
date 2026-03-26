import { Component, inject } from '@angular/core'
import { CommonModule } from '@angular/common'
import { RouterLink, RouterLinkActive } from '@angular/router'
import { AuthService } from '../../services/auth.service'
import { NotificationBellComponent } from './notification-bell.component'

@Component({
  selector: 'app-nav-main',
  standalone: true,
  imports: [CommonModule, RouterLink, RouterLinkActive, NotificationBellComponent],
  template: `
    <nav class="bg-white shadow dark:bg-gray-800">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
          <div class="flex">
            <div class="flex flex-shrink-0 items-center">
              <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                Inventory Manager
              </h1>
            </div>
            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
              <a
                routerLink="/dashboard"
                routerLinkActive="border-indigo-500 text-gray-900 dark:text-white"
                [routerLinkActiveOptions]="{ exact: true }"
                class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300"
              >
                Dashboard
              </a>
              <a
                routerLink="/inventory"
                routerLinkActive="border-indigo-500 text-gray-900 dark:text-white"
                [routerLinkActiveOptions]="{ exact: true }"
                class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300"
              >
                Inventory
              </a>
              <a
                routerLink="/stock-locations"
                routerLinkActive="border-indigo-500 text-gray-900 dark:text-white"
                [routerLinkActiveOptions]="{ exact: true }"
                class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300"
              >
                Stock Locations
              </a>
              <a
                routerLink="/shopping-lists"
                routerLinkActive="border-indigo-500 text-gray-900 dark:text-white"
                [routerLinkActiveOptions]="{ exact: true }"
                class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300"
              >
                Shopping Lists
              </a>
              <button
                type="button"
                (click)="logout()"
                class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300"
              >
                Logout
              </button>
            </div>
          </div>
          <!-- Right side: notification bell -->
          <div class="flex items-center gap-3">
            <app-notification-bell />
          </div>
        </div>
      </div>
    </nav>
  `,
})
export class NavMainComponent {
  private auth = inject(AuthService)

  logout(): void {
    this.auth.logout()
  }
}
