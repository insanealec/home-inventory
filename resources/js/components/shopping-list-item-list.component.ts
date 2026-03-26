import { Component, Input, Output, EventEmitter, inject } from '@angular/core'
import { CommonModule } from '@angular/common'
import { ShoppingListItem } from '../types/shopping-list'
import { ShoppingListService } from '../services/shopping-list.service'

@Component({
  selector: 'app-shopping-list-item-list',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-800">
          <tr>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-12"
            >
              Done
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
            >
              Item
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
            >
              Qty
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
            >
              Unit
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
            >
              Category
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
            >
              Est. Price
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
            >
              Notes
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
            >
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          @for (item of items; track item.id) {
            <tr
              class="hover:bg-gray-50 dark:hover:bg-gray-700"
              [class.opacity-50]="item.is_completed"
              [class.line-through]="item.is_completed"
            >
              <td class="px-6 py-4 whitespace-nowrap">
                <input
                  type="checkbox"
                  [checked]="item.is_completed"
                  (change)="toggleComplete(item)"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                {{ item.name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                {{ item.quantity }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                {{ item.unit || '—' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                {{ item.category?.name || '—' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                {{ item.estimated_price ? '$' + item.estimated_price.toFixed(2) : '—' }}
              </td>
              <td class="px-6 py-4 max-w-xs truncate text-sm text-gray-700 dark:text-gray-300">
                {{ item.notes || '—' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button
                  (click)="deleteItemConfirm(item.id)"
                  class="text-red-600 hover:text-red-900 dark:hover:text-red-400"
                >
                  Delete
                </button>
              </td>
            </tr>
          }
        </tbody>
      </table>
      @if (items.length === 0) {
        <div class="py-8 text-center text-gray-500 dark:text-gray-400">
          No items in this shopping list yet.
        </div>
      }
    </div>
  `,
})
export class ShoppingListItemListComponent {
  @Input() listId!: number
  @Input() items: ShoppingListItem[] = []
  @Output() deleteItem = new EventEmitter<number>()

  private shoppingListService = inject(ShoppingListService)

  async updateItem(item: ShoppingListItem): Promise<void> {
    await this.shoppingListService.updateItem(this.listId, item.id, item)
  }

  async deleteItemConfirm(itemId: number): Promise<void> {
    if (confirm('Are you sure you want to remove this item?')) {
      await this.shoppingListService.deleteItem(this.listId, itemId)
      this.deleteItem.emit(itemId)
    }
  }

  async toggleComplete(item: ShoppingListItem): Promise<void> {
    item.is_completed = !item.is_completed
    await this.updateItem(item)
  }
}
