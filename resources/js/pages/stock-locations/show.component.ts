import { Component, OnInit, inject } from '@angular/core'
import { CommonModule, Location } from '@angular/common'
import { RouterLink, Router, ActivatedRoute } from '@angular/router'
import { CardComponent } from '../../components/common/card.component'
import { ContentComponent } from '../../components/common/content.component'
import { StockLocationService } from '../../services/stock-location.service'

@Component({
  selector: 'app-stock-locations-show',
  standalone: true,
  imports: [CommonModule, RouterLink, CardComponent, ContentComponent],
  template: `
    <app-content>
      @if (stockLocationService.loading()) {
        <app-card>
          <span slot="title">Loading...</span>
          <p>Loading stock location details...</p>
        </app-card>
      } @else if (stockLocationService.stockLocation()) {
        <app-card>
          <span slot="title">{{ stockLocationService.stockLocation()!.name }}</span>

          <div class="space-y-6">
            <!-- Basic Information -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Name
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ stockLocationService.stockLocation()!.name }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Short Name
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ stockLocationService.stockLocation()!.short_name }}
                </p>
              </div>
            </div>

            <!-- Description -->
            @if (stockLocationService.stockLocation()!.description) {
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Description
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ stockLocationService.stockLocation()!.description }}
                </p>
              </div>
            }

            <!-- Timestamps -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Created At
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ stockLocationService.stockLocation()!.created_at }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Updated At
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ stockLocationService.stockLocation()!.updated_at }}
                </p>
              </div>
            </div>

            <!-- Inventory Items Count -->
            @if (stockLocationService.stockLocation()!.inventory_items) {
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Items in Location
                </label>
                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">
                  {{ stockLocationService.stockLocation()!.inventory_items.length }}
                </p>
              </div>
            }

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
                [routerLink]="'/stock-locations/' + stockLocationService.stockLocation()!.id + '/edit'"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Edit
              </a>
              <button
                type="button"
                (click)="deleteStockLocation()"
                [disabled]="stockLocationService.deleting()"
                class="px-4 py-2 bg-red-600 text-white rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ stockLocationService.deleting() ? 'Deleting...' : 'Delete' }}
              </button>
            </div>
          </div>
        </app-card>
      } @else {
        <app-card>
          <span slot="title">Not Found</span>
          <p class="text-gray-900 dark:text-gray-100">
            Stock location not found.
          </p>
          <button
            type="button"
            (click)="router.navigate(['/stock-locations'])"
            class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700"
          >
            Back to Stock Locations
          </button>
        </app-card>
      }
    </app-content>
  `,
})
export class StockLocationShowComponent implements OnInit {
  stockLocationService = inject(StockLocationService)
  router = inject(Router)
  location = inject(Location)
  private route = inject(ActivatedRoute)

  ngOnInit(): void {
    const id = this.route.snapshot.paramMap.get('id')
    if (id) {
      this.stockLocationService.loadStockLocation(parseInt(id))
    }
  }

  async deleteStockLocation(): Promise<void> {
    if (!this.stockLocationService.stockLocation()) return
    if (await this.stockLocationService.deleteStockLocation(this.stockLocationService.stockLocation()!.id as number)) {
      await this.router.navigate(['/stock-locations'])
    }
  }
}
