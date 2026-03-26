import { Injectable, signal, computed, inject } from '@angular/core'
import { HttpClient } from '@angular/common/http'
import { firstValueFrom } from 'rxjs'
import { Pagination } from '../types/common'
import { ShoppingList, ShoppingListItem } from '../types/shopping-list'

@Injectable({ providedIn: 'root' })
export class ShoppingListService {
  private http = inject(HttpClient)

  readonly paginator = signal<Pagination<ShoppingList>>({
    data: [],
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
    links: [],
  })
  readonly list = signal<ShoppingList | null>(null)
  readonly items = signal<ShoppingListItem[]>([])
  readonly errors = signal<Record<string, string[]>>({})
  readonly loading = signal(false)
  readonly creating = signal(false)
  readonly updating = signal(false)
  readonly deleting = signal(false)

  readonly lists = computed(() => this.paginator().data)

  async loadLists(page: number = 1): Promise<void> {
    this.loading.set(true)
    try {
      const data = await firstValueFrom(
        this.http.get<Pagination<ShoppingList>>('/api/shopping-lists', { params: { page } })
      )
      this.paginator.set(data)
    } catch (error) {
      console.error('Error loading shopping lists:', error)
    } finally {
      this.loading.set(false)
    }
  }

  async loadList(id: number): Promise<void> {
    this.loading.set(true)
    try {
      const data = await firstValueFrom(
        this.http.get<ShoppingList>(`/api/shopping-lists/${id}`)
      )
      this.list.set(data)
      if (data.items) {
        this.items.set(data.items)
      }
    } catch (error) {
      console.error('Error loading shopping list:', error)
    } finally {
      this.loading.set(false)
    }
  }

  async loadItems(listId: number): Promise<void> {
    this.loading.set(true)
    try {
      const data = await firstValueFrom(
        this.http.get<ShoppingListItem[]>(`/api/shopping-lists/${listId}/items`)
      )
      this.items.set(data)
    } catch (error) {
      console.error('Error loading shopping list items:', error)
    } finally {
      this.loading.set(false)
    }
  }

  initList(): void {
    this.list.set({
      id: 0,
      name: '',
      notes: '',
      is_completed: false,
      shopping_date: null,
      user_id: 0,
      created_at: '',
      updated_at: '',
    })
  }

  resetList(): void {
    this.initList()
  }

  async createList(data: Partial<ShoppingList>): Promise<boolean> {
    try {
      this.creating.set(true)
      this.errors.set({})
      const response = await firstValueFrom(
        this.http.post<ShoppingList>('/api/shopping-lists', data)
      )
      this.list.set(response)
      return true
    } catch (error: any) {
      if (error.status === 422) {
        this.errors.set(error.error.errors ?? {})
      }
      console.error('Error creating shopping list:', error)
      return false
    } finally {
      this.creating.set(false)
    }
  }

  async updateList(id: number, data: Partial<ShoppingList>): Promise<boolean> {
    try {
      this.updating.set(true)
      const response = await firstValueFrom(
        this.http.put<ShoppingList>(`/api/shopping-lists/${id}`, data)
      )
      this.list.set(response)
      return true
    } catch (error) {
      console.error('Error updating shopping list:', error)
      return false
    } finally {
      this.updating.set(false)
    }
  }

  async deleteList(id: number): Promise<boolean> {
    try {
      this.deleting.set(true)
      await firstValueFrom(this.http.delete(`/api/shopping-lists/${id}`))
      return true
    } catch (error) {
      console.error('Error deleting shopping list:', error)
      return false
    } finally {
      this.deleting.set(false)
    }
  }

  async createItem(listId: number, data: Partial<ShoppingListItem>): Promise<boolean> {
    try {
      const response = await firstValueFrom(
        this.http.post<ShoppingListItem>(`/api/shopping-lists/${listId}/items`, data)
      )
      this.items.update(current => [...current, response])
      return true
    } catch (error) {
      console.error('Error creating shopping list item:', error)
      return false
    }
  }

  async updateItem(
    listId: number,
    itemId: number,
    data: Partial<ShoppingListItem>
  ): Promise<boolean> {
    try {
      const response = await firstValueFrom(
        this.http.put<ShoppingListItem>(
          `/api/shopping-lists/${listId}/items/${itemId}`,
          data
        )
      )
      this.items.update(current =>
        current.map(item => (item.id === itemId ? response : item))
      )
      return true
    } catch (error) {
      console.error('Error updating shopping list item:', error)
      return false
    }
  }

  async deleteItem(listId: number, itemId: number): Promise<boolean> {
    try {
      await firstValueFrom(this.http.delete(`/api/shopping-lists/${listId}/items/${itemId}`))
      this.items.update(current => current.filter(item => item.id !== itemId))
      return true
    } catch (error) {
      console.error('Error deleting shopping list item:', error)
      return false
    }
  }

  async addBulkItems(
    listId: number,
    newItems: Partial<ShoppingListItem>[]
  ): Promise<any> {
    try {
      const response = await firstValueFrom(
        this.http.post<{ created: ShoppingListItem[] }>(
          `/api/shopping-lists/${listId}/items/bulk`,
          { items: newItems }
        )
      )
      if (response.created) {
        this.items.update(current => [...current, ...response.created])
      }
      return response
    } catch (error) {
      console.error('Error adding bulk items:', error)
      return null
    }
  }

  async bulkUpdateItems(
    listId: number,
    updates: Record<number, Partial<ShoppingListItem>>
  ): Promise<any> {
    try {
      const response = await firstValueFrom(
        this.http.put<{ updated: number[] }>(
          `/api/shopping-lists/${listId}/items/bulk`,
          { updates }
        )
      )
      this.items.update(current =>
        current.map(item =>
          updates[item.id] ? { ...item, ...updates[item.id] } : item
        )
      )
      return response
    } catch (error) {
      console.error('Error updating bulk items:', error)
      return null
    }
  }

  async addInventoryItem(
    listId: number,
    inventoryItemId: number,
    quantity: number
  ): Promise<boolean> {
    try {
      const response = await firstValueFrom(
        this.http.post<ShoppingListItem>(
          `/api/shopping-lists/${listId}/items/from-inventory`,
          { inventory_item_id: inventoryItemId, quantity }
        )
      )
      this.items.update(current => [...current, response])
      return true
    } catch (error) {
      console.error('Error adding inventory item to shopping list:', error)
      return false
    }
  }

  async addStandaloneItem(
    listId: number,
    data: Partial<ShoppingListItem>
  ): Promise<boolean> {
    try {
      const response = await firstValueFrom(
        this.http.post<ShoppingListItem>(
          `/api/shopping-lists/${listId}/items/standalone`,
          data
        )
      )
      this.items.update(current => [...current, response])
      return true
    } catch (error) {
      console.error('Error adding standalone item to shopping list:', error)
      return false
    }
  }
}
