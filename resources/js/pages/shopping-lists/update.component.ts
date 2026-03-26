import { Component, OnInit, inject } from '@angular/core'
import { CommonModule } from '@angular/common'
import { FormsModule } from '@angular/forms'
import { ActivatedRoute, Router, RouterLink } from '@angular/router'
import { CardComponent } from '../../components/common/card.component'
import { ContentComponent } from '../../components/common/content.component'
import { ShoppingListService } from '../../services/shopping-list.service'

@Component({
  selector: 'app-shopping-lists-update',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink, CardComponent, ContentComponent],
  template: `
    <app-content>
      <app-card>
        <span slot="title">Edit Shopping List</span>

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

          <div class="flex items-center">
            <input
              [(ngModel)]="form.is_completed"
              name="is_completed"
              type="checkbox"
              id="is_completed"
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600"
            />
            <label for="is_completed" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
              Mark as completed
            </label>
          </div>

          <div class="flex gap-4">
            <button
              type="submit"
              class="px-6 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              Update Shopping List
            </button>
            <a
              [routerLink]="'/shopping-lists/' + listId"
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
export class ShoppingListUpdateComponent implements OnInit {
  shoppingListService = inject(ShoppingListService)
  private route = inject(ActivatedRoute)
  private router = inject(Router)

  listId = 0

  form = {
    name: '',
    notes: '',
    shopping_date: '',
    is_completed: false,
  }

  ngOnInit(): void {
    this.route.params.subscribe(async params => {
      this.listId = parseInt(params['id'], 10)
      await this.shoppingListService.loadList(this.listId)

      const list = this.shoppingListService.list()
      if (list) {
        this.form = {
          name: list.name,
          notes: list.notes,
          shopping_date: list.shopping_date || '',
          is_completed: list.is_completed,
        }
      }
    })
  }

  async submitForm(): Promise<void> {
    if (!this.form.name.trim()) {
      alert('Please enter a shopping list name')
      return
    }

    const success = await this.shoppingListService.updateList(this.listId, {
      name: this.form.name,
      notes: this.form.notes,
      shopping_date: this.form.shopping_date || null,
      is_completed: this.form.is_completed,
    })

    if (success) {
      this.router.navigate(['/shopping-lists', this.listId])
    }
  }
}
