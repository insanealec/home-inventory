---
name: angular-migration
description: >
  Use this skill whenever working on Angular frontend code in this project, including
  converting Vue components to Angular, creating new Angular components or services,
  setting up routing, handling HTTP calls, or managing reactive state with signals.
  Trigger on any task touching resources/js files after the Angular migration has begun,
  or any task mentioning Angular, components, services, signals, or the frontend build.
---

# Angular Migration — Home Inventory

This project is migrating its frontend from **Vue 3 + Pinia** to **Angular (standalone components + Signals)**,
keeping the existing **Laravel 12 / Fortify / Sanctum** backend and **TailwindCSS 4** untouched.

The build pipeline uses **@analogjs/vite-plugin-angular** so Angular runs inside the existing
Vite + `laravel-vite-plugin` setup — no separate Angular CLI build.

Before starting any task, decide which reference file you need and read it:

| Task | Reference file |
|---|---|
| Initial project setup / install | `references/setup.md` |
| Creating or converting a component | `references/components.md` |
| Converting a Pinia store to a service | `references/services.md` |
| Routing, auth guards, navigation | `references/routing-auth.md` |

---

## Project layout (after migration)

```
resources/js/
├── app.ts                  ← bootstraps Angular (replaces Vue createApp)
├── app.component.ts        ← root AppComponent (replaces App.vue)
├── app.routes.ts           ← route config (replaces createRouter in app.ts)
├── components/             ← shared/common Angular components
│   ├── nav/
│   └── common/
├── pages/                  ← routed page components (one folder per domain)
│   ├── auth/
│   ├── dashboard/
│   ├── inventory-items/
│   ├── stock-locations/
│   ├── shopping-lists/
│   └── settings/
├── services/               ← Angular services (replaces stores/)
│   ├── auth.service.ts
│   ├── inventory.service.ts
│   ├── stock-location.service.ts
│   ├── shopping-list.service.ts
│   ├── shopping-category.service.ts
│   ├── notification.service.ts
│   └── token.service.ts
├── types/                  ← TypeScript types (unchanged from Vue)
│   ├── auth.ts
│   ├── common.ts
│   ├── inventory.ts
│   └── shopping-list.ts
├── actions/                ← Wayfinder route helpers (unchanged, reuse as-is)
└── routes/                 ← Wayfinder API routes (unchanged)
```

The `types/` and `actions/` folders carry over from Vue **unchanged** — they have no Vue dependencies.

---

## Core conventions

### Components are always standalone

Every component uses `standalone: true` and imports its own dependencies directly.
There are no `NgModule` files in this project.

```typescript
@Component({
  selector: 'app-example',
  standalone: true,
  imports: [CommonModule, RouterLink, FormsModule],
  templateUrl: './example.component.html',
})
export class ExampleComponent { }
```

### State lives in services using signals

Services are `@Injectable({ providedIn: 'root' })` and expose state as signals.
Loading, error, and data state all follow the same pattern — see `references/services.md`.

### HTTP goes through `HttpClient`

All HTTP calls use Angular's `HttpClient`, injected into services. The CSRF/Sanctum
interceptor is configured at bootstrap — see `references/routing-auth.md`.

### Routing mirrors the existing Vue Router routes exactly

The same 19 routes, same paths, same structure. Auth guards protect the authenticated routes.
See `references/routing-auth.md` for the full route config.

### TailwindCSS classes are unchanged

All Tailwind utility classes from the Vue templates transfer directly to Angular templates.
No changes to `resources/css/app.css`.

---

## Vue → Angular quick reference

| Vue | Angular |
|---|---|
| `v-if="expr"` | `@if (expr) { }` |
| `v-for="item in list"` | `@for (item of list; track item.id) { }` |
| `v-model="val"` | `[(ngModel)]="val"` (needs `FormsModule`) |
| `v-bind:prop` / `:prop` | `[prop]="expr"` |
| `v-on:click` / `@click` | `(click)="handler()"` |
| `<slot>` / `<slot name="x">` | `<ng-content>` / `<ng-content select="[slot=x]">` |
| `router-link to="/path"` | `routerLink="/path"` (needs `RouterLink` import) |
| `$emit('event', val)` | `@Output() event = new EventEmitter()` |
| `defineProps<{...}>()` | `@Input() propName: type` |
| `onMounted(() => ...)` | `ngOnInit()` (implement `OnInit`) |
| `computed(() => ...)` | `computed(() => ...)` (Angular signal) |
| `ref(value)` | `signal(value)` |
| `store.someValue` | `this.service.someValue()` (call signal) |
| `store.someValue = x` | `this.service.someSignal.set(x)` |
