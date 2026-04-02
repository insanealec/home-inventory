import { Injectable, signal } from '@angular/core'

export type ToastType = 'error' | 'warning' | 'success'

export interface Toast {
  id: number
  type: ToastType
  message: string
}

let nextId = 0

@Injectable({ providedIn: 'root' })
export class ToastService {
  readonly toasts = signal<Toast[]>([])

  error(message: string, duration = 6000): void {
    this.add('error', message, duration)
  }

  warning(message: string, duration = 5000): void {
    this.add('warning', message, duration)
  }

  success(message: string, duration = 3000): void {
    this.add('success', message, duration)
  }

  dismiss(id: number): void {
    this.toasts.update(list => list.filter(t => t.id !== id))
  }

  private add(type: ToastType, message: string, duration: number): void {
    const id = ++nextId
    this.toasts.update(list => [...list, { id, type, message }])
    setTimeout(() => this.dismiss(id), duration)
  }
}
