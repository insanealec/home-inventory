import { Component, OnInit, inject } from '@angular/core'
import { CommonModule, Location } from '@angular/common'
import { RouterLink, Router, ActivatedRoute } from '@angular/router'
import { CardComponent } from '../../components/common/card.component'
import { ContentComponent } from '../../components/common/content.component'
import { InventoryService } from '../../services/inventory.service'

@Component({
  selector: 'app-inventory-show',
  standalone: true,
  imports: [CommonModule, RouterLink, CardComponent, ContentComponent],
  template: `
    <app-content>
      @if (inventoryService.loading()) {
        <app-card>
          <span slot="title">Loading...</span>
          <p>Loading inventory item details...</p>
        </app-card>
      } @else if (inventoryService.item()) {
        <app-card>
          <span slot="title">{{ inventoryService.item()!.name }}</span>

          <div class="space-y-6">
            <!-- Basic Information -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  SKU
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ inventoryService.item()!.sku || 'N/A' }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Stock Location
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ inventoryService.item()!.stock_location?.name || 'N/A' }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Position
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ inventoryService.item()!.position || 'N/A' }}
                </p>
              </div>
            </div>

            <!-- Description -->
            @if (inventoryService.item()!.description) {
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Description
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ inventoryService.item()!.description }}
                </p>
              </div>
            }

            <!-- Quantity & Stock Levels -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Current Quantity
                </label>
                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">
                  {{ inventoryService.item()!.quantity }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Reorder Point
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ inventoryService.item()!.reorder_point }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Reorder Quantity
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ inventoryService.item()!.reorder_quantity }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Unit Price
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ '$' + (inventoryService.item()!.unit_price ?? 0).toFixed(2) }}
                </p>
              </div>
            </div>

            <!-- Stock Level Constraints -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Minimum Stock Level
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ inventoryService.item()!.min_stock_level }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Maximum Stock Level
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ inventoryService.item()!.max_stock_level }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Unit
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ inventoryService.item()!.unit || 'N/A' }}
                </p>
              </div>
            </div>

            <!-- Expiration Date & Timestamps -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
              @if (inventoryService.item()!.expiration_date) {
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Expiration Date
                  </label>
                  <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    {{ inventoryService.item()!.expiration_date }}
                  </p>
                </div>
              }

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Created At
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ inventoryService.item()!.created_at }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Updated At
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ inventoryService.item()!.updated_at }}
                </p>
              </div>
            </div>

            <!-- Action Buttons -->
            <div
              class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700"
            >
              <button
                type="button"
                (click)="location.back()"
                class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Back
              </button>
              <a
                [routerLink]="'/inventory/' + inventoryService.item()!.id + '/edit'"
                class="px-4 py-2 bg-blue-600 text-white rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Edit
              </a>
              <button
                type="button"
                (click)="deleteItem()"
                [disabled]="inventoryService.deleting()"
                class="px-4 py-2 bg-red-600 text-white rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ inventoryService.deleting() ? 'Deleting...' : 'Delete' }}
              </button>
            </div>
          </div>
        </app-card>
      } @else {
        <app-card>
          <span slot="title">Not Found</span>
          <p class="text-gray-900 dark:text-gray-100">
            Inventory item not found.
          </p>
          <button
            type="button"
            (click)="router.navigate(['/inventory'])"
            class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700"
          >
            Back to Inventory
          </button>
        </app-card>
      }
    </app-content>
  `,
})
export class InventoryShowComponent implements OnInit {
  inventoryService = inject(InventoryService)
  router = inject(Router)
  location = inject(Location)
  private route = inject(ActivatedRoute)

  ngOnInit(): void {
    const id = this.route.snapshot.paramMap.get('id')
    if (id) {
      this.inventoryService.loadItem(parseInt(id))
    }
  }

  async deleteItem(): Promise<void> {
    if (!this.inventoryService.item()) return
    if (await this.inventoryService.deleteItem(this.inventoryService.item()!.id as number)) {
      await this.router.navigate(['/inventory'])
    }
  }
}
