import { Injectable, signal, computed, inject } from '@angular/core'
import { HttpClient } from '@angular/common/http'
import { firstValueFrom } from 'rxjs'
import { Pagination } from '../types/common'
import { StockLocation, createStockLocation } from '../types/inventory'
import LoadStockLocations from '../actions/App/Actions/StockLocation/LoadStockLocations'
import LoadStockLocation from '../actions/App/Actions/StockLocation/LoadStockLocation'
import CreateStockLocation from '../actions/App/Actions/StockLocation/CreateStockLocation'
import UpdateStockLocation from '../actions/App/Actions/StockLocation/UpdateStockLocation'
import DeleteStockLocation from '../actions/App/Actions/StockLocation/DeleteStockLocation'

@Injectable({ providedIn: 'root' })
export class StockLocationService {
  private http = inject(HttpClient)

  readonly paginator = signal<Pagination<StockLocation>>({
    data: [],
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
    links: [],
  })
  readonly stockLocation = signal<StockLocation | null>(null)
  readonly errors = signal<Record<string, string[]>>({})
  readonly loading = signal(false)
  readonly creating = signal(false)
  readonly updating = signal(false)
  readonly deleting = signal(false)

  readonly stockLocations = computed(() => this.paginator().data)

  async loadStockLocations(
    page: number = 1,
    sortField: string | null = null,
    sortDirection: 'asc' | 'desc' = 'asc'
  ): Promise<void> {
    this.loading.set(true)
    try {
      const params: any = { page }
      if (sortField) {
        params.sortBy = sortField
        params.sortDirection = sortDirection
      }
      const data = await firstValueFrom(
        this.http.get<Pagination<StockLocation>>(LoadStockLocations.url(), { params })
      )
      this.paginator.set(data)
    } catch (error) {
      console.error('Error loading stock locations:', error)
    } finally {
      this.loading.set(false)
    }
  }

  async loadStockLocation(id: number): Promise<void> {
    this.loading.set(true)
    try {
      const data = await firstValueFrom(
        this.http.get<StockLocation>(LoadStockLocation.url(id))
      )
      this.stockLocation.set(data)
    } catch (error) {
      console.error('Error loading stock location:', error)
    } finally {
      this.loading.set(false)
    }
  }

  resetStockLocation(): void {
    this.stockLocation.set(createStockLocation())
  }

  async createStockLocation(): Promise<boolean> {
    try {
      this.creating.set(true)
      this.errors.set({})
      await firstValueFrom(
        this.http.post(CreateStockLocation.url(), this.stockLocation())
      )
      return true
    } catch (error: any) {
      if (error.status === 422) {
        this.errors.set(error.error.errors ?? {})
      }
      console.error('Error creating stock location:', error)
      return false
    } finally {
      this.creating.set(false)
    }
  }

  async updateStockLocation(): Promise<boolean> {
    if (!this.stockLocation()?.id) return false
    try {
      this.updating.set(true)
      const data = await firstValueFrom(
        this.http.put<StockLocation>(
          UpdateStockLocation.url(this.stockLocation()!.id),
          this.stockLocation()
        )
      )
      this.stockLocation.set(data)
      return true
    } catch (error) {
      console.error('Error updating stock location:', error)
      return false
    } finally {
      this.updating.set(false)
    }
  }

  async deleteStockLocation(id: number): Promise<boolean> {
    try {
      this.deleting.set(true)
      await firstValueFrom(this.http.delete(DeleteStockLocation.url(id)))
      return true
    } catch (error) {
      console.error('Error deleting stock location:', error)
      return false
    } finally {
      this.deleting.set(false)
    }
  }
}
