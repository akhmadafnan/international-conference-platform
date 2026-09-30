# ICHES Public Design System Baseline

**Workstream:** PF-00  
**Status:** PROVISIONAL — TO BE VALIDATED BY HOMEPAGE UAT

This document defines the first design-token direction. Values may be corrected during visual UAT before DESIGN SYSTEM LOCK.

## 1. Visual identity direction

Core palette intent:

- Warm Ivory / Off White — primary public background;
- Deep Forest Green — primary brand/action color;
- Near Black — main text;
- White — elevated/surface content;
- Muted Gold — restrained accent only;
- Neutral gray/green — supporting text and borders.

Initial token candidates:

```css
--background: #F7F6F1;
--surface: #FFFFFF;
--foreground: #17211C;
--muted-foreground: #657069;
--primary: #164734;
--primary-foreground: #FFFFFF;
--primary-dark: #0E3325;
--accent: #B79A5B;
--border: #DEDFD9;
```

These values are prototypes, not permanent brand assets until UAT locks them.

## 2. Color discipline

Forest green is the dominant identity color.

Muted gold is an accent, not a primary surface color.

Use gold for small details such as:

- active indicators;
- small labels;
- thin separators;
- selected milestones;
- restrained icon accents.

Avoid:

- gold-filled large panels;
- bright green;
- blue SaaS defaults;
- excessive gradient use.

## 3. Typography

Direction:

- editorial, modern, highly legible;
- strong display hierarchy on public pages;
- neutral legibility for body text and functional UI;
- Arabic typography must be tested separately rather than assumed to behave like Latin text.

The exact font family remains subject to final asset/licensing and implementation validation.

Typography scale should prioritize:

- large but controlled hero display;
- strong H2 section statements;
- compact metadata/eyebrow labels;
- comfortable reading measure for body copy.

## 4. Layout

Public layout principles:

- wide but bounded container;
- generous vertical section spacing;
- clear column rhythm;
- reduced density on mobile;
- no arbitrary full-width text paragraphs;
- intentional alternation between editorial text, photography, timeline, and structured data.

## 5. Borders, radius, and shadows

Prefer:

- thin neutral borders;
- restrained radius;
- minimal shadows;
- surface hierarchy through spacing and contrast rather than elevation.

Avoid turning every section into a rounded card.

## 6. Photography

Use photography intentionally.

Preferred:

- one strong hero visual;
- authentic conference/speaker/venue imagery;
- consistent ratios;
- high-quality editorial crop.

Avoid:

- poster images as hero;
- collage-heavy hero sections;
- stock-like corporate imagery;
- text embedded inside source images;
- multiple competing visuals above the fold.

## 7. Interaction and motion

Motion must be subtle and optional.

Acceptable:

- small hover transitions;
- restrained reveal;
- navigation transitions;
- accordion transitions.

Avoid:

- scroll-jacking;
- large parallax effects;
- animation required to understand content;
- continuous decorative motion.

Respect reduced-motion preferences.

## 8. Icons

Use Lucide Vue for functional iconography.

Icons should support text, not replace essential labels.

## 9. shadcn-vue policy

shadcn-vue is a primitive foundation.

Allowed examples:

- Button;
- Sheet;
- Dropdown Menu;
- Accordion;
- Dialog;
- Tabs;
- Tooltip;
- Select;
- Badge.

ICHES public components should compose primitives into branded components.

The final site must not look like a stock shadcn example.

## 10. Responsive behavior

Design mobile-first, but desktop should retain editorial impact.

Test at minimum:

- compact mobile;
- typical modern mobile;
- tablet;
- desktop;
- large desktop.

Navigation and CTA must remain usable at narrow widths.

## 11. RTL

Arabic is first-class.

Requirements:

- `dir="rtl"` behavior supported at app/layout level;
- logical CSS properties preferred;
- directional icons reviewed;
- grids and navigation verified;
- alignment must be semantically correct;
- mixed Latin/Arabic content tested;
- no late CSS patch strategy.

## 12. Accessibility baseline

Design proof must account for:

- semantic headings;
- keyboard navigation;
- visible focus;
- readable contrast;
- target sizing;
- reduced motion;
- alt text contract;
- accessible language switcher;
- usable mobile navigation.

Accessibility is part of the design system, not a post-production add-on.
