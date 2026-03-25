# Routing & Auth

## Route config (app.routes.ts)

Mirrors the 19 Vue Router routes exactly. Authenticated routes are protected by `authGuard`.

```typescript
import { Routes } from '@angular/router'
import { authGuard } from './services/auth.guard'

export const routes: Routes = [
  // Public routes
  { path: '', loadComponent: () => import('./pages/welcome/welcome.component').then(m => m.WelcomeComponent) },
  { path: 'login', loadComponent: () => import('./pages/auth/login.component').then(m => m.LoginComponent) },
  { path: 'register', loadComponent: () => import('./pages/auth/register.component').then(m => m.RegisterComponent) },

  // Authenticated routes
  {
    path: '',
    canActivate: [authGuard],
    children: [
      { path: 'dashboard', loadComponent: () => import('./pages/dashboard/dashboard.component').then(m => m.DashboardComponent) },

      { path: 'inventory', loadComponent: () => import('./pages/inventory-items/index.component').then(m => m.InventoryIndexComponent) },
      { path: 'inventory/create', loadComponent: () => import('./pages/inventory-items/create.component').then(m => m.InventoryCreateComponent) },
      { path: 'inventory/:id', loadComponent: () => import('./pages/inventory-items/show.component').then(m => m.InventoryShowComponent) },
      { path: 'inventory/:id/edit', loadComponent: () => import('./pages/inventory-items/update.component').then(m => m.InventoryUpdateComponent) },

      { path: 'stock-locations', loadComponent: () => import('./pages/stock-locations/index.component').then(m => m.StockLocationIndexComponent) },
      { path: 'stock-locations/create', loadComponent: () => import('./pages/stock-locations/create.component').then(m => m.StockLocationCreateComponent) },
      { path: 'stock-locations/:id', loadComponent: () => import('./pages/stock-locations/show.component').then(m => m.StockLocationShowComponent) },
      { path: 'stock-locations/:id/edit', loadComponent: () => import('./pages/stock-locations/update.component').then(m => m.StockLocationUpdateComponent) },

      { path: 'shopping-lists', loadComponent: () => import('./pages/shopping-lists/index.component').then(m => m.ShoppingListIndexComponent) },
      { path: 'shopping-lists/create', loadComponent: () => import('./pages/shopping-lists/create.component').then(m => m.ShoppingListCreateComponent) },
      { path: 'shopping-lists/:id', loadComponent: () => import('./pages/shopping-lists/show.component').then(m => m.ShoppingListShowComponent) },
      { path: 'shopping-lists/:id/edit', loadComponent: () => import('./pages/shopping-lists/update.component').then(m => m.ShoppingListUpdateComponent) },

      { path: 'settings/notifications', loadComponent: () => import('./pages/settings/notification-preferences.component').then(m => m.NotificationPreferencesComponent) },
    ],
  },

  { path: '**', redirectTo: '' },
]
```

`loadComponent` (lazy loading) is preferred — each page is a separate chunk, which matches
how Vue Router imports worked with Vite.

## Auth guard (auth.guard.ts)

```typescript
import { inject } from '@angular/core'
import { CanActivateFn, Router } from '@angular/router'
import { AuthService } from './auth.service'

export const authGuard: CanActivateFn = () => {
  const auth = inject(AuthService)
  const router = inject(Router)

  if (auth.isAuthenticated()) return true

  // Not authenticated — redirect to login
  return router.createUrlTree(['/login'])
}
```

This replaces Vue Router navigation guards. The guard runs before any authenticated route
is rendered.

## CSRF interceptor for Sanctum (csrf.interceptor.ts)

Laravel Sanctum uses cookie-based CSRF protection for browser requests. Angular's
`HttpClient` doesn't send the `X-XSRF-TOKEN` header automatically (unlike axios, which
does). Add this interceptor:

```typescript
import { HttpInterceptorFn } from '@angular/common/http'

function getCookie(name: string): string | null {
  const match = document.cookie.match(new RegExp(`(^| )${name}=([^;]+)`))
  return match ? decodeURIComponent(match[2]) : null
}

export const csrfInterceptor: HttpInterceptorFn = (req, next) => {
  const token = getCookie('XSRF-TOKEN')

  if (token && ['POST', 'PUT', 'PATCH', 'DELETE'].includes(req.method)) {
    req = req.clone({ setHeaders: { 'X-XSRF-TOKEN': token } })
  }

  return next(req)
}
```

Register it in `app.ts` (already shown in `setup.md`):
```typescript
provideHttpClient(withInterceptors([csrfInterceptor]))
```

The existing `resources/js/bootstrap.js` calls `axios.defaults.withCredentials = true`
and sets the `X-Requested-With` header — this remains in place. Angular's `HttpClient`
also needs `withCredentials: true` to send the session cookie. Set this globally:

```typescript
// In app.ts providers:
provideHttpClient(
  withInterceptors([csrfInterceptor]),
  withRequestsMadeViaParent(), // only needed if using parent injector
)
```

Or set it per-request via an interceptor if needed. The simplest approach is to set it
globally using `withXsrfConfiguration`:

```typescript
// In app.ts
import { provideHttpClient, withInterceptors, withXsrfConfiguration } from '@angular/common/http'

provideHttpClient(
  withInterceptors([csrfInterceptor]),
  withXsrfConfiguration({
    cookieName: 'XSRF-TOKEN',
    headerName: 'X-XSRF-TOKEN',
  }),
)
```

With `withXsrfConfiguration`, Angular handles the XSRF token automatically — you may not
need the manual `csrfInterceptor` at all. Test both approaches; `withXsrfConfiguration` is
simpler if it works for your setup.

## Reading route params in page components

```typescript
import { Component, inject, OnInit } from '@angular/core'
import { ActivatedRoute } from '@angular/router'

@Component({ ... })
export class InventoryShowComponent implements OnInit {
  private route = inject(ActivatedRoute)
  private service = inject(InventoryService)

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'))
    this.service.loadItem(id)
  }
}
```

## Programmatic navigation (replacing `window.location.href`)

The `AuthService` uses `window.location.href` for post-login/logout redirects (full page
reload to re-seed the session). This is intentional — Fortify's session cookie is set
server-side and requires a page reload to be reflected in `window.App.user`.

For in-app navigation (e.g., after creating an item), use the Angular Router:

```typescript
import { Router } from '@angular/router'

export class CreateComponent {
  private router = inject(Router)
  private service = inject(InventoryService)

  async submit(): Promise<void> {
    const item = this.service.item()
    if (!item) return
    const ok = await this.service.createItem(item)
    if (ok) this.router.navigate(['/inventory'])
  }
}
```

## RouterLink active class

Vue Router uses `router-link-active` / `router-link-exact-active`. Angular uses
`routerLinkActive`:

```html
<!-- Vue -->
<router-link to="/dashboard" active-class="border-indigo-500 text-gray-900">Dashboard</router-link>

<!-- Angular -->
<a routerLink="/dashboard" routerLinkActive="border-indigo-500 text-gray-900"
   [routerLinkActiveOptions]="{ exact: true }">
  Dashboard
</a>
```

Apply this to all nav links in `NavMainComponent` and `NavGuestComponent`.
