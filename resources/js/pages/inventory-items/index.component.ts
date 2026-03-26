import { Component, OnInit, inject } from '@angular/core'
import { CommonModule } from '@angular/common'
import { RouterLink, ActivatedRoute } from '@angular/router'
import { CardComponent } from '../../components/common/card.component'
import { ContentComponent } from '../../components/common/content.component'
import { PaginatorComponent } from '../../components/common/paginator.component'
import { InventoryService } from '../../services/inventory.service'

@Component({
  selector: 'app-inventory-index',
  standalone: true,
  imports: [
    CommonModule,
    RouterLink,
    CardComponent,
    ContentComponent,
    PaginatorComponent,
  ],
  template: `
    <app-content>
      <app-card>
        <span slot="title">Inventory Items</span>
        <div class="mb-4">
          <a
            routerLink="/inventory/create"
            class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700"
          >
            Add Inventory Item
          </a>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
              <tr>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                  (click)="handleSort('name')"
                >
                  <div class="flex items-center">
                    Name
                    @if (sortField === 'name') {
                      <span class="ml-1">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    }
                  </div>
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                  (click)="handleSort('sku')"
                >
                  <div class="flex items-center">
                    SKU
                    @if (sortField === 'sku') {
                      <span class="ml-1">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    }
                  </div>
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                  (click)="handleSort('quantity')"
                >
                  <div class="flex items-center">
                    Quantity
                    @if (sortField === 'quantity') {
                      <span class="ml-1">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    }
                  </div>
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                  (click)="handleSort('stock_location_id')"
                >
                  <div class="flex items-center">
                    Stock Location
                    @if (sortField === 'stock_location_id') {
                      <span class="ml-1">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    }
                  </div>
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                  (click)="handleSort('created_at')"
                >
                  <div class="flex items-center">
                    Created At
                    @if (sortField === 'created_at') {
                      <span class="ml-1">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    }
                  </div>
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Actions
                </th>
              </tr>
            </thead>
            <tbody
              class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700"
            >
              @for (item of inventoryService.items(); track item.id) {
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                  <td
                    class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100"
                  >
                    {{ item.name }}
                  </td>
                  <td
                    class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100"
                  >
                    {{ item.sku }}
                  </td>
                  <td
                    class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100"
                  >
                    {{ item.quantity }}
                  </td>
                  <td
                    class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100"
                  >
                    {{ item.stock_location?.name || 'N/A' }}
                  </td>
                  <td
                    class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100"
                  >
                    {{ item.created_at }}
                  </td>
                  <td
                    class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2"
                  >
                    <a
                      [routerLink]="'/inventory/' + item.id"
                      class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300"
                    >
                      View
                    </a>
                    <button
                      type="button"
                      (click)="deleteItem(item.id)"
                      class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              }
            </tbody>
          </table>
        </div>
        <!-- Pagination Controls -->
        <app-paginator
          [paginator]="inventoryService.paginator()"
          [currentPath]="'/inventory'"
        />
      </app-card>
    </app-content>
  `,
})
export class InventoryIndexComponent implements OnInit {
  inventoryService = inject(InventoryService)
  private route = inject(ActivatedRoute)

  sortField: string | null = null
  sortDirection: 'asc' | 'desc' = 'asc'

  ngOnInit(): void {
    this.loadThisPage()
  }

  handleSort(field: string): void {
    if (this.sortField === field) {
      this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc'
    } else {
      this.sortField = field
      this.sortDirection = 'asc'
    }
    this.inventoryService.loadItems(1, this.sortField, this.sortDirection)
  }

  async deleteItem(id: number): Promise<void> {
    await this.inventoryService.deleteItem(id)
    this.loadThisPage()
  }

  private loadThisPage(): void {
    const page = this.route.snapshot.queryParamMap.get('page')
      ? parseInt(this.route.snapshot.queryParamMap.get('page') as string)
      : 1
    this.inventoryService.loadItems(page, this.sortField, this.sortDirection)
  }
}
