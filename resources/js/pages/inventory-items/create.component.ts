import { Component, OnInit, inject } from '@angular/core'
import { CommonModule } from '@angular/common'
import { Router } from '@angular/router'
import { CardComponent } from '../../components/common/card.component'
import { ContentComponent } from '../../components/common/content.component'
import { ItemFormComponent } from '../../components/common/item-form.component'
import { InventoryService } from '../../services/inventory.service'

@Component({
  selector: 'app-inventory-create',
  standalone: true,
  imports: [CommonModule, CardComponent, ContentComponent, ItemFormComponent],
  template: `
    <app-content>
      <app-card>
        <span slot="title">Create New Inventory Item</span>
        <app-item-form (submitForm)="submitForm()" />
      </app-card>
    </app-content>
  `,
})
export class InventoryCreateComponent implements OnInit {
  private inventoryService = inject(InventoryService)
  private router = inject(Router)

  ngOnInit(): void {
    this.inventoryService.resetItem()
  }

  async submitForm(): Promise<void> {
    if (!(await this.inventoryService.createItem())) return
    await this.router.navigate(['/inventory'])
  }
}
