import { Component, Input } from '@angular/core'
import { RouterLink } from '@angular/router'
import { CommonModule } from '@angular/common'
import { Pagination } from '../../types/common'

@Component({
  selector: 'app-paginator',
  standalone: true,
  imports: [CommonModule, RouterLink],
  template: `
    <div class="mt-4">
      <nav
        class="flex items-center justify-between px-4 py-3 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 sm:px-6"
        aria-label="Pagination"
      >
        <div class="hidden sm:block">
          <p class="text-sm text-gray-700 dark:text-gray-300">
            Showing
            <span class="font-medium">{{ (paginator.current_page - 1) * paginator.per_page + 1 }}</span>
            to
            <span class="font-medium">{{ min(paginator.current_page * paginator.per_page, paginator.total) }}</span>
            of <span class="font-medium">{{ paginator.total }}</span> results
          </p>
        </div>
        <div class="flex-1 flex justify-between sm:justify-end">
          @for (link of paginator.links; track link.label) {
            <a
              [routerLink]="link.url ? currentPath : null"
              [queryParams]="link.url ? { page: link.page } : null"
              [ngClass]="linkClass(link)"
              [innerHTML]="link.label"
            ></a>
          }
        </div>
      </nav>
    </div>
  `,
})
export class PaginatorComponent {
  @Input() paginator!: Pagination
  @Input() currentPath = ''

  min = Math.min

  linkClass(link: any): string {
    const base =
      'relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md'
    if (!link.url)
      return `${base} text-gray-400 cursor-not-allowed bg-gray-50 dark:bg-gray-800`
    if (link.active)
      return `${base} bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300`
    return `${base} text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700`
  }
}
