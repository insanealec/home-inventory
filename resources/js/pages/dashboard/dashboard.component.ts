import { Component, OnInit, inject, signal } from '@angular/core'
import { CommonModule } from '@angular/common'
import { RouterLink } from '@angular/router'
import { HttpClient } from '@angular/common/http'
import { firstValueFrom } from 'rxjs'
import { CardComponent } from '../../components/common/card.component'
import { ContentComponent } from '../../components/common/content.component'

interface DashboardSummary {
  total_items: number
  total_locations: number
  active_shopping_lists: number
  low_stock_items: any[]
  expiring_items: any[]
  recent_items: any[]
}

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule, RouterLink, CardComponent, ContentComponent],
  template: `
    <app-content>
      <!-- Summary Grid -->
      @if (summary() && !loading()) {
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
          <app-card>
            <div class="text-center">
              <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">
                {{ summary()!.total_items }}
              </div>
              <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Total Items
              </div>
            </div>
          </app-card>

          <app-card>
            <div class="text-center">
              <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                {{ summary()!.total_locations }}
              </div>
              <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Stock Locations
              </div>
            </div>
          </app-card>

          <app-card>
            <div class="text-center">
              <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                {{ summary()!.active_shopping_lists }}
              </div>
              <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Active Lists
              </div>
            </div>
          </app-card>

          <app-card>
            <div class="text-center">
              <div
                [class]="
                  summary()!.low_stock_items.length > 0
                    ? 'text-red-600 dark:text-red-400'
                    : 'text-green-600 dark:text-green-400'
                "
                class="text-3xl font-bold"
              >
                {{ summary()!.low_stock_items.length }}
              </div>
              <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Low Stock
              </div>
            </div>
          </app-card>
        </div>
      }

      <!-- Quick Actions -->
      <app-card>
        <span slot="title">Quick Actions</span>
        <div class="flex flex-wrap gap-3">
          <a
            routerLink="/inventory/create"
            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
          >
            Add Item
          </a>
          <a
            routerLink="/stock-locations/create"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
          >
            Add Location
          </a>
          <a
            routerLink="/shopping-lists/create"
            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
          >
            New Shopping List
          </a>
        </div>
      </app-card>

      <!-- Low Stock Items -->
      @if (summary() && summary()!.low_stock_items.length > 0) {
        <app-card>
          <span slot="title">Low Stock Items</span>
          <div class="space-y-2">
            @for (item of summary()!.low_stock_items; track item.id) {
              <div
                class="flex justify-between items-center p-2 bg-red-50 dark:bg-red-900/20 rounded"
              >
                <a
                  [routerLink]="'/inventory/' + item.id"
                  class="text-indigo-600 dark:text-indigo-400 hover:underline"
                >
                  {{ item.name }}
                </a>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                  {{ item.quantity }} / {{ item.reorder_point }}
                </span>
              </div>
            }
          </div>
        </app-card>
      }

      <!-- Expiring Soon Items -->
      @if (summary() && summary()!.expiring_items.length > 0) {
        <app-card>
          <span slot="title">Expiring Soon</span>
          <div class="space-y-2">
            @for (item of summary()!.expiring_items; track item.id) {
              <div
                class="flex justify-between items-center p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded"
              >
                <a
                  [routerLink]="'/inventory/' + item.id"
                  class="text-indigo-600 dark:text-indigo-400 hover:underline"
                >
                  {{ item.name }}
                </a>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                  Expires: {{ item.expiration_date }}
                </span>
              </div>
            }
          </div>
        </app-card>
      }

      <!-- Recently Updated Items -->
      @if (summary() && summary()!.recent_items.length > 0) {
        <app-card>
          <span slot="title">Recently Updated</span>
          <div class="space-y-2">
            @for (item of summary()!.recent_items; track item.id) {
              <div
                class="flex justify-between items-center p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded"
              >
                <a
                  [routerLink]="'/inventory/' + item.id"
                  class="text-indigo-600 dark:text-indigo-400 hover:underline"
                >
                  {{ item.name }}
                </a>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                  Qty: {{ item.quantity }}
                </span>
              </div>
            }
          </div>
        </app-card>
      }
    </app-content>
  `,
})
export class DashboardComponent implements OnInit {
  private http = inject(HttpClient)

  summary = signal<DashboardSummary | null>(null)
  loading = signal(true)

  async ngOnInit(): Promise<void> {
    try {
      const data = await firstValueFrom(
        this.http.get<DashboardSummary>('/api/dashboard')
      )
      this.summary.set(data)
    } catch (error) {
      console.error('Error loading dashboard:', error)
    } finally {
      this.loading.set(false)
    }
  }
}
