# Tailwind CSS v4

This project uses **Tailwind CSS v4**. v4 is CSS-first and has breaking changes from v3.

## Import Syntax (v4 only)

```css
/* v4 — correct */
@import "tailwindcss";

/* v3 — DO NOT USE */
@tailwind base;
@tailwind components;
@tailwind utilities;
```

## Configuration (CSS-first, no tailwind.config.js)

```css
@theme {
  --color-brand: oklch(0.72 0.11 178);
  --font-display: "Geist", sans-serif;
}
```

## Deprecated Utilities → Replacements

| Deprecated             | Replacement          |
|------------------------|----------------------|
| `bg-opacity-*`         | `bg-black/*`         |
| `text-opacity-*`       | `text-black/*`       |
| `border-opacity-*`     | `border-black/*`     |
| `flex-shrink-*`        | `shrink-*`           |
| `flex-grow-*`          | `grow-*`             |
| `overflow-ellipsis`    | `text-ellipsis`      |
| `decoration-slice`     | `box-decoration-slice` |

Note: `corePlugins` is not supported in v4.

## Spacing

Prefer `gap` over margins for spacing between siblings:

```html
<!-- Good -->
<div class="flex gap-8">
  <div>Item 1</div>
  <div>Item 2</div>
</div>
```

## Dark Mode

If existing components support dark mode, new ones must too — use the `dark:` variant:

```html
<div class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
  Content
</div>
```

## Common Layouts

```html
<!-- Flex row with space-between -->
<div class="flex items-center justify-between gap-4">

<!-- Responsive grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
```

## Common Pitfalls
- Using v3 `@tailwind` directives instead of `@import "tailwindcss"`
- Using deprecated utilities (`flex-shrink`, `bg-opacity`, etc.)
- Using `tailwind.config.js` — use CSS `@theme` instead
- Using margins for sibling spacing instead of `gap`
- Forgetting `dark:` variants when the rest of the project uses dark mode
