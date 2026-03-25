# Component Patterns

## Anatomy of a standalone Angular component

Every Vue SFC becomes two files: a `.component.ts` (class + metadata) and optionally a
`.component.html` (template). For smaller components, keep the template inline.

```
nav-main.component.ts     ← class, decorator, inline or templateUrl
nav-main.component.html   ← (optional) separate template
```

**Naming convention**: kebab-case selector prefix `app-`, PascalCase class.
- `NavMainComponent` → selector `app-nav-main`
- `NotificationBellComponent` → selector `app-notification-bell`
- `InventoryIndexComponent` → selector `app-inventory-index` (page components)

## Minimal component template

```typescript
import { Component } from '@angular/core'
import { CommonModule } from '@angular/common'

@Component({
  selector: 'app-card',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
        <ng-content select="[slot=title]" />
      </h1>
      <ng-content />
    </div>
  `,
})
export class CardComponent {}
```

## Slots → ng-content

Vue named slots map to `ng-content` with a CSS selector attribute:

```html
<!-- Vue usage -->
<Card><template #title>My Title</template>Content here</Card>

<!-- Angular equivalent usage -->
<app-card><span slot="title">My Title</span>Content here</app-card>
```

```typescript
// Angular component
template: `
  <div>
    <ng-content select="[slot=title]" />  <!-- named slot -->
    <ng-content />                         <!-- default slot -->
  </div>
`
```

## Props → @Input

```typescript
// Vue
const props = defineProps<{ isOpen: boolean; title?: string }>()

// Angular
import { Input } from '@angular/core'
@Input() isOpen = false
@Input() title?: string
```

## Events → @Output

```typescript
// Vue
const emit = defineEmits<{ (e: 'close'): void }>()

// Angular
import { Output, EventEmitter } from '@angular/core'
@Output() close = new EventEmitter<void>()

// Emit it:
this.close.emit()

// In template:
// (close)="handleClose()"
```

## Injecting services

```typescript
import { Component, inject } from '@angular/core'
import { AuthService } from '../../services/auth.service'

@Component({ ... })
export class SomeComponent {
  // Modern Angular style — prefer inject() over constructor injection
  auth = inject(AuthService)
}
```

## Lifecycle

```typescript
import { Component, OnInit, OnDestroy } from '@angular/core'

@Component({ ... })
export class SomeComponent implements OnInit, OnDestroy {
  ngOnInit(): void {
    // Runs on mount — equivalent to Vue's onMounted
  }

  ngOnDestroy(): void {
    // Runs on unmount — clean up subscriptions here
  }
}
```

## Reading signals from services in templates

Signals are functions — call them with `()` in templates:

```html
<!-- service.loading is a signal -->
@if (service.loading()) {
  <p>Loading...</p>
}

@for (item of service.items(); track item.id) {
  <div>{{ item.name }}</div>
}
```

## Common component conversions

### Card.vue → card.component.ts

```typescript
@Component({
  selector: 'app-card',
  standalone: true,
  template: `
    <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
        <ng-content select="[slot=title]" />
      </h1>
      <ng-content />
    </div>
  `,
})
export class CardComponent {}
```

### Content.vue → content.component.ts

```typescript
@Component({
  selector: 'app-content',
  standalone: true,
  template: `
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
        <ng-content />
      </div>
    </div>
  `,
})
export class ContentComponent {}
```

### Modal.vue → modal.component.ts

```typescript
import { Component, Input, Output, EventEmitter } from '@angular/core'

@Component({
  selector: 'app-modal',
  standalone: true,
  template: `
    @if (isOpen) {
      <div
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
        (click)="close.emit()"
      >
        <div
          class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
          (click)="$event.stopPropagation()"
        >
          <div class="p-6">
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                {{ title }}
              </h2>
              <button type="button" (click)="close.emit()" class="text-gray-400 hover:text-gray-500">
                <!-- SVG close icon -->
              </button>
            </div>
            <div class="mt-4"><ng-content /></div>
          </div>
        </div>
      </div>
    }
  `,
})
export class ModalComponent {
  @Input() isOpen = false
  @Input() title?: string
  @Output() close = new EventEmitter<void>()
}
```

### FormInput.vue → form-input.component.ts

The Vue version supports both `v-model` and store-based binding. In Angular, use standard
`[(ngModel)]` binding — the parent is responsible for passing the value and handling changes.

```typescript
import { Component, Input, Output, EventEmitter, forwardRef } from '@angular/core'
import { ControlValueAccessor, NG_VALUE_ACCESSOR, FormsModule } from '@angular/forms'

@Component({
  selector: 'app-form-input',
  standalone: true,
  imports: [FormsModule],
  providers: [{
    provide: NG_VALUE_ACCESSOR,
    useExisting: forwardRef(() => FormInputComponent),
    multi: true,
  }],
  template: `
    <div>
      <label [for]="id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        {{ label }}
      </label>
      <input
        [id]="id"
        [type]="type || 'text'"
        [min]="min"
        [step]="step"
        [ngModel]="value"
        (ngModelChange)="onChange($event)"
        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
      />
      @if (error) {
        <p class="mt-1 text-sm text-red-600">{{ error }}</p>
      }
    </div>
  `,
})
export class FormInputComponent implements ControlValueAccessor {
  @Input() id = ''
  @Input() label = ''
  @Input() type?: string
  @Input() min?: number
  @Input() step?: number
  @Input() error?: string

  value: any = ''
  onChange = (_: any) => {}
  onTouched = () => {}

  writeValue(val: any): void { this.value = val }
  registerOnChange(fn: any): void { this.onChange = fn }
  registerOnTouched(fn: any): void { this.onTouched = fn }
}
```

## Paginator component

The Vue `Paginator.vue` uses `RouterLink`. Angular equivalent uses `routerLink`:

```typescript
import { Component, Input } from '@angular/core'
import { RouterLink } from '@angular/router'
import { Pagination } from '../../types/common'

@Component({
  selector: 'app-paginator',
  standalone: true,
  imports: [RouterLink],
  template: `
    <div class="mt-4">
      <nav class="flex items-center justify-between px-4 py-3 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 sm:px-6">
        <div class="hidden sm:block">
          <p class="text-sm text-gray-700 dark:text-gray-300">
            Showing
            <span class="font-medium">{{ (paginator.current_page - 1) * paginator.per_page + 1 }}</span>
            to
            <span class="font-medium">{{ min(paginator.current_page * paginator.per_page, paginator.total) }}</span>
            of <span class="font-medium">{{ paginator.total }}</span> results
          </p>
        </div>
        <div class="flex-1 flex justify-between sm:justify-end">
          @for (link of paginator.links; track link.label) {
            <a
              [routerLink]="link.url ? currentPath : null"
              [queryParams]="link.url ? { page: link.page } : null"
              [class]="linkClass(link)"
              [innerHTML]="link.label"
            ></a>
          }
        </div>
      </nav>
    </div>
  `,
})
export class PaginatorComponent {
  @Input() paginator!: Pagination
  @Input() currentPath = ''
  min = Math.min

  linkClass(link: any): string {
    const base = 'relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md'
    if (!link.url) return `${base} text-gray-400 cursor-not-allowed bg-gray-50 dark:bg-gray-800`
    if (link.active) return `${base} bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300`
    return `${base} text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50`
  }
}
```
