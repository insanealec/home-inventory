import { Component, OnInit, inject } from '@angular/core'
import { CommonModule } from '@angular/common'
import { Router } from '@angular/router'
import { CardComponent } from '../../components/common/card.component'
import { ContentComponent } from '../../components/common/content.component'
import { StockLocationFormComponent } from '../../components/common/stock-location-form.component'
import { StockLocationService } from '../../services/stock-location.service'

@Component({
  selector: 'app-stock-locations-create',
  standalone: true,
  imports: [CommonModule, CardComponent, ContentComponent, StockLocationFormComponent],
  template: `
    <app-content>
      <app-card>
        <span slot="title">Create New Stock Location</span>
        <app-stock-location-form (submitForm)="submitForm()" />
      </app-card>
    </app-content>
  `,
})
export class StockLocationCreateComponent implements OnInit {
  private stockLocationService = inject(StockLocationService)
  private router = inject(Router)

  ngOnInit(): void {
    this.stockLocationService.resetStockLocation()
  }

  async submitForm(): Promise<void> {
    if (!(await this.stockLocationService.createStockLocation())) return
    await this.router.navigate(['/stock-locations'])
  }
}
