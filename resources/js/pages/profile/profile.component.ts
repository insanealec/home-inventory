import { Component, OnInit, inject, signal } from '@angular/core'
import { CommonModule } from '@angular/common'
import { FormsModule } from '@angular/forms'
import { TokenService } from '../../services/token.service'
import { Token } from '../../types/auth'
import { ConfirmModalComponent } from '../../components/common/confirm-modal.component'
import { ModalComponent } from '../../components/common/modal.component'
import { ContentComponent } from '../../components/common/content.component'

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [CommonModule, FormsModule, ConfirmModalComponent, ModalComponent, ContentComponent],
  template: `
    <app-content>
    <div class="mx-auto max-w-2xl px-4 py-8">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Profile</h1>

      <!-- API Tokens -->
      <section class="mt-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">API Tokens</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
          Tokens grant full access to your account via the API and MCP server. Treat them like
          passwords — store them somewhere safe and never share them.
        </p>

        <!-- Create token form -->
        <div class="mt-4 flex gap-3">
          <input
            type="text"
            [(ngModel)]="tokenName"
            placeholder="Token name"
            class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
            (keyup.enter)="createToken()"
          />
          <button
            type="button"
            (click)="createToken()"
            [disabled]="!tokenName.trim()"
            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Create token
          </button>
        </div>

        <!-- Token list -->
        <div class="mt-4 space-y-2">
          @if (tokenService.tokens().length === 0) {
            <p class="text-sm text-gray-500 dark:text-gray-400">No tokens yet.</p>
          }
          @for (token of tokenService.tokens(); track token.id) {
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3 dark:border-gray-700 dark:bg-gray-800">
              <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ token.name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                  Last used:
                  @if (token.last_used_at) {
                    {{ token.last_used_at | date: 'mediumDate' }}
                  } @else {
                    Never
                  }
                </p>
              </div>
              <button
                type="button"
                (click)="promptDelete(token)"
                class="text-sm font-medium text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
              >
                Revoke
              </button>
            </div>
          }
        </div>
      </section>
    </div>
    </app-content>

    <!-- New token display modal -->
    <app-modal
      [isOpen]="!!newPlainTextToken()"
      title="Token created"
      (close)="newPlainTextToken.set(null)"
    >
      <p class="text-sm text-gray-600 dark:text-gray-400">
        Copy this token now — it won't be shown again.
      </p>
      <div class="mt-3 flex items-center gap-2 rounded-md bg-gray-100 dark:bg-gray-700 px-4 py-3">
        <code class="flex-1 break-all text-sm text-gray-900 dark:text-white">
          {{ newPlainTextToken() }}
        </code>
        <button
          type="button"
          (click)="copyToken()"
          class="shrink-0 text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400"
        >
          {{ copied() ? 'Copied!' : 'Copy' }}
        </button>
      </div>
      <div class="mt-4 flex justify-end">
        <button
          type="button"
          (click)="newPlainTextToken.set(null)"
          class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700"
        >
          Done
        </button>
      </div>
    </app-modal>

    <!-- Delete confirmation modal -->
    <app-confirm-modal
      [isOpen]="!!tokenPendingDelete()"
      title="Revoke token"
      [message]="deleteMessage()"
      confirmLabel="Revoke"
      [danger]="true"
      (confirmed)="confirmDelete()"
      (cancelled)="tokenPendingDelete.set(null)"
    />
  `,
})
export class ProfileComponent implements OnInit {
  tokenService = inject(TokenService)

  tokenName = ''
  newPlainTextToken = signal<string | null>(null)
  tokenPendingDelete = signal<Token | null>(null)
  copied = signal(false)

  deleteMessage(): string {
    const name = this.tokenPendingDelete()?.name ?? ''
    return `Revoke '${name}'? Any apps using it will lose access immediately.`
  }

  ngOnInit(): void {
    this.tokenService.loadTokens()
  }

  async createToken(): Promise<void> {
    const plainText = await this.tokenService.storeToken(this.tokenName)
    this.tokenName = ''
    if (plainText) {
      this.newPlainTextToken.set(plainText)
    }
  }

  promptDelete(token: Token): void {
    this.tokenPendingDelete.set(token)
  }

  async confirmDelete(): Promise<void> {
    const token = this.tokenPendingDelete()
    if (!token) return
    this.tokenPendingDelete.set(null)
    await this.tokenService.destroyToken(token.id)
  }

  async copyToken(): Promise<void> {
    const token = this.newPlainTextToken()
    if (!token) return
    await navigator.clipboard.writeText(token)
    this.copied.set(true)
    setTimeout(() => this.copied.set(false), 2000)
  }
}
