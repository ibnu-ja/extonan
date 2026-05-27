# Search Component - File Index & Quick Navigation

## 🎯 Start Here

**New to the Search component?** Start with:
1. [`README_SEARCH.md`](README_SEARCH.md) - Overview and quick start (5 min read)
2. [`SearchExample.svelte`](SearchExample.svelte) - See 5 working examples
3. Copy an example and modify for your use case

## 📁 File Structure

```
resources/js/components/
 Search.svelte ......................... Main component (use this!)
 SearchExample.svelte .................. Example implementations
 README_SEARCH.md ...................... START HERE - Overview
 SEARCH_COMPONENT.md ................... Detailed component docs
 SEARCH_MD3_MAPPING.md ................. MD3 token mapping
 SEARCH_USAGE_GUIDE.md ................. Complete patterns & examples
 SEARCH_INDEX.md ....................... This file
```

## 📖 Documentation Guide

### By Use Case

**I want to...**

- **See what it looks like** 
  → [`SearchExample.svelte`](SearchExample.svelte)

- **Use it in my app right now**
  → [`README_SEARCH.md`](README_SEARCH.md) + [`SEARCH_USAGE_GUIDE.md`](SEARCH_USAGE_GUIDE.md)

- **Understand all the props**
  → [`SEARCH_COMPONENT.md`](SEARCH_COMPONENT.md)

- **Know how it meets MD3 specs**
  → [`SEARCH_MD3_MAPPING.md`](SEARCH_MD3_MAPPING.md)

- **Find a specific pattern**
  → [`SEARCH_USAGE_GUIDE.md`](SEARCH_USAGE_GUIDE.md) (search for "Real-time", "Debounced", "History", etc.)

- **Customize the styling**
  → [`SEARCH_COMPONENT.md`](SEARCH_COMPONENT.md) → "Styling & Customization" section

- **Understand the accessibility**
  → [`SEARCH_MD3_MAPPING.md`](SEARCH_MD3_MAPPING.md) → "Accessibility" section

### By Document

#### `Search.svelte` (204 lines)
**The actual component - this is what you import**

```svelte
import Search from '@/components/Search.svelte';
```

Key sections:
- `<script>` - Component logic with TypeScript
- Template - Flexible icon slots, input field
- `<style>` - M3-compliant styling with CSS variables

#### `SearchExample.svelte` (65 lines)
**5 Working examples you can copy and adapt**

Examples include:
1. Basic search bar
2. With leading icon
3. With leading + trailing icons
4. With supporting text
5. Custom placeholder

#### `README_SEARCH.md` (220 lines)
**Quick reference and overview**

Covers:
- ✨ Features overview
- 🚀 Quick start
- 🎯 Common use cases (3 examples)
- 🎨 Design specifications
- 🔧 Customization (size, icons, spacing)
- ♿ Accessibility summary
- 🌓 Dark mode info
- 📱 Responsive behavior
- 📋 Props reference

**Best for:** Getting started, understanding capabilities, finding prop names

#### `SEARCH_COMPONENT.md` (170 lines)
**Detailed component API**

Covers:
- 💻 Features list
- 📋 Props table
- 🔄 CSS variables
- 🎨 Colors by state
- 📏 Spacing details
- 🔤 Typography
- ♿ Accessibility checklist
- 🌈 Dark mode variants
- 🏗️ Component architecture

**Best for:** Looking up specific props, understanding states, customizing styling

#### `SEARCH_MD3_MAPPING.md` (180 lines)
**Material Design 3 compliance**

Covers:
- 🎨 Color token mapping
- 📏 Layout & spacing mapping
- 🔤 Typography mapping
- 🏗️ Component architecture
- 🌓 Dark mode implementation
- ♿ Accessibility details
- 🔧 Customization options

**Best for:** Verifying MD3 compliance, understanding token usage, dark mode details

#### `SEARCH_USAGE_GUIDE.md` (280 lines)
**Complete patterns and recipes**

Covers:
- 🚀 Quick start
- 💡 4 feature levels (basic → full featured)
- 📚 API reference
- 🎨 Styling examples
- 🔄 Interactive behaviors (search as you type, debounced, history)
- ♿ Accessibility info
- 🎯 5 context examples (navigation, form, filtered list, etc.)
- 🔧 5 common patterns (escape to clear, enter to submit, validation, etc.)

**Best for:** Finding patterns, implementing specific behaviors, real-world examples

#### `SEARCH_INDEX.md` (this file)
**Navigation guide**

## 🚀 Common Tasks

### Task: Add search to my page
1. Read: [`README_SEARCH.md`](README_SEARCH.md) quick start section
2. View: [`SearchExample.svelte`](SearchExample.svelte) first example
3. Copy and adapt to your page

### Task: Add search with icons
1. Read: [`README_SEARCH.md`](README_SEARCH.md) "With Icons" section
2. View: [`SearchExample.svelte`](SearchExample.svelte) example 2 or 3
3. Implement with `@ktibow/iconset-material-symbols` icons

### Task: Customize appearance
1. Read: [`SEARCH_COMPONENT.md`](SEARCH_COMPONENT.md) "CSS Variables" section
2. Check: [`README_SEARCH.md`](README_SEARCH.md) "Customization" section
3. Override CSS variables in your styles

### Task: Add search with live results
1. Read: [`SEARCH_USAGE_GUIDE.md`](SEARCH_USAGE_GUIDE.md) "Search as You Type" section
2. View: [`SearchExample.svelte`](SearchExample.svelte)
3. Implement with Svelte effects

### Task: Verify MD3 compliance
1. Read: [`SEARCH_MD3_MAPPING.md`](SEARCH_MD3_MAPPING.md)
2. Cross-reference token values

## 🎯 Quick Facts

| Aspect | Details |
|--------|---------|
| **Component file** | `Search.svelte` (204 lines) |
| **Import path** | `@/components/Search.svelte` |
| **Framework** | Svelte 5+ |
| **TypeScript** | ✅ Full support |
| **M3 compliant** | ✅ Yes |
| **Dark mode** | ✅ Built-in |
| **Accessible** | ✅ WCAG AA |
| **Setup required** | ❌ None |
| **Dependencies** | None (except M3 Svelte) |

## 📏 Specifications at a Glance

```
HEIGHT: 56dp (3.5rem)
WIDTH: 100% (flexible)
PADDING: 16dp (1rem) left/right
ICONS: 24dp (1.5rem)
ICON GAP: 16dp (1rem)
BORDER RADIUS: medium (8dp)
FOCUS: secondary color, 3px shadow
```

## 🎨 Colors at a Glance

```
Background:    surface-container-high
Text:          on-surface
Placeholder:   on-surface-variant
Icons:         on-surface / on-surface-variant
Focus:         secondary (3px border + shadow)
Hover:         8% on-surface tint
Active:        10% on-surface tint
```

## 💡 Pro Tips

1. **For search with filtering:**
   - Use `bind:value` and filter your items list
   - See SEARCH_USAGE_GUIDE.md "Filtered List" example

2. **For search that submits:**
   - Add `onkeydown` handler for Enter key
   - See SEARCH_USAGE_GUIDE.md "Submit on Enter" pattern

3. **For performance:**
   - Debounce search queries (300ms)
   - See SEARCH_USAGE_GUIDE.md "Debounced Search" pattern

4. **For UX:**
   - Add clear button (trailing icon)
   - Show supporting text with search tips
   - See SearchExample.svelte examples

5. **For mobile:**
   - Component is fully responsive
   - Works great in headers and forms
   - Auto-adapts to light/dark mode

## 🔗 Related Resources

- **M3 Svelte Docs**: https://m3-svelte.vercel.app/
- **Material Design 3**: https://m3.material.io/
- **Material Symbols Icons**: https://fonts.google.com/icons
- **Svelte Docs**: https://svelte.dev/docs

## 📞 Support

For help:
1. Check if your question is answered in [`SEARCH_USAGE_GUIDE.md`](SEARCH_USAGE_GUIDE.md)
2. Look at [`SearchExample.svelte`](SearchExample.svelte) for similar example
3. Review [`SEARCH_COMPONENT.md`](SEARCH_COMPONENT.md) for prop details

---

**Version**: 1.0.0  
**Last Updated**: 2026-03-14  
**Status**: Production Ready ✅
