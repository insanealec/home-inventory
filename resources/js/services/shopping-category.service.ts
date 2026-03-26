import { Injectable, signal, computed, inject } from '@angular/core'
import { HttpClient } from '@angular/common/http'
import { firstValueFrom } from 'rxjs'
import { ShoppingCategory } from '../types/shopping-list'

@Injectable({ providedIn: 'root' })
export class ShoppingCategoryService {
  private http = inject(HttpClient)

  readonly categories = signal<ShoppingCategory[]>([])
  readonly errors = signal<Record<string, string[]>>({})
  readonly loading = signal(false)
  readonly creating = signal(false)
  readonly updating = signal(false)
  readonly deleting = signal(false)

  async loadCategories(): Promise<void> {
    this.loading.set(true)
    try {
      const data = await firstValueFrom(
        this.http.get<ShoppingCategory[]>('/api/shopping-categories')
      )
      this.categories.set(data)
    } catch (error) {
      console.error('Error loading shopping categories:', error)
    } finally {
      this.loading.set(false)
    }
  }

  async createCategory(data: Partial<ShoppingCategory>): Promise<boolean> {
    try {
      this.creating.set(true)
      this.errors.set({})
      const response = await firstValueFrom(
        this.http.post<ShoppingCategory>('/api/shopping-categories', data)
      )
      this.categories.update(current => [...current, response])
      return true
    } catch (error: any) {
      if (error.status === 422) {
        this.errors.set(error.error.errors ?? {})
      }
      console.error('Error creating shopping category:', error)
      return false
    } finally {
      this.creating.set(false)
    }
  }

  async updateCategory(id: number, data: Partial<ShoppingCategory>): Promise<boolean> {
    try {
      this.updating.set(true)
      const response = await firstValueFrom(
        this.http.put<ShoppingCategory>(`/api/shopping-categories/${id}`, data)
      )
      this.categories.update(current =>
        current.map(cat => (cat.id === id ? response : cat))
      )
      return true
    } catch (error) {
      console.error('Error updating shopping category:', error)
      return false
    } finally {
      this.updating.set(false)
    }
  }

  async deleteCategory(id: number): Promise<boolean> {
    try {
      this.deleting.set(true)
      await firstValueFrom(this.http.delete(`/api/shopping-categories/${id}`))
      this.categories.update(current => current.filter(cat => cat.id !== id))
      return true
    } catch (error) {
      console.error('Error deleting shopping category:', error)
      return false
    } finally {
      this.deleting.set(false)
    }
  }
}
