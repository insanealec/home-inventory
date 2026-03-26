import { Component, OnInit, inject } from '@angular/core'
import { CommonModule } from '@angular/common'
import { FormsModule } from '@angular/forms'
import { Router, RouterLink } from '@angular/router'
import { CardComponent } from '../../components/common/card.component'
import { ContentComponent } from '../../components/common/content.component'
import { ShoppingListService } from '../../services/shopping-list.service'

@Component({
  selector: 'app-shopping-lists-create',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink, CardComponent, ContentComponent],
  template: `
    <app-content>
      <app-card>
        <span slot="title">Create New Shopping List</span>

        <form (ngSubmit)="submitForm()" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Shopping List Name *
            </label>
            <input
              [(ngModel)]="form.name"
              name="name"
              type="text"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              placeholder="e.g., Weekly Groceries"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Notes
            </label>
            <textarea
              [(ngModel)]="form.notes"
              name="notes"
              class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              rows="3"
              placeholder="Optional notes about this shopping list"
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Shopping Date
            </label>
            <input
              [(ngModel)]="form.shopping_date"
              name="shopping_date"
              type="date"
              class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            />
          </div>

          <div class="flex gap-4">
            <button
              type="submit"
              class="px-6 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              Create Shopping List
            </button>
            <a
              routerLink="/shopping-lists"
              class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md shadow-sm hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600"
            >
              Cancel
            </a>
          </div>
        </form>
      </app-card>
    </app-content>
  `,
})
export class ShoppingListCreateComponent implements OnInit {
  shoppingListService = inject(ShoppingListService)
  private router = inject(Router)

  form = {
    name: '',
    notes: '',
    shopping_date: '',
  }

  ngOnInit(): void {
    this.shoppingListService.resetList()
  }

  async submitForm(): Promise<void> {
    if (!this.form.name.trim()) {
      alert('Please enter a shopping list name')
      return
    }

    const success = await this.shoppingListService.createList({
      name: this.form.name,
      notes: this.form.notes,
      shopping_date: this.form.shopping_date || null,
    })

    if (success) {
      this.router.navigate(['/shopping-lists'])
    }
  }
}
