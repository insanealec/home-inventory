import { Component, Input, forwardRef } from '@angular/core'
import { CommonModule } from '@angular/common'
import { ControlValueAccessor, NG_VALUE_ACCESSOR, FormsModule } from '@angular/forms'

@Component({
  selector: 'app-form-input',
  standalone: true,
  imports: [CommonModule, FormsModule],
  providers: [
    {
      provide: NG_VALUE_ACCESSOR,
      useExisting: forwardRef(() => FormInputComponent),
      multi: true,
    },
  ],
  template: `
    <div>
      <label
        [for]="id"
        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
      >
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

  writeValue(val: any): void {
    this.value = val
  }

  registerOnChange(fn: any): void {
    this.onChange = fn
  }

  registerOnTouched(fn: any): void {
    this.onTouched = fn
  }
}
