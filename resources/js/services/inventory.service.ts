import { Injectable, signal, computed, inject } from '@angular/core'
import { HttpClient } from '@angular/common/http'
import { firstValueFrom } from 'rxjs'
import { Pagination } from '../types/common'
import { InventoryItem, createInventoryItem } from '../types/inventory'
import LoadItems from '../actions/App/Actions/InventoryItem/LoadItems'
import LoadItem from '../actions/App/Actions/InventoryItem/LoadItem'
import CreateItem from '../actions/App/Actions/InventoryItem/CreateItem'
import UpdateItem from '../actions/App/Actions/InventoryItem/UpdateItem'
import DeleteItem from '../actions/App/Actions/InventoryItem/DeleteItem'

@Injectable({ providedIn: 'root' })
export class InventoryService {
  private http = inject(HttpClient)

  readonly paginator = signal<Pagination<InventoryItem>>({
    data: [],
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
    links: [],
  })
  readonly item = signal<InventoryItem | null>(null)
  readonly errors = signal<Record<string, string[]>>({})
  readonly loading = signal(false)
  readonly creating = signal(false)
  readonly updating = signal(false)
  readonly deleting = signal(false)

  readonly items = computed(() => this.paginator().data)

  async loadItems(
    page: number = 1,
    sortField: string | null = null,
    sortDirection: 'asc' | 'desc' = 'asc'
  ): Promise<void> {
    this.loading.set(true)
    try {
      const params: any = { page }
      if (sortField) {
        params.sort = sortField
        params.direction = sortDirection
      }
      const data = await firstValueFrom(
        this.http.get<Pagination<InventoryItem>>(LoadItems.url(), { params })
      )
      this.paginator.set(data)
    } catch (error) {
      console.error('Error loading inventory items:', error)
    } finally {
      this.loading.set(false)
    }
  }

  async loadItem(id: number): Promise<void> {
    this.loading.set(true)
    try {
      const data = await firstValueFrom(
        this.http.get<InventoryItem>(LoadItem.url(id))
      )
      this.item.set(data)
    } catch (error) {
      console.error('Error loading inventory item:', error)
    } finally {
      this.loading.set(false)
    }
  }

  resetItem(): void {
    this.item.set(createInventoryItem())
  }

  async createItem(): Promise<boolean> {
    try {
      this.creating.set(true)
      this.errors.set({})
      await firstValueFrom(
        this.http.post(CreateItem.url(), this.item())
      )
      return true
    } catch (error: any) {
      if (error.status === 422) {
        this.errors.set(error.error.errors ?? {})
      }
      console.error('Error creating inventory item:', error)
      return false
    } finally {
      this.creating.set(false)
    }
  }

  async updateItem(): Promise<boolean> {
    if (!this.item()?.id) return false
    try {
      this.updating.set(true)
      const data = await firstValueFrom(
        this.http.put<InventoryItem>(UpdateItem.url(this.item()!.id), this.item())
      )
      this.item.set(data)
      return true
    } catch (error) {
      console.error('Error updating inventory item:', error)
      return false
    } finally {
      this.updating.set(false)
    }
  }

  async deleteItem(id: number): Promise<boolean> {
    try {
      this.deleting.set(true)
      await firstValueFrom(this.http.delete(DeleteItem.url(id)))
      return true
    } catch (error) {
      console.error('Error deleting inventory item:', error)
      return false
    } finally {
      this.deleting.set(false)
    }
  }
}
