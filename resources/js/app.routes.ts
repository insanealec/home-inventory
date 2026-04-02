import { Routes } from '@angular/router'
import { authGuard } from './services/auth.guard'

export const routes: Routes = [
  // Public routes
  {
    path: '',
    loadComponent: () => import('./pages/welcome/welcome.component').then(m => m.WelcomeComponent),
  },
  {
    path: 'login',
    loadComponent: () => import('./pages/auth/login.component').then(m => m.LoginComponent),
  },
  {
    path: 'register',
    loadComponent: () => import('./pages/auth/register.component').then(m => m.RegisterComponent),
  },

  // Authenticated routes
  {
    path: '',
    canActivate: [authGuard],
    children: [
      {
        path: 'dashboard',
        loadComponent: () => import('./pages/dashboard/dashboard.component').then(m => m.DashboardComponent),
      },

      {
        path: 'inventory',
        loadComponent: () => import('./pages/inventory-items/index.component').then(m => m.InventoryIndexComponent),
      },
      {
        path: 'inventory/create',
        loadComponent: () => import('./pages/inventory-items/create.component').then(m => m.InventoryCreateComponent),
      },
      {
        path: 'inventory/:id',
        loadComponent: () => import('./pages/inventory-items/show.component').then(m => m.InventoryShowComponent),
      },
      {
        path: 'inventory/:id/edit',
        loadComponent: () => import('./pages/inventory-items/update.component').then(m => m.InventoryUpdateComponent),
      },

      {
        path: 'stock-locations',
        loadComponent: () => import('./pages/stock-locations/index.component').then(m => m.StockLocationIndexComponent),
      },
      {
        path: 'stock-locations/create',
        loadComponent: () => import('./pages/stock-locations/create.component').then(m => m.StockLocationCreateComponent),
      },
      {
        path: 'stock-locations/:id',
        loadComponent: () => import('./pages/stock-locations/show.component').then(m => m.StockLocationShowComponent),
      },
      {
        path: 'stock-locations/:id/edit',
        loadComponent: () => import('./pages/stock-locations/update.component').then(m => m.StockLocationUpdateComponent),
      },

      {
        path: 'shopping-lists',
        loadComponent: () => import('./pages/shopping-lists/index.component').then(m => m.ShoppingListIndexComponent),
      },
      {
        path: 'shopping-lists/create',
        loadComponent: () => import('./pages/shopping-lists/create.component').then(m => m.ShoppingListCreateComponent),
      },
      {
        path: 'shopping-lists/:id',
        loadComponent: () => import('./pages/shopping-lists/show.component').then(m => m.ShoppingListShowComponent),
      },
      {
        path: 'shopping-lists/:id/edit',
        loadComponent: () => import('./pages/shopping-lists/update.component').then(m => m.ShoppingListUpdateComponent),
      },

      {
        path: 'notifications',
        loadComponent: () => import('./pages/notifications/notifications.component').then(m => m.NotificationsComponent),
      },
      {
        path: 'settings/notifications',
        loadComponent: () => import('./pages/settings/notification-preferences.component').then(m => m.NotificationPreferencesComponent),
      },
      {
        path: 'profile',
        loadComponent: () => import('./pages/profile/profile.component').then(m => m.ProfileComponent),
      },
    ],
  },

  // Catch-all
  { path: '**', redirectTo: '' },
]
