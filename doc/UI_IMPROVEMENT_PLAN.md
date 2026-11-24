# StatWeb UI Improvement & Refactoring Plan

Written against commit: `3c3147eafaa6a7f2ed26cd9411f02638e297a6a1`  
Document version: 1.0.0  
Target Surface: Application Shell, Dashboard, Dataset & Analysis Views  

---

## 1. Design Language & Local System Overview

- **Audited Surfaces**:
  - Global Shell: `resources/views/layouts/app.blade.php`, `resources/views/layouts/navigation.blade.php`
  - Landing / Welcome: `resources/views/welcome.blade.php`
  - Core Workspace: `resources/views/dashboard.blade.php`, `resources/views/datasets/index.blade.php`, `resources/views/datasets/show.blade.php`, `resources/views/analysis/create.blade.php`
- **Design Sources**:
  - `tailwind.config.js`
  - `resources/css/app.css`
  - Inline tokens in `:root` (`resources/views/layouts/app.blade.php`)
- **Documented Decisions**:
  - **Color Palette**:
    - Navy Brand: `#162032` (Authoritative token in `:root` as `--sw-navy`)
    - Gold Accent: `#F6C90E` (Authoritative token in `:root` as `--sw-gold`)
    - Surface / Background: `#F7F9FA` (Neutral light canvas)
    - Border: `#E2E8F0` / Slate 200
  - **Typography**:
    - Display / Headings: `Space Grotesk` (weights 500, 600, 700)
    - Body / UI: `Inter` (weights 400, 500, 600)
    - Data / Tabular Numbers: `JetBrains Mono` with `tabular-nums`
- **Explicit Exceptions**: None documented.

---

## 2. Evidence Chain & Proven Findings

### Finding 1: Brand Token Drift & Hardcoded Color Inconsistencies
- **Surface**: All primary action buttons, links, and headers across `welcome.blade.php`, `dashboard.blade.php`, `datasets/index.blade.php`, `datasets/show.blade.php`, and `analysis/create.blade.php`.
- **Problem**: Elements hardcode legacy hex color `style="background: #0F2C59;"` and `style="color: #0F2C59;"`, conflicting with the primary design token `--sw-navy: #162032` established in `layouts/app.blade.php`. Sidebar logo in `app.blade.php` uses background `#f8f8f6` with text `#162032`, whereas `welcome.blade.php` uses background `#0F2C59` with text `#F6C90E`.
- **Design Evidence**: `resources/views/layouts/app.blade.php:17` defines `--sw-navy: #162032`.
- **Root Owner**: `tailwind.config.js` and `resources/views/layouts/app.blade.php`.
- **Correction**: Register `sw-navy: '#162032'` and `sw-gold: '#F6C90E'` in `tailwind.config.js` `theme.extend.colors`. Replace all occurrences of inline `style="background: #0F2C59"` with Tailwind classes `bg-sw-navy hover:bg-sw-navy/90 text-white`.

---

### Finding 2: Imperative Inline JavaScript State Handlers in Shell Navigation
- **Surface**: Desktop navigation links in `resources/views/layouts/app.blade.php:68-81`.
- **Problem**: Navigation items handle hover effects through raw inline JS string comparisons:
  ```html
  onmouseover="if(!this.style.background.includes('F6C90E')) { this.style.color='rgba(255,255,255,0.9)'; this.style.background='rgba(255,255,255,0.05)'; }"
  onmouseout="if(!this.style.background.includes('F6C90E')) { this.style.color='rgba(255,255,255,0.6)'; this.style.background=''; }"
  ```
- **Design Evidence**: Tailwind CSS is the designated utility framework in `resources/css/app.css`.
- **Root Owner**: `resources/views/layouts/app.blade.php`.
- **Correction**: Remove inline `onmouseover` and `onmouseout` handlers. Use conditional Blade styling with declarative Tailwind classes (`hover:bg-white/5 hover:text-white/90 text-white/60`, with active state `bg-sw-gold/15 text-sw-gold`).

---

### Finding 3: Typography Scale Mismatch in Tailwind Configuration
- **Surface**: Application-wide text rendering.
- **Problem**: `tailwind.config.js:15` specifies `sans: ['Figtree', ...defaultTheme.fontFamily.sans]`, but `Figtree` is never imported. Instead, `welcome.blade.php` and `app.blade.php` import Google Fonts (`Inter`, `Space Grotesk`, `JetBrains Mono`) and rely on manual inline CSS classes (`.sw-display`, `.sw-mono`).
- **Design Evidence**: Google Font imports in `resources/views/layouts/app.blade.php:11`.
- **Root Owner**: `tailwind.config.js`.
- **Correction**: Update `tailwind.config.js` to map `fontFamily.sans` to `Inter`, `fontFamily.display` to `Space Grotesk`, and `fontFamily.mono` to `JetBrains Mono`. This enables standard utility classes (`font-sans`, `font-display`, `font-mono`) and eliminates redundant inline `<style>` definitions.

---

## 3. Implementation Plan & Detailed Changes

### Step 1: Centralize Tokens in `tailwind.config.js`
**Target File**: `tailwind.config.js`
```javascript
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                'sw-navy': {
                    DEFAULT: '#162032',
                    dark: '#0F172A',
                    light: '#1E293B',
                },
                'sw-gold': {
                    DEFAULT: '#F6C90E',
                    hover: '#E5B800',
                },
                'sw-bg': '#F7F9FA',
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Space Grotesk', 'sans-serif'],
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
        },
    },

    plugins: [forms],
};
```

---

### Step 2: Refactor Navigation & Shell in `resources/views/layouts/app.blade.php`
- Remove inline `<style>` font definitions.
- Refactor desktop nav items to use declarative Blade classes:
```blade
<a href="{{ route('dashboard') }}"
   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-sw-gold/15 text-sw-gold' : 'text-white/60 hover:text-white/90 hover:bg-white/5' }}">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
    </svg>
    Dashboard
</a>
```
- Harmonize sidebar brand logo badge:
```blade
<div class="w-8 h-8 rounded-md flex items-center justify-center font-mono font-bold text-sm bg-sw-navy border border-white/10 text-sw-gold">
    Σ
</div>
```

---

### Step 3: Eliminate Inline Hex Styles Across Views
Replace all instances of `style="background: #0F2C59;"` with:
- `class="... bg-sw-navy hover:bg-sw-navy/90 text-white transition shadow-sm"`
Replace all instances of `style="color: #0F2C59;"` with:
- `class="... text-sw-navy hover:underline"`

**Affected Files**:
1. `resources/views/welcome.blade.php`
2. `resources/views/dashboard.blade.php`
3. `resources/views/datasets/index.blade.php`
4. `resources/views/datasets/show.blade.php`
5. `resources/views/analysis/create.blade.php`

---

## 4. UI/UX Polish Roadmap (Next Milestones)

1. **Dataset View Table Usability**:
   - Add `sticky top-0 bg-slate-50 z-10` to the table header in `datasets/show.blade.php` to prevent losing column labels during large row scrolls.
   - Use badge components (`bg-blue-50 text-blue-700 text-[10px] font-semibold px-1.5 py-0.5 rounded`) instead of raw Unicode symbols for measurement types (Scale, Ordinal, Nominal).
2. **Analysis Card Categorization**:
   - In `analysis/create.blade.php`, group the 8 analysis cards into three distinct semantic sections:
     - *Descriptive & Distribution*: Statistik Deskriptif, Distribusi Frekuensi, Scatter Plot
     - *Hypothesis Testing*: Uji Normalitas, One Sample t-Test, Independent t-Test, Paired t-Test, ANOVA
     - *Relationships & Modeling*: Korelasi Pearson/Spearman, Chi-Square, Regresi Linear (Sederhana & Berganda)
3. **Empty State Component**:
   - Replace empty table rows with a dedicated empty-state card featuring an illustrative icon, clear typography, and a direct CTA.

---

## 5. Verification & Testing

- **Asset Compilation**: Run `npm run build` to verify Tailwind compiles newly registered colors and fonts without errors.
- **Automated Tests**: Run `php artisan test` to confirm no blade syntax regressions.
- **Viewport Checks**:
  - Desktop (1280px+): Verify fixed sidebar, header alignment, and table scrolling.
  - Mobile (<640px): Verify mobile hamburger toggle, touch targets (minimum 44x44px), and table horizontal overflow.
