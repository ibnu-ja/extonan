# Material Design 3 Search Component

A production-ready MD3 search bar component built with Svelte and plain CSS, using Material Design 3 tokens and specifications.

## 📁 Files Included

- **`Search.svelte`** - Main component (204 lines)
- **`SearchExample.svelte`** - Usage examples and demos
- **`SEARCH_COMPONENT.md`** - Component documentation
- **`SEARCH_MD3_MAPPING.md`** - MD3 token specifications mapping
- **`SEARCH_USAGE_GUIDE.md`** - Complete usage guide with patterns
- **`README_SEARCH.md`** - This file

## ✨ Features

- ✅ **Material Design 3 Compliant** - Implements official MD3 specifications
- ✅ **Flexible Icons** - Support for leading/trailing icon slots
- ✅ **Focus States** - Secondary color focus indicator (3px)
- ✅ **Hover States** - 8% on-surface tinted background
- ✅ **Pressed States** - 10% on-surface background on icons
- ✅ **Supporting Text** - Optional helper text below input
- ✅ **Dark Mode** - Automatic light/dark theme adaptation
- ✅ **Accessible** - ARIA labels, semantic HTML, keyboard navigation
- ✅ **Customizable** - CSS variables for sizing and spacing
- ✅ **Responsive** - Works on all screen sizes

## 🚀 Quick Start

```svelte
<script>
  import Search from '@/components/Search.svelte';
</script>

<Search placeholder="Search items..." />
```

## 📦 Installation

The component is already in your project at:
```
resources/js/components/Search.svelte
```

Import and use immediately with no additional setup required.

## 🎯 Common Use Cases

### 1. Navigation Search
```svelte
<Search 
  placeholder="Search..."
  leadingIcon={() => <Icon icon={searchIcon} />}
/>
```

### 2. Product Search with Clear
```svelte
<script>
  let query = '';
</script>

<Search 
  bind:value={query}
  trailingIcon={() => query ? <Icon icon={closeIcon} /> : null}
  onTrailingIconClick={() => query = ''}
/>
```

### 3. Form Search
```svelte
<Search 
  bind:value={formData.search}
  label="Product Search"
  supportingText="Search by name or SKU"
/>
```

## 🎨 Design Specifications

### Dimensions
- **Height**: 56dp (3.5rem)
- **Icon Size**: 24dp (1.5rem)
- **Horizontal Padding**: 16dp (1rem)
- **Corner Radius**: medium (8dp)

### Colors (MD3 Tokens)
- **Background**: `surface-container-high`
- **Text**: `on-surface`
- **Placeholder**: `on-surface-variant`
- **Icons**: `on-surface` (leading), `on-surface-variant` (trailing)
- **Focus**: `secondary` with 3px shadow

### Typography
- **Font**: Roboto
- **Size**: 16pt
- **Weight**: 400
- **Line Height**: 24pt
- **Letter Spacing**: 0.5px

## 📚 Documentation

### For Quick Usage
 See `SEARCH_USAGE_GUIDE.md` for patterns and examples

### For Component Details
 See `SEARCH_COMPONENT.md` for props, events, and styling

### For MD3 Compliance
 See `SEARCH_MD3_MAPPING.md` for token-to-spec mapping

### For Examples
 See `SearchExample.svelte` for implementation examples

## 🔧 Customization

### Change Container Size
```css
:root {
  --m3-search-container-height: 3rem;
}
```

### Change Icon Size
```css
:root {
  --m3-search-icon-size: 1.25rem;
}
```

### Change Spacing
```css
:root {
  --m3-search-leading-space: 0.75rem;
  --m3-search-trailing-space: 0.75rem;
}
```

## ♿ Accessibility

 Semantic HTML input element  
 ARIA labels on all buttons  
 Proper focus management  
 3px focus indicator with secondary color  
 WCAG AA color contrast  
 Full keyboard navigation  
 Proper icon button tab skip (`tabindex="-1"`)

## 🌓 Dark Mode

Component automatically adapts to dark theme using:
- `light-dark()` CSS function for colors
- `@media (prefers-color-scheme: dark)` for dark-specific styles
- M3 color tokens that include dark variants

## 📱 Responsive

Component works seamlessly across:
- Mobile phones (small)
- Tablets (medium)
- Desktops (large)
- Uses standard viewport units (rem)

## 🔄 State Management

### With Svelte 5 Runes
```svelte
<script>
  let query = $state('');
</script>

<Search bind:value={query} />
```

### With Effects
```svelte
<script>
  let query = '';
  let results = $state([]);
  
  $effect(() => {
    // React to query changes
    if (query) {
      results = performSearch(query);
    }
  });
</script>

<Search bind:value={query} />
```

## 🎯 Integration Examples

### With M3 Svelte Button
```svelte
<div style="display: flex; gap: 1rem;">
  <Search bind:value={query} />
  <Button>Search</Button>
</div>
```

### In a List Filter
```svelte
<Search 
  bind:value={filterText}
  placeholder="Filter items..."
/>
<ItemList items={filteredItems} />
```

### In a Form
```svelte
<form>
  <div>
    <Search 
      bind:value={formData.query}
      label="Product Search"
    />
  </div>
  <Button type="submit">Submit</Button>
</form>
```

## 📋 Component Props

| Prop | Type | Default | Purpose |
|------|------|---------|---------|
| `value` | `string` | `""` | Search query (use `bind:value`) |
| `label` | `string` | `"Search"` | ARIA label & default placeholder |
| `placeholder` | `string` | `label` | Input placeholder text |
| `supportingText` | `string` | - | Helper text below input |
| `leadingIcon` | `Snippet` | - | Leading icon slot |
| `trailingIcon` | `Snippet` | - | Trailing icon slot |
| `onLeadingIconClick` | `() => void` | - | Leading icon handler |
| `onTrailingIconClick` | `() => void` | - | Trailing icon handler |
| `...inputAttrs` | `InputHTMLAttributes` | - | All standard input attributes |

## 🛠️ Events & Handlers

```svelte
<Search 
  onkeydown={(e) => { /* handle keydown */ }}
  onfocus={(e) => { /* handle focus */ }}
  onblur={(e) => { /* handle blur */ }}
  on:input={(e) => { /* handle input */ }}
/>
```

## 💾 Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## 📝 License

Part of the application's component system. Uses M3 tokens from Material Design 3.

## 🔗 References

- **Material Design 3 Search Bar**: https://m3.material.io/components/search/overview
- **M3 Svelte**: https://m3-svelte.vercel.app/
- **Iconify Material Symbols**: https://icon-sets.iconify.design/material-symbols/

---

**Version**: 1.0.0  
**Status**: Production Ready  
**Last Updated**: 2026-03-14
