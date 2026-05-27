# Search Component

A Material Design 3 (MD3) compliant search bar component built with Svelte and plain CSS using MD3 tokens.

## Features

- **MD3 Compliant**: Uses Material Design 3 color and spacing tokens
- **Flexible Icons**: Support for leading and trailing icon slots
- **Customizable**: Supports placeholder, supporting text, and custom callbacks
- **Accessible**: Proper ARIA labels and keyboard navigation
- **Responsive States**: Hover, focus, and active states with smooth transitions
- **Dark Mode**: Automatically adapts to light and dark color schemes

## Usage

### Basic Search Bar

```svelte
<script>
  import Search from './Search.svelte';
  
  let query = '';
</script>

<Search bind:value={query} />
```

### With Leading Icon

```svelte
<script>
  import Search from './Search.svelte';
  import IconSearch from '@ktibow/iconset-material-symbols/search';
  
  let query = '';
</script>

<Search 
  bind:value={query}
  leadingIcon={IconSearch}
/>
```

### With Leading and Trailing Icons

```svelte
<script>
  import Search from './Search.svelte';
  import IconSearch from '@ktibow/iconset-material-symbols/search';
  import IconClose from '@ktibow/iconset-material-symbols/close';
  
  let query = '';
  
  function clearSearch() {
    query = '';
  }
</script>

<Search 
  bind:value={query}
  leadingIcon={IconSearch}
  trailingIcon={query ? IconClose : undefined}
  onTrailingIconClick={clearSearch}
/>
```

### With Supporting Text

```svelte
<Search 
  placeholder="Search products"
  supportingText="Try searching for 'electronics' or 'clothing'"
/>
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | `string` | `"Search"` | Aria label and default placeholder |
| `placeholder` | `string` | `label` | Input placeholder text |
| `value` | `string` | `""` | The search query (use `bind:value`) |
| `leadingIcon` | `IconifyIcon` | `undefined` | Leading icon from Material Symbols |
| `trailingIcon` | `IconifyIcon` | `undefined` | Trailing icon from Material Symbols |
| `supportingText` | `string` | `undefined` | Helper text displayed below input |
| `onLeadingIconClick` | `() => void` | `undefined` | Callback for leading icon click |
| `onTrailingIconClick` | `() => void` | `undefined` | Callback for trailing icon click |

## CSS Variables

The component uses the following MD3 CSS variables (customizable):

```css
--m3-search-container-height: 3.5rem;
--m3-search-container-shape: var(--m3-shape-medium);
--m3-search-icon-size: 1.5rem;
--m3-search-leading-space: 1rem;
--m3-search-trailing-space: 1rem;
--m3-search-icon-label-gap: 1rem;
```

## Colors

The component uses the following MD3 color tokens:

- **Background**: `--m3c-surface-container-high`
- **Text**: `--m3c-on-surface`
- **Placeholder**: `--m3c-on-surface-variant`
- **Icons**: `--m3c-on-surface` (leading), `--m3c-on-surface-variant` (trailing)
- **Focus Border**: `--m3c-secondary`
- **Focus Shadow**: `rgba(99, 92, 113, 0.12)` / `rgba(204, 194, 219, 0.12)` (dark)

## States

### Default
- Background: `surface-container-high`
- Text: `on-surface`

### Hover
- Background: Tinted with 8% `on-surface`

### Focused
- Border: 2px `secondary` color
- Shadow: 3px focus ring

### Active/Pressed
- Icon background opacity: 10%

## Spacing

Based on MD3 specifications:

- **Container Height**: 56dp (3.5rem)
- **Horizontal Padding**: 16dp (1rem)
- **Icon Size**: 24dp (1.5rem)
- **Icon-Label Gap**: 16dp (1rem)

## Typography

- **Font**: Roboto (via `--m3-font`)
- **Size**: 16pt (1rem)
- **Weight**: 400 (regular)
- **Line Height**: 24pt (1.5rem)
- **Letter Spacing**: 0.5px

## Accessibility

- Proper `aria-label` attributes on icon buttons
- Icon buttons use `tabindex="-1"` to prevent keyboard focus
- Input field maintains focus management
- Supports keyboard navigation
- Proper color contrast ratios per MD3

## Browser Support

Works with all modern browsers that support:
- CSS custom properties (variables)
- CSS `light-dark()` function for theme switching
- `color-mix()` for computed colors
- Svelte 5+ syntax

## Example Integration

See `SearchExample.svelte` for a complete example with multiple search bar variations.
