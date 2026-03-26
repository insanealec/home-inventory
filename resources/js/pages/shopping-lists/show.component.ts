import { Component, OnInit, inject, signal } from '@angular/core'
import { CommonModule } from '@angular/common'
import { FormsModule } from '@angular/forms'
import { ActivatedRoute, RouterLink } from '@angular/router'
import { CardComponent } from '../../components/common/card.component'
import { ContentComponent } from '../../components/common/content.component'
import { ShoppingListItemListComponent } from '../../components/shopping-list-item-list.component'
import { ShoppingListService } from '../../services/shopping-list.service'
import { ShoppingCategoryService } from '../../services/shopping-category.service'
import { InventoryService } from '../../services/inventory.service'

@Component({
  selector: 'app-shopping-lists-show',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    RouterLink,
    CardComponent,
    ContentComponent,
    ShoppingListItemListComponent,
  ],
  template: `
    <app-content>
      @if (shoppingListService.list()) {
        <div class="space-y-4">
          <!-- List Header Card -->
          <app-card>
            <span slot="title">{{ shoppingListService.list()?.name }}</span>
            <div class="space-y-2">
              @if (shoppingListService.list()?.notes) {
                <div class="text-gray-700 dark:text-gray-300">
                  <strong>Notes:</strong> {{ shoppingListService.list()?.notes }}
                </div>
              }
              <div class="text-gray-700 dark:text-gray-300">
                <strong>Status:</strong>
                @if (shoppingListService.list()?.is_completed) {
                  <span class="ml-2 px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                    Completed
                  </span>
                } @else {
                  <span class="ml-2 px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">
                    Active
                  </span>
                }
              </div>
              @if (shoppingListService.list()?.shopping_date) {
                <div class="text-gray-700 dark:text-gray-300">
                  <strong>Shopping Date:</strong> {{ shoppingListService.list()?.shopping_date }}
                </div>
              }
              <div class="flex gap-2 mt-4">
                <a
                  [routerLink]="'/shopping-lists/' + listId + '/edit'"
                  class="px-4 py-2 bg-blue-600 text-white rounded-md shadow-sm hover:bg-blue-700"
                >
                  Edit List
                </a>
                <a
                  routerLink="/shopping-lists"
                  class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md shadow-sm hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600"
                >
                  Back to Lists
                </a>
              </div>
            </div>
          </app-card>

          <!-- Items Card -->
          <app-card>
            <span slot="title">Items ({{ shoppingListService.items().length }})</span>
            <app-shopping-list-item-list
              [listId]="listId"
              [items]="shoppingListService.items()"
              (deleteItem)="handleDeleteItem()"
            />
          </app-card>

          <!-- Add Item Card -->
          <app-card>
            <button
              (click)="showAddItemForm.set(!showAddItemForm())"
              class="text-indigo-600 hover:text-indigo-900 dark:hover:text-indigo-400 font-semibold"
            >
              {{ showAddItemForm() ? 'Cancel' : '+ Add Item' }}
            </button>

            @if (showAddItemForm()) {
              <div class="space-y-4">
                <!-- Mode toggle -->
                <div class="flex rounded-md border border-gray-300 dark:border-gray-600 overflow-hidden w-fit">
                  <button
                    type="button"
                    (click)="addMode.set('freeform')"
                    [class]="
                      addMode() === 'freeform'
                        ? 'bg-indigo-600 text-white'
                        : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'
                    "
                    class="px-4 py-2 text-sm font-medium transition-colors"
                  >
                    Free-form item
                  </button>
                  <button
                    type="button"
                    (click)="addMode.set('inventory')"
                    [class]="
                      addMode() === 'inventory'
                        ? 'bg-indigo-600 text-white'
                        : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'
                    "
                    class="px-4 py-2 text-sm font-medium border-l border-gray-300 dark:border-gray-600 transition-colors"
                  >
                    From inventory
                  </button>
                </div>

                <!-- Free-form form -->
                @if (addMode() === 'freeform') {
                  <form (ngSubmit)="addFreeformItem()" class="space-y-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Item Name *
                      </label>
                      <input
                        [ngModel]="newItem().name"
                        (ngModelChange)="setNewItemField('name', $event)"
                        name="name"
                        type="text"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                      />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                      <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                          Quantity *
                        </label>
                        <input
                          [ngModel]="newItem().quantity"
                          (ngModelChange)="setNewItemField('quantity', $event)"
                          name="quantity"
                          type="number"
                          min="1"
                          required
                          class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                          Unit
                        </label>
                        <input
                          [ngModel]="newItem().unit"
                          (ngModelChange)="setNewItemField('unit', $event)"
                          name="unit"
                          type="text"
                          placeholder="e.g., kg, liters"
                          class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />
                      </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                      <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                          Category
                        </label>
                        <select
                          [ngModel]="newItem().category_id"
                          (ngModelChange)="setNewItemField('category_id', $event)"
                          name="category_id"
                          class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                          <option [value]="null">None</option>
                          @for (cat of shoppingCategoryService.categories(); track cat.id) {
                            <option [value]="cat.id">{{ cat.name }}</option>
                          }
                        </select>
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                          Est. Price
                        </label>
                        <input
                          [ngModel]="newItem().estimated_price"
                          (ngModelChange)="setNewItemField('estimated_price', $event)"
                          name="estimated_price"
                          type="number"
                          min="0"
                          step="0.01"
                          class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />
                      </div>
                    </div>

                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Notes
                      </label>
                      <textarea
                        [ngModel]="newItem().notes"
                        (ngModelChange)="setNewItemField('notes', $event)"
                        name="notes"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        rows="2"
                      ></textarea>
                    </div>

                    <button
                      type="submit"
                      class="px-6 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700"
                    >
                      Add Item
                    </button>
                  </form>
                }

                <!-- From inventory form -->
                @if (addMode() === 'inventory') {
                  <form (ngSubmit)="addFromInventory()" class="space-y-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Inventory Item *
                      </label>
                      <select
                        [ngModel]="inventoryAdd().inventory_item_id"
                        (ngModelChange)="setInventoryAddField('inventory_item_id', $event)"
                        name="inventory_item_id"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                      >
                        <option [value]="null">— Select an item —</option>
                        @for (item of inventoryService.items(); track item.id) {
                          <option [value]="item.id">
                            {{ item.name }}{{ item.unit ? ' (' + item.unit + ')' : '' }}{{ item.stock_location_id ? ' · ' + item.quantity + ' in stock' : '' }}
                          </option>
                        }
                      </select>
                    </div>

                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Quantity *
                      </label>
                      <input
                        [ngModel]="inventoryAdd().quantity"
                        (ngModelChange)="setInventoryAddField('quantity', $event)"
                        name="inventory_quantity"
                        type="number"
                        min="1"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                      />
                    </div>

                    <button
                      type="submit"
                      class="px-6 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700"
                    >
                      Add to List
                    </button>
                  </form>
                }
              </div>
            }
          </app-card>
        </div>
      } @else {
        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
          Loading shopping list...
        </div>
      }
    </app-content>
  `,
})
export class ShoppingListShowComponent implements OnInit {
  shoppingListService = inject(ShoppingListService)
  shoppingCategoryService = inject(ShoppingCategoryService)
  inventoryService = inject(InventoryService)
  private route = inject(ActivatedRoute)

  listId = 0
  showAddItemForm = signal(false)
  addMode = signal<'freeform' | 'inventory'>('freeform')

  newItem = signal({
    name: '',
    quantity: 1,
    unit: '',
    category_id: null as number | null,
    estimated_price: null as number | null,
    notes: '',
  })

  inventoryAdd = signal({
    inventory_item_id: null as number | null,
    quantity: 1,
  })

  ngOnInit(): void {
    this.route.params.subscribe(async params => {
      this.listId = parseInt(params['id'], 10)
      await this.shoppingListService.loadList(this.listId)
      await this.shoppingCategoryService.loadCategories()
      await this.inventoryService.loadItems()
    })
  }

  setNewItemField(field: string, value: any): void {
    this.newItem.update(item => ({ ...item, [field]: value }))
  }

  setInventoryAddField(field: string, value: any): void {
    this.inventoryAdd.update(item => ({ ...item, [field]: value }))
  }

  async addFreeformItem(): Promise<void> {
    const item = this.newItem()
    if (!item.name.trim()) return

    const success = await this.shoppingListService.createItem(this.listId, {
      name: item.name,
      quantity: item.quantity,
      unit: item.unit || null,
      category_id: item.category_id,
      estimated_price: item.estimated_price,
      notes: item.notes,
    })

    if (success) {
      this.resetForms()
    }
  }

  async addFromInventory(): Promise<void> {
    const inv = this.inventoryAdd()
    if (!inv.inventory_item_id) return

    const success = await this.shoppingListService.addInventoryItem(
      this.listId,
      inv.inventory_item_id,
      inv.quantity,
    )

    if (success) {
      this.resetForms()
    }
  }

  handleDeleteItem(): void {
    const list = this.shoppingListService.list()
    if (list) {
      this.shoppingListService.loadItems(list.id)
    }
  }

  private resetForms(): void {
    this.newItem.set({
      name: '',
      quantity: 1,
      unit: '',
      category_id: null,
      estimated_price: null,
      notes: '',
    })
    this.inventoryAdd.set({
      inventory_item_id: null,
      quantity: 1,
    })
    this.showAddItemForm.set(false)
  }
}
