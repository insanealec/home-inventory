import { Injectable, signal, computed, inject } from '@angular/core'
import { HttpClient } from '@angular/common/http'
import { firstValueFrom } from 'rxjs'
import { User } from '../types/auth'

@Injectable({ providedIn: 'root' })
export class AuthService {
  private http = inject(HttpClient)

  readonly user = signal<User | null>(null)
  readonly isAuthenticated = computed(() => this.user() !== null)
  readonly errors = signal<Record<string, string[]>>({})

  setUser(authUser: User | null): void {
    this.user.set(authUser)
  }

  clearErrors(): void {
    this.errors.set({})
  }

  async login(credentials: { email: string; password: string }): Promise<void> {
    this.errors.set({})
    try {
      const response = await firstValueFrom(
        this.http.post('/login', credentials, { observe: 'response' })
      )
      if (response.status === 200) {
        window.location.href = '/dashboard'
      }
    } catch (error: any) {
      if (error.status === 422) {
        this.errors.set(error.error.errors ?? {})
      }
    }
  }

  async register(data: {
    name: string
    email: string
    password: string
    password_confirmation: string
  }): Promise<void> {
    this.errors.set({})
    try {
      const response = await firstValueFrom(
        this.http.post('/register', data, { observe: 'response' })
      )
      if (response.status === 201) {
        window.location.href = '/dashboard'
      }
    } catch (error: any) {
      if (error.status === 422) {
        this.errors.set(error.error.errors ?? {})
      }
    }
  }

  async logout(): Promise<void> {
    await firstValueFrom(this.http.post('/logout', {}))
    window.location.href = '/'
  }
}
