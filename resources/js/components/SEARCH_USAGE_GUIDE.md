# Search Component - Complete Usage Guide

## Quick Start

Import the component:
```svelte
<script>
  import Search from '@/components/Search.svelte';
</script>

<Search />
```

## Feature Overview

### 1. Basic Search

Simplest form - just a search input with placeholder:

```svelte
<Search placeholder="Search..." />
```

**Resulting in:**
- Input field with surface-container-high background
- Text input with on-surface color
- on-surface-variant placeholder text
- 56dp height with medium corner radius
- Focus ring with secondary color on focus

### 2. With Icons

Add Material Symbols icons from the Iconify ecosystem:

```svelte
<script>
  import Search from '@/components/Search.svelte';
  import IconSearch from '@ktibow/iconset-material-symbols/search';
  import IconClose from '@ktibow/iconset-material-symbols/close';
  
  let query = '';
  
  function clear() {
    query = '';
  }
</script>

<Search 
  bind:value={query}
  leadingIcon={IconSearch}
  trailingIcon={query ? IconClose : undefined}
  onTrailingIconClick={clear}
/>
```

**Features:**
- Leading icon (typically search icon)
- Trailing icon (conditionally shown - only when text entered)
- Click handlers for each icon
- 24dp icon size
- Icon buttons with hover/active states

### 3. With Supporting Text

Add helper text below the input:

```svelte
<Search 
  placeholder="Search recipes"
  supportingText="Try 'pasta', 'chicken', or 'desserts'"
/>
```

**Features:**
- Helper text in on-surface-variant color
- Smaller font than input (12pt)
- Provides context or suggestions

### 4. Full Featured

Complete example with all features:

```svelte
<script>
  import Search from '@/components/Search.svelte';
  import IconSearch from '@ktibow/iconset-material-symbols/search';
  import IconClose from '@ktibow/iconset-material-symbols/close';
  import IconSettings from '@ktibow/iconset-material-symbols/settings';
  
  let searchQuery = '';
  let isLoading = false;
  
  function handleSearch() {
    isLoading = true;
    // Simulate search
    setTimeout(() => { isLoading = false; }, 1000);
  }
  
  function clearSearch() {
    searchQuery = '';
  }
  
  function openFilters() {
    // Open filters modal
  }
</script>

<Search 
  bind:value={searchQuery}
  placeholder="Search items..."
  label="Item Search"
  supportingText="Type to search in title and description"
  leadingIcon={IconSearch}
  trailingIcon={searchQuery ? IconClose : undefined}
  onTrailingIconClick={clearSearch}
/>
```

## API Reference

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `value` | `string` | `""` | Bound search query - use `bind:value` |
| `label` | `string` | `"Search"` | ARIA label and default placeholder |
| `placeholder` | `string` | `label` | Input placeholder text |
| `supportingText` | `string` | `undefined` | Helper text below input |
| `leadingIcon` | `IconifyIcon` | `undefined` | Leading icon from Material Symbols |
| `trailingIcon` | `IconifyIcon` | `undefined` | Trailing icon from Material Symbols |
| `onLeadingIconClick` | `() => void` | `undefined` | Leading icon click handler |
| `onTrailingIconClick` | `() => void` | `undefined` | Trailing icon click handler |
| Plus all standard HTML input attributes (`disabled`, `required`, `readonly`, etc.) |

### Reactive State

```svelte
<script>
  let searchValue = '';
  
  $effect(() => {
    console.log('Search changed:', searchValue);
    // Perform search logic
  });
</script>

<Search bind:value={searchValue} />
```

## Styling & Customization

### Override CSS Variables

```svelte
<style>
  :root {
    --m3-search-container-height: 3rem;
    --m3-search-icon-size: 1.25rem;
    --m3-search-leading-space: 0.75rem;
  }
</style>

<Search />
```

### Custom Class

The component accepts a `class` prop for additional styling:

```svelte
<Search class="my-custom-search" />

<style>
  :global(.my-custom-search) {
    /* Custom styles */
  }
</style>
```

### Override Colors

For theme variants, override M3 color tokens:

```svelte
<style>
  :root {
    --m3c-surface-container-high: #f5f5f5;
    --m3c-on-surface: #1a1a1a;
  }
</style>
```

## Interactive Behaviors

### Search as You Type

```svelte
<script>
  import Search from '@/components/Search.svelte';
  
  let results = [];
  let query = '';
  
  $effect(() => {
    if (query) {
      // Fetch results as user types
      fetchResults(query).then(r => results = r);
    } else {
      results = [];
    }
  });
</script>

<Search bind:value={query} />
<ResultsList {results} />
```

### Debounced Search

```svelte
<script>
  import { debounce } from '@/lib/utils';
  
  let query = '';
  const performSearch = debounce(async (q) => {
    const results = await api.search(q);
    // handle results
  }, 300);
  
  $effect(() => {
    performSearch(query);
  });
</script>

<Search bind:value={query} />
```

### Search with History

```svelte
<script>
  let query = '';
  let searchHistory = [];
  
  function handleSearch() {
    if (query && !searchHistory.includes(query)) {
      searchHistory = [query, ...searchHistory.slice(0, 4)];
      localStorage.setItem('searchHistory', JSON.stringify(searchHistory));
    }
  }
</script>

<Search bind:value={query} on:submit={handleSearch} />
```

## Accessibility Considerations

 **Semantic Input**: Uses proper `<input>` element  
 **ARIA Labels**: Icon buttons have `aria-label`  
 **Focus Management**: Proper tab order, icon buttons skip tabbing  
 **Color Contrast**: All text meets WCAG AA standards  
 **Focus Indicator**: 3px visible focus ring  
 **Keyboard Navigation**: Full keyboard support  

## Examples in Context

### Navigation Search

```svelte
<header>
  <Search 
    placeholder="Search..." 
    leadingIcon={searchIcon}
  />
</header>
```

### Form Search

```svelte
<form>
  <Search 
    bind:value={formData.query}
    label="Product Search"
    supportingText="Search by name or SKU"
  />
  <button type="submit">Search</button>
</form>
```

### Filtered List

```svelte
<div>
  <Search 
    bind:value={filterText}
    placeholder="Filter items..."
  />
  <ItemList items={items.filter(item => 
    item.name.includes(filterText)
  )} />
</div>
```

## Common Patterns

### Clear on Escape

```svelte
<script>
  let query = '';
  
  function handleKeydown(e) {
    if (e.key === 'Escape') {
      query = '';
    }
  }
</script>

<Search 
  bind:value={query}
  onkeydown={handleKeydown}
/>
```

### Submit on Enter

```svelte
<script>
  function handleKeydown(e) {
    if (e.key === 'Enter') {
      performSearch(e.currentTarget.value);
    }
  }
</script>

<Search onkeydown={handleKeydown} />
```

### Real-time Validation

```svelte
<script>
  let query = '';
  let error = '';
  
  $effect(() => {
    if (query && query.length < 2) {
      error = 'Search query must be at least 2 characters';
    } else {
      error = '';
    }
  });
</script>

<Search 
  bind:value={query}
  supportingText={error || 'Type to search...'}
/>
```

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+
- Requires CSS custom properties support
