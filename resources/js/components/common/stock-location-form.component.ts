import { Component, Output, EventEmitter, inject } from '@angular/core'
import { CommonModule } from '@angular/common'
import { FormsModule } from '@angular/forms'
import { StockLocationService } from '../../services/stock-location.service'
import { FormInputComponent } from './form-input.component'

@Component({
  selector: 'app-stock-location-form',
  standalone: true,
  imports: [CommonModule, FormsModule, FormInputComponent],
  template: `
    @if (stockLocationService.stockLocation()) {
      <form (ngSubmit)="onSubmit()" class="space-y-6">
        <!-- Required Fields -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
          <app-form-input
            id="name"
            label="Location Name *"
            [ngModel]="stockLocationService.stockLocation()?.name"
            (ngModelChange)="setField('name', $event)"
            name="name"
            [error]="getError('name')"
          />

          <app-form-input
            id="short_name"
            label="Short Name *"
            [ngModel]="stockLocationService.stockLocation()?.short_name"
            (ngModelChange)="setField('short_name', $event)"
            name="short_name"
            [error]="getError('short_name')"
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
            [ngModel]="stockLocationService.stockLocation()?.description"
            (ngModelChange)="setField('description', $event)"
            name="description"
            rows="3"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
          ></textarea>
          @if (getError('description')) {
            <p class="mt-1 text-sm text-red-600">{{ getError('description') }}</p>
          }
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
          <button
            type="submit"
            [disabled]="stockLocationService.creating() || stockLocationService.updating()"
            class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ (stockLocationService.creating() || stockLocationService.updating()) ? 'Saving...' : 'Save' }}
          </button>
        </div>
      </form>
    }
  `,
})
export class StockLocationFormComponent {
  @Output() submitForm = new EventEmitter<void>()

  stockLocationService = inject(StockLocationService)

  setField(field: string, value: any): void {
    this.stockLocationService.stockLocation.update(loc => loc ? { ...loc, [field]: value } : loc)
  }

  onSubmit(): void {
    this.submitForm.emit()
  }

  getError(field: string): string {
    const errors = this.stockLocationService.errors()
    return errors[field] ? errors[field][0] : ''
  }
}
