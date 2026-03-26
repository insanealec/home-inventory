import { Injectable, signal, inject } from '@angular/core'
import { HttpClient } from '@angular/common/http'
import { firstValueFrom } from 'rxjs'

@Injectable({ providedIn: 'root' })
export class TokenService {
  private http = inject(HttpClient)

  readonly tokens = signal<any[]>([])
  readonly newTokenName = signal('')

  async loadTokens(): Promise<void> {
    try {
      const data = await firstValueFrom(this.http.get<any[]>('/api/tokens'))
      this.tokens.set(data)
    } catch (error) {
      console.error('Failed to load tokens:', error)
    }
  }

  async storeToken(): Promise<void> {
    if (!this.newTokenName().trim()) return
    try {
      const data = await firstValueFrom(
        this.http.post<{ accessToken: any; plainTextToken: string }>(
          '/api/tokens',
          { name: this.newTokenName(), abilities: ['*'] }
        )
      )
      this.tokens.update(list => [...list, data.accessToken])
      // TODO: modal
      alert(`New Token Created: ${data.plainTextToken}`)
      this.newTokenName.set('')
    } catch (error) {
      console.error('Failed to create token:', error)
    }
  }

  async destroyToken(tokenId: string): Promise<void> {
    try {
      await firstValueFrom(this.http.delete(`/api/tokens/${tokenId}`))
      this.tokens.update(list => list.filter(t => t.id !== tokenId))
    } catch (error) {
      console.error('Failed to destroy token:', error)
    }
  }
}
