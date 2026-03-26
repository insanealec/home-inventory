import { Component } from '@angular/core'

@Component({
  selector: 'app-content',
  standalone: true,
  template: `
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
        <ng-content />
      </div>
    </div>
  `,
})
export class ContentComponent {}
