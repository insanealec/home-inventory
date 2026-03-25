# Services & State (Pinia → Angular)

## The pattern

Each Pinia store becomes an `@Injectable({ providedIn: 'root' })` service. State is held
in **signals** (writable). Computed values use Angular's `computed()`. The service exposes
public readonly signals for components to read, and methods that mutate them.

This keeps the same intent as Pinia's Composition API stores — co-located state + actions —
just using Angular idioms.

## Standard service skeleton

This is the template for every resource service (inventory, stock locations, shopping lists, etc.):

```typescript
import { Injectable, signal, computed, inject } from '@angular/core'
import { HttpClient } from '@angular/common/http'
import { firstValueFrom } from 'rxjs'
import { Pagination } from '../types/common'
import { SomeType } from '../types/some-type'

@Injectable({ providedIn: 'root' })
export class SomeService {
  private http = inject(HttpClient)

  // State signals
  readonly paginator = signal<Pagination<SomeType>>({
    data: [], current_page: 1, last_page: 1, per_page: 15, total: 0, links: [],
  })
  readonly item = signal<SomeType | null>(null)
  readonly errors = signal<Record<string, string[]>>({})
  readonly loading = signal(false)
  readonly creating = signal(false)
  readonly updating = signal(false)
  readonly deleting = signal(false)

  // Computed (derived from signals)
  readonly items = computed(() => this.paginator().data)

  async loadItems(page = 1): Promise<void> {
    this.loading.set(true)
    try {
      const data = await firstValueFrom(
        this.http.get<Pagination<SomeType>>('/api/some-resource', { params: { page } })
      )
      this.paginator.set(data)
    } finally {
      this.loading.set(false)
    }
  }

  async loadItem(id: number): Promise<void> {
    this.loading.set(true)
    try {
      const data = await firstValueFrom(this.http.get<SomeType>(`/api/some-resource/${id}`))
      this.item.set(data)
    } finally {
      this.loading.set(false)
    }
  }

  async createItem(payload: Partial<SomeType>): Promise<boolean> {
    this.creating.set(true)
    this.errors.set({})
    try {
      await firstValueFrom(this.http.post('/api/some-resource', payload))
      return true
    } catch (error: any) {
      if (error.status === 422) this.errors.set(error.error.errors ?? {})
      return false
    } finally {
      this.creating.set(false)
    }
  }

  async updateItem(id: number, payload: Partial<SomeType>): Promise<boolean> {
    this.updating.set(true)
    try {
      const data = await firstValueFrom(this.http.put<SomeType>(`/api/some-resource/${id}`, payload))
      this.item.set(data)
      return true
    } catch {
      return false
    } finally {
      this.updating.set(false)
    }
  }

  async deleteItem(id: number): Promise<boolean> {
    this.deleting.set(true)
    try {
      await firstValueFrom(this.http.delete(`/api/some-resource/${id}`))
      return true
    } catch {
      return false
    } finally {
      this.deleting.set(false)
    }
  }
}
```

## Using `firstValueFrom`

`HttpClient` methods return Observables. `firstValueFrom()` converts them to Promises,
which lets the services keep the same `async/await` style as the Pinia stores. Import it
from `rxjs`.

## InventoryService

Converts `stores/inventory.ts`. Uses Wayfinder action URLs:

```typescript
import LoadItems from '../actions/App/Actions/InventoryItem/LoadItems'
import LoadItem from '../actions/App/Actions/InventoryItem/LoadItem'
import CreateItem from '../actions/App/Actions/InventoryItem/CreateItem'
import UpdateItem from '../actions/App/Actions/InventoryItem/UpdateItem'
import DeleteItem from '../actions/App/Actions/InventoryItem/DeleteItem'

// In methods, use the Wayfinder URL helpers:
const data = await firstValueFrom(
  this.http.get<Pagination<InventoryItem>>(LoadItems.url(), { params })
)
```

The `initItem()` method becomes `resetItem()` in Angular convention:

```typescript
resetItem(): void {
  this.item.set(createInventoryItem())
}
```

## StockLocationService

Same pattern as InventoryService. Uses Wayfinder URLs from
`actions/App/Actions/StockLocation/`.

## ShoppingListService

More complex — handles both lists and items. Keep both in one service since the items
always belong to a list and are always fetched in that context:

```typescript
readonly list = signal<ShoppingList | null>(null)
readonly items = signal<ShoppingListItem[]>([])
// ... standard paginator, loading, errors signals

async addBulkItems(listId: number, newItems: Partial<ShoppingListItem>[]): Promise<any> {
  const data = await firstValueFrom(
    this.http.post<{ created: ShoppingListItem[] }>(
      `/api/shopping-lists/${listId}/items/bulk`,
      { items: newItems }
    )
  )
  this.items.update(current => [...current, ...data.created])
  return data
}

async bulkUpdateItems(listId: number, updates: Record<number, Partial<ShoppingListItem>>): Promise<any> {
  const data = await firstValueFrom(
    this.http.put<{ updated: number[] }>(
      `/api/shopping-lists/${listId}/items/bulk`,
      { updates }
    )
  )
  this.items.update(current =>
    current.map(item => updates[item.id] ? { ...item, ...updates[item.id] } : item)
  )
  return data
}
```

## NotificationService

Converts `stores/notifications.ts`. Was the only store using the `apiService` wrapper —
in Angular it uses `HttpClient` directly like all other services:

```typescript
@Injectable({ providedIn: 'root' })
export class NotificationService {
  private http = inject(HttpClient)

  readonly notifications = signal<NotificationItem[]>([])
  readonly preferences = signal<NotificationPreferences>({ low_stock: true, expiring_items: true })
  readonly loading = signal(false)
  readonly preferencesLoading = signal(false)

  readonly unreadCount = computed(() =>
    this.notifications().filter(n => n.read_at === null).length
  )

  async fetchNotifications(): Promise<void> {
    this.loading.set(true)
    try {
      const data = await firstValueFrom(this.http.get<NotificationItem[]>('/api/notifications'))
      this.notifications.set(data)
    } finally {
      this.loading.set(false)
    }
  }

  async markRead(id: string): Promise<void> {
    await firstValueFrom(this.http.put(`/api/notifications/${id}`, {}))
    this.notifications.update(list =>
      list.map(n => n.id === id ? { ...n, read_at: new Date().toISOString() } : n)
    )
  }

  async dismiss(id: string): Promise<void> {
    await firstValueFrom(this.http.delete(`/api/notifications/${id}`))
    this.notifications.update(list => list.filter(n => n.id !== id))
  }

  async markAllRead(): Promise<void> {
    const unread = this.notifications().filter(n => n.read_at === null)
    await Promise.all(unread.map(n => this.markRead(n.id)))
  }
}
```

## AuthService

Converts `stores/auth.ts`. Holds the current user and handles login/register/logout.
User state is seeded from `window.App.user` by `AppComponent.ngOnInit()`.

```typescript
import { Injectable, signal, computed, inject } from '@angular/core'
import { HttpClient } from '@angular/common/http'
import { firstValueFrom } from 'rxjs'
import { User } from '../types/auth'

@Injectable({ providedIn: 'root' })
export class AuthService {
  private http = inject(HttpClient)

  readonly user = signal<User | null>(null)
  readonly isAuthenticated = computed(() => this.user() !== null)
  readonly errors = signal<Record<string, string[]>>({})

  setUser(user: User | null): void {
    this.user.set(user)
  }

  async login(credentials: { email: string; password: string }): Promise<void> {
    this.errors.set({})
    try {
      const response = await firstValueFrom(
        this.http.post('/login', credentials, { observe: 'response' })
      )
      if (response.status === 200) window.location.href = '/dashboard'
    } catch (error: any) {
      if (error.status === 422) this.errors.set(error.error.errors ?? {})
    }
  }

  async register(data: { name: string; email: string; password: string; password_confirmation: string }): Promise<void> {
    this.errors.set({})
    try {
      const response = await firstValueFrom(
        this.http.post('/register', data, { observe: 'response' })
      )
      if (response.status === 201) window.location.href = '/dashboard'
    } catch (error: any) {
      if (error.status === 422) this.errors.set(error.error.errors ?? {})
    }
  }

  async logout(): Promise<void> {
    await firstValueFrom(this.http.post('/logout', {}))
    window.location.href = '/'
  }
}
```

## TokenService

Converts `stores/token.ts`. Manages Sanctum API tokens:

```typescript
@Injectable({ providedIn: 'root' })
export class TokenService {
  private http = inject(HttpClient)

  readonly tokens = signal<any[]>([])
  readonly newTokenName = signal('')

  async loadTokens(): Promise<void> {
    const data = await firstValueFrom(this.http.get<any[]>('/api/tokens'))
    this.tokens.set(data)
  }

  async storeToken(): Promise<void> {
    if (!this.newTokenName().trim()) return
    const data = await firstValueFrom(
      this.http.post<{ accessToken: any; plainTextToken: string }>(
        '/api/tokens', { name: this.newTokenName(), abilities: ['*'] }
      )
    )
    this.tokens.update(list => [...list, data.accessToken])
    alert(`New Token Created: ${data.plainTextToken}`) // TODO: modal
    this.newTokenName.set('')
  }

  async destroyToken(tokenId: string): Promise<void> {
    await firstValueFrom(this.http.delete(`/api/tokens/${tokenId}`))
    this.tokens.update(list => list.filter(t => t.id !== tokenId))
  }
}
```
