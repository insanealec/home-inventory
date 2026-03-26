import { Component, OnInit, inject } from '@angular/core'
import { CommonModule } from '@angular/common'
import { Router, ActivatedRoute } from '@angular/router'
import { CardComponent } from '../../components/common/card.component'
import { ContentComponent } from '../../components/common/content.component'
import { StockLocationFormComponent } from '../../components/common/stock-location-form.component'
import { StockLocationService } from '../../services/stock-location.service'

@Component({
  selector: 'app-stock-locations-update',
  standalone: true,
  imports: [CommonModule, CardComponent, ContentComponent, StockLocationFormComponent],
  template: `
    <app-content>
      <app-card>
        <span slot="title">Edit Stock Location</span>
        <app-stock-location-form (submitForm)="submitForm()" />
      </app-card>
    </app-content>
  `,
})
export class StockLocationUpdateComponent implements OnInit {
  private stockLocationService = inject(StockLocationService)
  private router = inject(Router)
  private route = inject(ActivatedRoute)

  ngOnInit(): void {
    const id = this.route.snapshot.paramMap.get('id')
    if (id) {
      this.stockLocationService.loadStockLocation(parseInt(id))
    }
  }

  async submitForm(): Promise<void> {
    const success = await this.stockLocationService.updateStockLocation()
    if (!success) return
    await this.router.navigate(['/stock-locations', this.stockLocationService.stockLocation()!.id])
  }
}
