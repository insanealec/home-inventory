import { Component, OnInit, inject } from '@angular/core'
import { CommonModule } from '@angular/common'
import { RouterLink } from '@angular/router'
import { HttpClient } from '@angular/common/http'
import { firstValueFrom } from 'rxjs'
import { CardComponent } from '../../components/common/card.component'
import { ContentComponent } from '../../components/common/content.component'
import { TokenService } from '../../services/token.service'

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
      @if (summary && !loading) {
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
          <app-card>
            <div class="text-center">
              <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">
                {{ summary.total_items }}
              </div>
              <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Total Items
              </div>
            </div>
          </app-card>

          <app-card>
            <div class="text-center">
              <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                {{ summary.total_locations }}
              </div>
              <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Stock Locations
              </div>
            </div>
          </app-card>

          <app-card>
            <div class="text-center">
              <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                {{ summary.active_shopping_lists }}
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
                  summary.low_stock_items.length > 0
                    ? 'text-red-600 dark:text-red-400'
                    : 'text-green-600 dark:text-green-400'
                "
                class="text-3xl font-bold"
              >
                {{ summary.low_stock_items.length }}
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
      @if (summary && summary.low_stock_items.length > 0) {
        <app-card>
          <span slot="title">Low Stock Items</span>
          <div class="space-y-2">
            @for (item of summary.low_stock_items; track item.id) {
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
      @if (summary && summary.expiring_items.length > 0) {
        <app-card>
          <span slot="title">Expiring Soon</span>
          <div class="space-y-2">
            @for (item of summary.expiring_items; track item.id) {
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
      @if (summary && summary.recent_items.length > 0) {
        <app-card>
          <span slot="title">Recently Updated</span>
          <div class="space-y-2">
            @for (item of summary.recent_items; track item.id) {
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

      <!-- Token Management Card -->
      <app-card>
        <span slot="title">Token Management</span>
        <div class="space-y-8">
          <!-- Create Token Form -->
          <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
              Create New Token
            </h2>
            <form
              (ngSubmit)="tokenService.storeToken()"
              class="flex gap-4"
            >
              <input
                [ngModel]="tokenService.newTokenName()"
                (ngModelChange)="tokenService.newTokenName.set($event)"
                [ngModelOptions]="{ standalone: true }"
                type="text"
                placeholder="Token name"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
              <button
                type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
              >
                Create Token
              </button>
            </form>
          </div>

          <!-- Tokens Table -->
          <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
              Your Tokens
            </h2>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                  <tr>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Name
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Created At
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Last Used
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                  @for (token of tokenService.tokens(); track token.id) {
                    <tr>
                      <td
                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white"
                      >
                        {{ token.name }}
                      </td>
                      <td
                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                      >
                        {{ token.created_at }}
                      </td>
                      <td
                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                      >
                        {{ token.last_used_at || 'Never' }}
                      </td>
                      <td
                        class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                      >
                        <button
                          type="button"
                          (click)="tokenService.destroyToken(token.id)"
                          class="text-red-600 hover:text-red-900 focus:outline-none focus:underline dark:text-red-400 dark:hover:text-red-300"
                        >
                          Delete
                        </button>
                      </td>
                    </tr>
                  } @empty {
                    <tr>
                      <td
                        colspan="4"
                        class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400"
                      >
                        No tokens found.
                      </td>
                    </tr>
                  }
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </app-card>
    </app-content>
  `,
})
export class DashboardComponent implements OnInit {
  private http = inject(HttpClient)
  tokenService = inject(TokenService)

  summary: DashboardSummary | null = null
  loading = true

  async ngOnInit(): Promise<void> {
    try {
      this.summary = await firstValueFrom(
        this.http.get<DashboardSummary>('/api/dashboard')
      )
    } catch (error) {
      console.error('Error loading dashboard:', error)
    } finally {
      this.loading = false
    }

    await this.tokenService.loadTokens()
  }
}
