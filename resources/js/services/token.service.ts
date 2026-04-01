import { Injectable, signal, inject } from '@angular/core'
import { HttpClient } from '@angular/common/http'
import { firstValueFrom } from 'rxjs'
import { Token } from '../types/auth'

@Injectable({ providedIn: 'root' })
export class TokenService {
  private http = inject(HttpClient)

  readonly tokens = signal<Token[]>([])

  async loadTokens(): Promise<void> {
    try {
      const data = await firstValueFrom(this.http.get<Token[]>('/api/tokens'))
      this.tokens.set(data)
    } catch (error) {
      console.error('Failed to load tokens:', error)
    }
  }

  /** Returns the plain text token on success, or null on failure. */
  async storeToken(name: string): Promise<string | null> {
    if (!name.trim()) return null
    try {
      const data = await firstValueFrom(
        this.http.post<{ accessToken: Token; plainTextToken: string }>(
          '/api/tokens',
          { name, abilities: ['*'] }
        )
      )
      this.tokens.update(list => [...list, data.accessToken])
      return data.plainTextToken
    } catch (error) {
      console.error('Failed to create token:', error)
      return null
    }
  }

  async destroyToken(tokenId: number): Promise<void> {
    try {
      await firstValueFrom(this.http.delete(`/api/tokens/${tokenId}`))
      this.tokens.update(list => list.filter(t => t.id !== tokenId))
    } catch (error) {
      console.error('Failed to destroy token:', error)
    }
  }
}
