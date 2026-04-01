import { Component, Input, Output, EventEmitter } from '@angular/core'

@Component({
  selector: 'app-confirm-modal',
  standalone: true,
  template: `
    @if (isOpen) {
      <div
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
        (click)="cancelled.emit()"
      >
        <div
          class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md"
          (click)="$event.stopPropagation()"
        >
          <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ title }}</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ message }}</p>

            <div class="mt-6 flex justify-end gap-3">
              <button
                type="button"
                (click)="cancelled.emit()"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
              >
                Cancel
              </button>
              <button
                type="button"
                (click)="confirmed.emit()"
                [class]="danger
                  ? 'px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2'
                  : 'px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2'"
              >
                {{ confirmLabel }}
              </button>
            </div>
          </div>
        </div>
      </div>
    }
  `,
})
export class ConfirmModalComponent {
  @Input() isOpen = false
  @Input() title = 'Are you sure?'
  @Input() message = ''
  @Input() confirmLabel = 'Confirm'
  @Input() danger = false
  @Output() confirmed = new EventEmitter<void>()
  @Output() cancelled = new EventEmitter<void>()
}
