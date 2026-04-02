import { HttpInterceptorFn, HttpErrorResponse } from '@angular/common/http'
import { inject } from '@angular/core'
import { Router } from '@angular/router'
import { catchError, throwError } from 'rxjs'
import { ToastService } from './toast.service'

export const errorInterceptor: HttpInterceptorFn = (req, next) => {
  const router = inject(Router)
  const toast = inject(ToastService)

  return next(req).pipe(
    catchError((error: HttpErrorResponse) => {
      if (error.status === 401) {
        router.navigate(['/login'])
      } else if (error.status === 422) {
        // Validation errors — let individual services/components handle these
      } else if (error.status === 0) {
        // Network error / server unreachable
        toast.error('Network error. Please check your connection and try again.')
      } else if (error.status >= 500) {
        toast.error('Something went wrong on our end. Please try again later.')
      } else if (error.status === 403) {
        toast.error('You do not have permission to perform that action.')
      } else if (error.status === 404) {
        toast.error('The requested resource was not found.')
      } else if (error.status === 429) {
        toast.warning('Too many requests. Please slow down and try again.')
      }

      return throwError(() => error)
    }),
  )
}
