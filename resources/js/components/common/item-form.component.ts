import { Component, Output, EventEmitter, OnInit, inject } from '@angular/core'
import { CommonModule } from '@angular/common'
import { FormsModule } from '@angular/forms'
import { InventoryService } from '../../services/inventory.service'
import { StockLocationService } from '../../services/stock-location.service'
import { FormInputComponent } from './form-input.component'
import { StockLocationModalComponent } from './stock-location-modal.component'

@Component({
  selector: 'app-item-form',
  standalone: true,
  imports: [CommonModule, FormsModule, FormInputComponent, StockLocationModalComponent],
  template: `
    @if (inventoryService.item()) {
      <form (ngSubmit)="onSubmit()" class="space-y-6">
        <!-- Required Fields -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
          <app-form-input
            id="name"
            label="Item Name *"
            [ngModel]="inventoryService.item()?.name"
            (ngModelChange)="setField('name', $event)"
            name="name"
            [error]="getError('name')"
          />

          <app-form-input
            id="sku"
            label="SKU"
            [ngModel]="inventoryService.item()?.sku"
            (ngModelChange)="setField('sku', $event)"
            name="sku"
            [error]="getError('sku')"
          />

          <div>
            <label
              for="stock_location_id"
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
            >
              Stock Location
            </label>
            <div class="flex items-center space-x-2">
              <select
                id="stock_location_id"
                [ngModel]="inventoryService.item()?.stock_location_id"
                (ngModelChange)="setField('stock_location_id', $event)"
                name="stock_location_id"
                class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
              >
                <option value="">Select a location</option>
                @for (location of stockLocationService.stockLocations(); track location.id) {
                  <option [value]="location.id">{{ location.name }}</option>
                }
              </select>
              <app-stock-location-modal />
            </div>
            @if (getError('stock_location_id')) {
              <p class="mt-1 text-sm text-red-600">{{ getError('stock_location_id') }}</p>
            }
          </div>

          <app-form-input
            id="position"
            label="Position"
            [ngModel]="inventoryService.item()?.position"
            (ngModelChange)="setField('position', $event)"
            name="position"
            [error]="getError('position')"
          />
        </div>

        <!-- Description -->
        <div>
          <label
            for="description"
            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
          >
            Description
          </label>
          <textarea
            id="description"
            [ngModel]="inventoryService.item()?.description"
            (ngModelChange)="setField('description', $event)"
            name="description"
            rows="3"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
          ></textarea>
          @if (getError('description')) {
            <p class="mt-1 text-sm text-red-600">{{ getError('description') }}</p>
          }
        </div>

        <!-- Quantity & Stock Levels -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
          <app-form-input
            id="quantity"
            label="Quantity"
            type="number"
            [min]="0"
            [ngModel]="inventoryService.item()?.quantity"
            (ngModelChange)="setField('quantity', $event)"
            name="quantity"
            [error]="getError('quantity')"
          />

          <app-form-input
            id="reorder_point"
            label="Reorder Point"
            type="number"
            [min]="0"
            [ngModel]="inventoryService.item()?.reorder_point"
            (ngModelChange)="setField('reorder_point', $event)"
            name="reorder_point"
            [error]="getError('reorder_point')"
          />

          <app-form-input
            id="reorder_quantity"
            label="Reorder Quantity"
            type="number"
            [min]="0"
            [ngModel]="inventoryService.item()?.reorder_quantity"
            (ngModelChange)="setField('reorder_quantity', $event)"
            name="reorder_quantity"
            [error]="getError('reorder_quantity')"
          />

          <app-form-input
            id="unit_price"
            label="Unit Price"
            type="number"
            [min]="0"
            [step]="0.01"
            [ngModel]="inventoryService.item()?.unit_price"
            (ngModelChange)="setField('unit_price', $event)"
            name="unit_price"
            [error]="getError('unit_price')"
          />
        </div>

        <!-- Stock Level Constraints -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
          <app-form-input
            id="min_stock_level"
            label="Minimum Stock Level"
            type="number"
            [min]="0"
            [ngModel]="inventoryService.item()?.min_stock_level"
            (ngModelChange)="setField('min_stock_level', $event)"
            name="min_stock_level"
            [error]="getError('min_stock_level')"
          />

          <app-form-input
            id="max_stock_level"
            label="Maximum Stock Level"
            type="number"
            [min]="0"
            [ngModel]="inventoryService.item()?.max_stock_level"
            (ngModelChange)="setField('max_stock_level', $event)"
            name="max_stock_level"
            [error]="getError('max_stock_level')"
          />
        </div>

        <!-- Unit & Expiration Date -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
          <app-form-input
            id="unit"
            label="Unit"
            [ngModel]="inventoryService.item()?.unit"
            (ngModelChange)="setField('unit', $event)"
            name="unit"
            [error]="getError('unit')"
          />

          <app-form-input
            id="expiration_date"
            label="Expiration Date"
            type="date"
            [ngModel]="inventoryService.item()?.expiration_date"
            (ngModelChange)="setField('expiration_date', $event)"
            name="expiration_date"
            [error]="getError('expiration_date')"
          />
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
          <button
            type="submit"
            [disabled]="inventoryService.creating() || inventoryService.updating()"
            class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ (inventoryService.creating() || inventoryService.updating()) ? 'Saving...' : 'Save' }}
          </button>
        </div>
      </form>
    }
  `,
})
export class ItemFormComponent implements OnInit {
  @Output() submitForm = new EventEmitter<void>()

  inventoryService = inject(InventoryService)
  stockLocationService = inject(StockLocationService)

  ngOnInit(): void {
    this.stockLocationService.loadStockLocations()
  }

  setField(field: string, value: any): void {
    this.inventoryService.item.update(item => item ? { ...item, [field]: value } : item)
  }

  onSubmit(): void {
    this.submitForm.emit()
  }

  getError(field: string): string {
    const errors = this.inventoryService.errors()
    return errors[field] ? errors[field][0] : ''
  }
}
