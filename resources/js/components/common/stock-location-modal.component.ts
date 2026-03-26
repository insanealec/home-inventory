import { Component, signal, inject } from '@angular/core'
import { CommonModule } from '@angular/common'
import { StockLocationService } from '../../services/stock-location.service'
import { ModalComponent } from './modal.component'
import { StockLocationFormComponent } from './stock-location-form.component'

@Component({
  selector: 'app-stock-location-modal',
  standalone: true,
  imports: [CommonModule, ModalComponent, StockLocationFormComponent],
  template: `
    <div>
      <button
        type="button"
        (click)="openModal()"
        class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
        Create New Location
      </button>

      <app-modal [isOpen]="isOpen()" title="Create New Stock Location" (close)="closeModal()">
        <app-stock-location-form (submitForm)="submitForm()" />
      </app-modal>
    </div>
  `,
})
export class StockLocationModalComponent {
  stockLocationService = inject(StockLocationService)
  isOpen = signal(false)

  openModal(): void {
    this.stockLocationService.resetStockLocation()
    this.isOpen.set(true)
  }

  closeModal(): void {
    this.isOpen.set(false)
  }

  async submitForm(): Promise<void> {
    if (await this.stockLocationService.createStockLocation()) {
      this.closeModal()
      await this.stockLocationService.loadStockLocations()
    }
  }
}
