import 'zone.js'
import '@angular/compiler'
import './bootstrap'
import '../css/app.css'
import { bootstrapApplication } from '@angular/platform-browser'
import { provideRouter } from '@angular/router'
import { provideHttpClient, withInterceptors, withXsrfConfiguration } from '@angular/common/http'
import { AppComponent } from './app.component'
import { routes } from './app.routes'
import { csrfInterceptor } from './services/csrf.interceptor'

bootstrapApplication(AppComponent, {
  providers: [
    provideRouter(routes),
    provideHttpClient(
      withInterceptors([csrfInterceptor]),
      withXsrfConfiguration({
        cookieName: 'XSRF-TOKEN',
        headerName: 'X-XSRF-TOKEN',
      }),
    ),
  ],
}).catch(console.error)
