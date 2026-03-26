import { Component, inject, OnInit } from '@angular/core'
import { CommonModule } from '@angular/common'
import { FormsModule } from '@angular/forms'
import { signal } from '@angular/core'
import { AuthService } from '../../services/auth.service'

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, FormsModule],
  template: `
    <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900">
      <div class="bg-white p-8 rounded shadow-md w-full max-w-md dark:bg-gray-800">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">
          Register
        </h2>
        <form (ngSubmit)="submit()" class="space-y-4">
          <div>
            <label
              for="name"
              class="block text-gray-700 dark:text-gray-300 mb-1"
            >
              Name
            </label>
            <input
              [ngModel]="name()"
              (ngModelChange)="name.set($event)"
              type="text"
              id="name"
              name="name"
              class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              required
            />
            @if (authService.errors()?.['name']) {
              @for (error of authService.errors()['name']; track $index) {
                <div class="text-red-500 text-sm mt-1">
                  {{ error }}
                </div>
              }
            }
          </div>
          <div>
            <label
              for="email"
              class="block text-gray-700 dark:text-gray-300 mb-1"
            >
              Email
            </label>
            <input
              [ngModel]="email()"
              (ngModelChange)="email.set($event)"
              type="email"
              id="email"
              name="email"
              class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              required
            />
            @if (authService.errors()?.['email']) {
              @for (error of authService.errors()['email']; track $index) {
                <div class="text-red-500 text-sm mt-1">
                  {{ error }}
                </div>
              }
            }
          </div>
          <div>
            <label
              for="password"
              class="block text-gray-700 dark:text-gray-300 mb-1"
            >
              Password
            </label>
            <input
              [ngModel]="password()"
              (ngModelChange)="password.set($event)"
              type="password"
              id="password"
              name="password"
              class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              required
            />
            @if (authService.errors()?.['password']) {
              @for (error of authService.errors()['password']; track $index) {
                <div class="text-red-500 text-sm mt-1">
                  {{ error }}
                </div>
              }
            }
          </div>
          <div>
            <label
              for="password_confirmation"
              class="block text-gray-700 dark:text-gray-300 mb-1"
            >
              Confirm Password
            </label>
            <input
              [ngModel]="passwordConfirmation()"
              (ngModelChange)="passwordConfirmation.set($event)"
              type="password"
              id="password_confirmation"
              name="password_confirmation"
              class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              required
            />
            @if (authService.errors()?.['password_confirmation']) {
              @for (error of authService.errors()['password_confirmation']; track $index) {
                <div class="text-red-500 text-sm mt-1">
                  {{ error }}
                </div>
              }
            }
          </div>
          <button
            type="submit"
            class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition dark:hover:bg-blue-800"
          >
            Register
          </button>
        </form>
      </div>
    </div>
  `,
})
export class RegisterComponent implements OnInit {
  authService = inject(AuthService)
  name = signal('')
  email = signal('')
  password = signal('')
  passwordConfirmation = signal('')

  ngOnInit(): void {
    this.authService.clearErrors()
  }

  submit(): void {
    this.authService.register({
      name: this.name(),
      email: this.email(),
      password: this.password(),
      password_confirmation: this.passwordConfirmation(),
    })
  }
}
