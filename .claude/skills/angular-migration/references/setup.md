# Setup: Angular + Vite + Laravel

## Install dependencies

```bash
npm install @angular/core @angular/common @angular/router @angular/forms \
            @angular/platform-browser @angular/platform-browser-dynamic \
            rxjs zone.js

npm install -D @analogjs/vite-plugin-angular \
               @angular-devkit/build-angular \
               @angular/compiler @angular/compiler-cli \
               @angular/language-service
```

## vite.config.ts

Replace the Vue plugin with the Angular plugin:

```typescript
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import analog from '@analogjs/vite-plugin-angular'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.ts'],
      refresh: true,
    }),
    analog(),
    tailwindcss(),
  ],
})
```

## tsconfig.json

Angular requires specific TypeScript options. Update or create `tsconfig.json`:

```json
{
  "compilerOptions": {
    "target": "ES2022",
    "module": "ES2022",
    "moduleResolution": "bundler",
    "experimentalDecorators": true,
    "useDefineForClassFields": false,
    "strict": true,
    "strictPropertyInitialization": false,
    "skipLibCheck": true,
    "lib": ["ES2022", "dom"],
    "paths": {
      "@/*": ["resources/js/*"]
    }
  },
  "include": ["resources/js/**/*"]
}
```

`"useDefineForClassFields": false` is required for Angular decorators to work correctly.
`"strictPropertyInitialization": false` avoids needing to initialise every class field
(Angular services often initialise them via `inject()`).

## resources/js/app.ts (bootstrap)

Replace the Vue `createApp` bootstrap with Angular's:

```typescript
import './bootstrap'
import '../css/app.css'
import { bootstrapApplication } from '@angular/platform-browser'
import { provideRouter } from '@angular/router'
import { provideHttpClient, withInterceptors } from '@angular/common/http'
import { AppComponent } from './app.component'
import { routes } from './app.routes'
import { csrfInterceptor } from './services/csrf.interceptor'

bootstrapApplication(AppComponent, {
  providers: [
    provideRouter(routes),
    provideHttpClient(withInterceptors([csrfInterceptor])),
  ],
}).catch(console.error)
```

## resources/js/app.component.ts (root component)

Replaces `App.vue`. Reads the initial user from `window.App.user` and conditionally
renders the correct nav:

```typescript
import { Component, OnInit } from '@angular/core'
import { RouterOutlet } from '@angular/router'
import { NavMainComponent } from './components/nav/nav-main.component'
import { NavGuestComponent } from './components/nav/nav-guest.component'
import { AuthService } from './services/auth.service'
import { CommonModule } from '@angular/common'

declare const App: { user: any }

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet, NavMainComponent, NavGuestComponent, CommonModule],
  template: `
    <div id="app">
      @if (auth.isAuthenticated()) {
        <app-nav-main />
      } @else {
        <app-nav-guest />
      }
      <main class="py-6 bg-white dark:bg-gray-900">
        <router-outlet />
      </main>
    </div>
  `,
})
export class AppComponent implements OnInit {
  constructor(public auth: AuthService) {}

  ngOnInit(): void {
    this.auth.setUser(window?.App?.user ?? null)
  }
}
```

## resources/views/app.blade.php

No changes needed — the blade template already mounts to `#body` and includes the
Vite assets. The Angular bootstrap above replaces `#body` content.

If the selector in `AppComponent` is `app-root`, ensure the blade template contains:
```html
<app-root id="body"></app-root>
```
or change the selector to `#body` using an attribute selector:
```typescript
selector: '[id="body"]'
```
The simplest approach is to keep `<div id="body"></div>` in the blade and use
`selector: '#body'` — Angular supports CSS id selectors.

## Removing Vue

Once all components are converted, uninstall Vue packages:

```bash
npm uninstall vue vue-router pinia @vitejs/plugin-vue lucide-vue-next \
              vue-input-otp reka-ui @vueuse/core eslint-plugin-vue \
              vue-tsc
```

Remove `eslint-plugin-vue` config and `vue-tsc` from build scripts.
