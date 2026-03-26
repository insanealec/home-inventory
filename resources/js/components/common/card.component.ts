import { Component } from '@angular/core'

@Component({
  selector: 'app-card',
  standalone: true,
  template: `
    <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
        <ng-content select="[slot=title]" />
      </h1>
      <ng-content />
    </div>
  `,
})
export class CardComponent {}
