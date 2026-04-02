import { Component, inject } from '@angular/core'
import { CommonModule } from '@angular/common'
import { ToastService, Toast } from '../../services/toast.service'

@Component({
  selector: 'app-toast',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="fixed bottom-4 right-4 z-50 flex flex-col gap-2 w-full max-w-sm">
      @for (toast of toastService.toasts(); track toast.id) {
        <div
          class="flex items-start gap-3 rounded-lg px-4 py-3 shadow-lg text-sm font-medium"
          [class]="toastClass(toast)"
        >
          <span class="flex-1">{{ toast.message }}</span>
          <button
            type="button"
            (click)="toastService.dismiss(toast.id)"
            class="shrink-0 opacity-70 hover:opacity-100"
          >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      }
    </div>
  `,
})
export class ToastComponent {
  toastService = inject(ToastService)

  toastClass(toast: Toast): string {
    switch (toast.type) {
      case 'error':   return 'bg-red-600 text-white'
      case 'warning': return 'bg-yellow-500 text-white'
      case 'success': return 'bg-green-600 text-white'
    }
  }
}
