import { Component, OnInit, inject } from '@angular/core'
import { CommonModule } from '@angular/common'
import { RouterLink, ActivatedRoute } from '@angular/router'
import { CardComponent } from '../../components/common/card.component'
import { ContentComponent } from '../../components/common/content.component'
import { PaginatorComponent } from '../../components/common/paginator.component'
import { ShoppingListService } from '../../services/shopping-list.service'

@Component({
  selector: 'app-shopping-lists-index',
  standalone: true,
  imports: [CommonModule, RouterLink, CardComponent, ContentComponent, PaginatorComponent],
  template: `
    <app-content>
      <app-card>
        <span slot="title">Shopping Lists</span>
        <div class="mb-4">
          <a
            routerLink="/shopping-lists/create"
            class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700"
          >
            Create Shopping List
          </a>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
              <tr>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Name
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Items
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Status
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Shopping Date
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
              @for (list of shoppingListService.lists(); track list.id) {
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                    {{ list.name }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                    {{ list.items_count ?? 0 }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">
                    @if (list.is_completed) {
                      <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                        Completed
                      </span>
                    } @else {
                      <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">
                        Active
                      </span>
                    }
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                    {{ list.shopping_date ?? 'Not set' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium gap-2 flex">
                    <a
                      [routerLink]="'/shopping-lists/' + list.id"
                      class="text-indigo-600 hover:text-indigo-900 dark:hover:text-indigo-400"
                    >
                      View
                    </a>
                    <a
                      [routerLink]="'/shopping-lists/' + list.id + '/edit'"
                      class="text-blue-600 hover:text-blue-900 dark:hover:text-blue-400"
                    >
                      Edit
                    </a>
                    <button
                      (click)="deleteAndRefresh(list.id)"
                      class="text-red-600 hover:text-red-900 dark:hover:text-red-400"
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              }
            </tbody>
          </table>
        </div>
        <app-paginator
          [paginator]="shoppingListService.paginator()"
          [currentPath]="'/shopping-lists'"
        />
      </app-card>
    </app-content>
  `,
})
export class ShoppingListIndexComponent implements OnInit {
  shoppingListService = inject(ShoppingListService)
  private route = inject(ActivatedRoute)

  ngOnInit(): void {
    this.route.queryParams.subscribe(params => {
      const page = params['page'] ? parseInt(params['page'], 10) : 1
      this.shoppingListService.loadLists(page)
    })
  }

  async deleteAndRefresh(listId: number): Promise<void> {
    if (confirm('Are you sure you want to delete this shopping list?')) {
      await this.shoppingListService.deleteList(listId)
      const currentPage = this.shoppingListService.paginator().current_page
      this.shoppingListService.loadLists(currentPage)
    }
  }
}
