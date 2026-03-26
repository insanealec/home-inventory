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
