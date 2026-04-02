import { Component, OnInit, inject } from '@angular/core'
import { CommonModule } from '@angular/common'
import { RouterOutlet } from '@angular/router'
import { AuthService } from './services/auth.service'
import { NavMainComponent } from './components/nav/nav-main.component'
import { NavGuestComponent } from './components/nav/nav-guest.component'
import { ToastComponent } from './components/common/toast.component'

declare const App: { user: any }

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [CommonModule, RouterOutlet, NavMainComponent, NavGuestComponent, ToastComponent],
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
      <app-toast />
    </div>
  `,
})
export class AppComponent implements OnInit {
  auth = inject(AuthService)

  ngOnInit(): void {
    this.auth.setUser(window?.App?.user ?? null)
  }
}
