# ICHES Public Component & Mock Contract

**Workstream:** PF-00  
**Status:** BASELINE FOR PF-01 IMPLEMENTATION

## 1. Destination-oriented structure

Prototype code should already resemble the eventual Laravel/Inertia destination.

```text
resources/js/
├── components/
│   ├── ui/
│   ├── shared/
│   └── public/
├── layouts/
│   └── PublicLayout.vue
├── pages/
│   └── public/
│       └── Home.vue
├── composables/
├── i18n/
│   ├── id/
│   ├── en/
│   └── ar/
├── mocks/
├── types/
└── lib/
```

Do not create a separate SPA architecture that later requires migration.

## 2. Initial public components

PF-01 may introduce components such as:

- `AnnouncementBar`
- `PublicHeader`
- `MobileNavigation`
- `LanguageSwitcher`
- `ConferenceHero`
- `ConferenceIntroduction`
- `ImportantDates`
- `ConferenceTracks`
- `SpeakerShowcase`
- `SubmissionJourney`
- `FeaturedProgram`
- `RegistrationSection`
- `PublicationSection`
- `VenueSection`
- `PartnerSection`
- `LatestNews`
- `ConferenceFAQ`
- `FinalCTA`
- `PublicFooter`

Names are implementation suggestions; responsibilities matter more than exact filenames.

## 3. Data boundary

Components should receive typed props.

Avoid importing mock fixtures deep inside reusable components.

Preferred:

```text
Home.vue
  ↓
mock/home.ts during prototype
  ↓
typed props
  ↓
public components
```

Later:

```text
Laravel Controller
  ↓
Inertia page props
  ↓
Home.vue
  ↓
same typed public components
```

## 4. Mock-data policy

Mock data must be:

- typed;
- centralized;
- clearly identified as mock/demo;
- replaceable;
- free of invented product policies;
- capable of exercising empty and alternate states where relevant.

Do not scatter literals across Vue templates.

## 5. Initial TypeScript contracts

Suggested public-facing model boundaries:

```ts
interface ConferenceEditionSummary {
  id: string
  slug: string
  year: number
  title: string
  theme: string
  startDate: string
  endDate: string
  city: string
  country: string
}

interface ImportantDate {
  id: string
  label: string
  date: string
  status?: 'past' | 'current' | 'upcoming'
}

interface ConferenceTrack {
  id: string
  order: number
  title: string
  description?: string
}

interface Speaker {
  id: string
  name: string
  title?: string
  institution: string
  country?: string
  speakerType?: string
  topic?: string
  photoUrl?: string
}

interface ProgramHighlight {
  id: string
  title: string
  startsAt?: string
  location?: string
  type?: string
}

interface PublicationOpportunity {
  id: string
  type: 'proceedings' | 'selected_journal'
  title: string
  description: string
}

interface NewsSummary {
  id: string
  slug: string
  title: string
  publishedAt: string
  excerpt?: string
  imageUrl?: string
}
```

These contracts are frontend prototypes only. They must not freeze the database schema.

## 6. i18n boundary

User-facing strings belong in locale files.

Do not hardcode translated labels across components.

Support:

- `id`;
- `en`;
- `ar`.

Content locale and UI locale remain separate concepts.

## 7. Routing boundary

The prototype must remain compatible with Inertia routing.

Do not introduce Vue Router as the app router.

Links should be abstracted or implemented in a way that can become Inertia `<Link>` without redesign.

## 8. Form boundary

Public PF-01 is primarily presentational.

If any lightweight form proof is needed:

- use typed local state or Inertia-compatible structure;
- do not add a separate form framework;
- do not encode backend validation rules that have not been frozen.

## 9. Asset policy

Do not embed copyrighted reference-site assets.

Use project-owned, licensed, placeholder, or clearly replaceable prototype assets.

## 10. PF-01 implementation gate

PF-01 may start only when this PF-00 baseline is reviewed.

PF-01 must not extend scope into:

- authenticated dashboard;
- domain workflow;
- backend integration;
- payment;
- review;
- publication decisions;
- certificate logic.
