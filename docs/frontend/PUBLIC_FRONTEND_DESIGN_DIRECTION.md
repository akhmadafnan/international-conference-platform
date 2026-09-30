# ICHES Public Frontend Design Direction

**Workstream:** PF-00 — Public Frontend Baseline Freeze  
**Status:** BASELINE FOR PROTOTYPING  
**Branch:** `proto/public-frontend-v1`  
**Scope:** Public-facing frontend only. No backend/domain implementation.

## 1. Objective

Build an original public frontend for ICHES that combines:

- the information completeness and conference-product maturity of AICIS+;
- the elegant, spacious, editorial visual language demonstrated by ForumX Conference;
- an original ICHES identity that remains reusable across conference editions.

AICIS+ and ForumX are references only. ICHES must not visually clone either website.

## 2. Product character

The public frontend should feel:

- international academic;
- contemporary;
- elegant;
- calm;
- credible;
- human;
- editorial;
- premium without luxury excess;
- mobile-first;
- accessible;
- multilingual-native.

Avoid:

- generic SaaS landing-page appearance;
- generic shadcn demo appearance;
- old-fashioned university portal appearance;
- excessive cards;
- excessive gradients;
- glassmorphism;
- neon palettes;
- heavy shadows;
- decorative motion that competes with content;
- banner/carousel-heavy homepages;
- hardcoded assumptions tied to one edition.

## 3. Reference roles

### AICIS+

Use primarily as a **product/information architecture reference**.

Relevant patterns:

- multilingual public conference experience;
- conference theme and identity;
- important dates;
- sub-themes/tracks;
- submission journey;
- publication information;
- announcements;
- news;
- FAQ;
- participant-facing conference information.

Do not copy:

- exact visual styling;
- exact navigation;
- content;
- assets;
- branding;
- page structure verbatim.

### ForumX Conference

Use primarily as a **visual/composition reference**.

Relevant principles:

- generous whitespace;
- editorial typography;
- strong hierarchy;
- restrained use of cards;
- large photographic moments;
- numbered information blocks;
- thin borders;
- simple navigation;
- deliberate section rhythm;
- strong final CTA;
- subtle motion.

Do not copy:

- React/Next architecture;
- exact section layouts;
- exact colors;
- branding;
- copy;
- imagery;
- animation implementation.

## 4. Locked frontend direction

Implementation must remain compatible with the project's selected direction:

- Vue 3;
- TypeScript;
- Inertia-compatible page/component structure;
- Tailwind CSS;
- shadcn-vue as component primitive foundation;
- Vite;
- Vue I18n;
- Lucide Vue.

Do not introduce:

- React;
- Next.js;
- Nuxt;
- Livewire;
- Bootstrap;
- Vuetify;
- PrimeVue;
- Element Plus;
- Vue Router as the application router;
- separate REST API architecture.

Until backend contracts exist, use typed mock data. Do not invent backend rules.

## 5. Public design principles

### 5.1 Editorial over dashboard-like

The public website must not feel like an authenticated dashboard.

Prefer:

- large type;
- strong section titles;
- whitespace;
- restrained surfaces;
- photography where meaningful;
- long-form conference storytelling;
- clear CTA hierarchy.

### 5.2 Information-rich without visual density

Conference information may be extensive, but the interface should reveal it progressively.

Use:

- clear sections;
- accordions;
- concise summaries;
- links to deeper pages;
- timelines;
- ordered/numbered structures.

Avoid putting every piece of content inside a card.

### 5.3 One edition, reusable platform

Content must come from edition-aware data contracts. Avoid hardcoding:

- year;
- host;
- dates;
- venue;
- tracks;
- speakers;
- fees;
- partners;
- publication destinations.

## 6. Initial design proof scope

The first visual implementation must be intentionally bounded to:

1. Public header;
2. Announcement bar;
3. Mobile navigation;
4. Language switcher;
5. Public homepage;
6. Public footer;
7. Indonesian / English / Arabic shell;
8. RTL proof;
9. Typed mock fixtures;
10. Responsive behavior.

Out of scope for this design proof:

- participant dashboard;
- payment workflow;
- submission wizard;
- reviewer workspace;
- backend;
- database;
- authentication;
- uploads;
- email;
- real documents;
- business-state implementation.

## 7. Homepage visual intent

The homepage should emphasize:

1. Conference identity and current edition;
2. Theme;
3. Date and location;
4. Primary CTA;
5. Conference introduction;
6. Important dates;
7. Tracks/sub-themes;
8. Key speakers;
9. Submission journey;
10. Program highlights;
11. Registration;
12. Publication;
13. Venue;
14. Organizers/partners;
15. Latest news;
16. FAQ;
17. Final CTA.

No hero carousel.

## 8. Acceptance criteria

PF-01 design proof is acceptable only if:

- ICHES is immediately recognizable as an international academic conference;
- information completeness is comparable to a mature conference website;
- visual density remains controlled;
- the site does not look like generic SaaS;
- the site does not look like a stock shadcn demo;
- desktop and mobile remain coherent;
- Arabic RTL is structurally supported;
- typed mock contracts can later become Inertia props without redesign;
- no product/business rule has been invented.
