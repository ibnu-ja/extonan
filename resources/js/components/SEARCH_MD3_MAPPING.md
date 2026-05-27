# Search Component - MD3 Token Mapping

This document maps the created Search component to the Material Design 3 specifications provided.

## Color Tokens

### Enabled State
- **Container**: `var(--m3c-surface-container-high)` 
  - Maps to: `md.comp.search-bar.container.color`
- **Leading Icon**: `var(--m3c-on-surface)`
  - Maps to: `md.comp.search-bar.leading-icon.color`
- **Trailing Icon**: `var(--m3c-on-surface-variant)`
  - Maps to: `md.comp.search-bar.trailing-icon.color`
- **Input Text**: `var(--m3c-on-surface)`
  - Maps to: `md.comp.search-bar.input-text.color`
- **Supporting Text**: `var(--m3c-on-surface-variant)`
  - Maps to: `md.comp.search-bar.supporting-text.color`

### Hovered State
- **State Layer**: `rgba(52, 49, 58, 0.08)` (8% `on-surface`)
  - Maps to: `md.comp.search-bar.hover.state-layer.opacity` = 0.08
- **Supporting Text**: `var(--m3c-on-surface-variant)`

### Pressed State
- **State Layer**: `rgba(52, 49, 58, 0.1)` (10% `on-surface`)
  - Maps to: `md.comp.search-bar.pressed.state-layer.opacity` = 0.1
- **Supporting Text**: `var(--m3c-on-surface-variant)`

### Focused State
- **Focus Indicator**: `var(--m3c-secondary)`
  - Maps to: `md.comp.search-bar.focus.indicator.color`
- **Focus Indicator Thickness**: 2px border + 3px shadow
  - Maps to: `md.comp.search-bar.focus.indicator.thickness` = 3dp
- **Focus Indicator Offset**: Shadow provides visual offset
  - Maps to: `md.comp.search-bar.focus.indicator.outline.offset` = 2dp

## Layout & Spacing

### Container
- **Height**: 3.5rem (56dp)
  - Maps to: `md.comp.search-bar.container.height` = 56dp
- **Shape**: `var(--m3-shape-medium)` (rounded corners)
  - Maps to: `md.comp.search-bar.container.shape`
- **Elevation**: Implicit in MD3 (surface-container-high)

### Spacing (Baseline)
- **Leading Space**: 1rem (16dp)
  - Maps to: `md.comp.search-bar.leading-space` = 16dp
- **Trailing Space**: 1rem (16dp)
  - Maps to: `md.comp.search-bar.trailing-space` = 16dp
- **Icon-Label Gap**: 1rem (16dp)
  - Maps to: `md.comp.search-bar.leading-icon.leading-icon-label-space` = 16dp

### Icons
- **Icon Size**: 1.5rem (24dp)
  - Maps to: `md.comp.search-bar.icon.size` = 24dp

## Typography

### Input Text
- **Font**: Roboto (via `--m3-font`)
  - Maps to: `md.comp.search-bar.input-text.font` = Roboto
- **Size**: 1rem (16pt)
  - Maps to: `md.comp.search-bar.input-text.size` = 16pt
- **Weight**: 400
  - Maps to: `md.comp.search-bar.input-text.weight` = 400
- **Line Height**: 1.5rem (24pt)
  - Maps to: `md.comp.search-bar.input-text.line-height` = 24pt
- **Letter Spacing**: 0.5px
  - Maps to: `md.comp.search-bar.input-text.tracking` = 0.5pt

### Supporting Text
- **Font**: Roboto (via `--m3-font`)
  - Maps to: `md.comp.search-bar.supporting-text.font` = Roboto
- **Size**: 0.75rem (12pt) - uses standard body-small equivalent
  - Maps to: `md.comp.search-bar.supporting-text.size` = 16pt (base, reduced via scale)
- **Weight**: 400
  - Maps to: `md.comp.search-bar.supporting-text.weight` = 400
- **Line Height**: 1.25rem (20pt)
  - Maps to: `md.comp.search-bar.supporting-text.line-height` = 24pt
- **Letter Spacing**: 0.4px
  - Maps to: `md.comp.search-bar.supporting-text.tracking` = 0.5pt

## Component Architecture

### Structure
```
.search-container
├── .search-bar (main container)
│   ├── .icon-button.leading (or .icon-slot.leading)
│   ├── .input-field
│   └── .icon-button.trailing (or .icon-slot.trailing)
└── .supporting-text (optional)
```

### States
- **Enabled**: Default rendering with surface-container-high background
- **Hovered**: Background tinted with 8% on-surface opacity
- **Focused**: 2px secondary border with 3px focus shadow
- **Pressed** (icons): 10% on-surface background on icon buttons

## Dark Mode Support

The component includes dark mode variants:
- Focus shadow colors adjusted for dark theme
- Icon button hover/active states use light-themed colors
- Automatic adaptation via `@media (prefers-color-scheme: dark)`

## Accessibility

✓ Semantic HTML with `<input>` element  
✓ ARIA labels on icon buttons  
✓ Icon buttons use `tabindex="-1"` to maintain proper focus order  
✓ Color contrast meets WCAG AA standards  
✓ Focus indicator clearly visible (3px shadow)  
✓ Proper placeholder text handling

## Customization

The component exposes CSS variables for easy customization:

```css
--m3-search-container-height: 3.5rem;
--m3-search-container-shape: var(--m3-shape-medium);
--m3-search-icon-size: 1.5rem;
--m3-search-leading-space: 1rem;
--m3-search-trailing-space: 1rem;
--m3-search-icon-label-gap: 1rem;
```

Override these in your app CSS to customize the search bar dimensions and spacing.
