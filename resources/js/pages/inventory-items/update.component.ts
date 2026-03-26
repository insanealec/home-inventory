import { Component, OnInit, inject } from '@angular/core'
import { CommonModule } from '@angular/common'
import { Router, ActivatedRoute } from '@angular/router'
import { CardComponent } from '../../components/common/card.component'
import { ContentComponent } from '../../components/common/content.component'
import { ItemFormComponent } from '../../components/common/item-form.component'
import { InventoryService } from '../../services/inventory.service'

@Component({
  selector: 'app-inventory-update',
  standalone: true,
  imports: [CommonModule, CardComponent, ContentComponent, ItemFormComponent],
  template: `
    <app-content>
      <app-card>
        <span slot="title">Edit Inventory Item</span>
        <app-item-form (submitForm)="submitForm()" />
      </app-card>
    </app-content>
  `,
})
export class InventoryUpdateComponent implements OnInit {
  private inventoryService = inject(InventoryService)
  private router = inject(Router)
  private route = inject(ActivatedRoute)

  ngOnInit(): void {
    const id = this.route.snapshot.paramMap.get('id')
    if (id) {
      this.inventoryService.loadItem(parseInt(id))
    }
  }

  async submitForm(): Promise<void> {
    const success = await this.inventoryService.updateItem()
    if (!success) return
    await this.router.navigate(['/inventory', this.inventoryService.item()!.id])
  }
}
