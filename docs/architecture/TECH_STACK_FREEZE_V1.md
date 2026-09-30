# Technical Stack Freeze v1

**Document ID:** ICHES-ARCH-STACK-001
**Status:** FROZEN FOR ACCELERATED V1
**Updated:** 2026-09-30

## 1. Application shape

ICHES V1 is a **single Laravel modular monolith**.

No microservices.
No separate SPA API backend.
No GraphQL layer.
No duplicated Livewire + Inertia frontend.

Primary request flow:

Route
→ Controller
→ Form Request
→ Action / Service only when business behavior warrants it
→ Eloquent / domain model
→ Inertia
→ Vue page/component

Simple CRUD may remain Controller + Request + Eloquent.

Business actions use dedicated Actions such as:
- VerifyPayment
- SubmitAbstract
- CompleteAdministrativeScreening
- AssignReviewer
- RecordAcademicDecision
- IssuePresentationLoA
- ConfirmPresenter
- PublishSchedule
- RecordPresentationOutcome
- FinalizeAcademicApproval
- FinalizePublicationSnapshot
- FinalizeAward
- GenerateCertificate
- IssueCertificate
- RevokeCertificate

## 2. Backend

- PHP 8.4 target runtime
- Laravel 13
- Laravel built-in authentication / Fortify-backed Starter Kit features
- email verification required before active participant workflow
- built-in TOTP 2FA available; mandatory policy for high-risk internal authorities may be enabled
- Eloquent ORM
- Laravel Policies / Gates for resource authorization
- Spatie Laravel Permission compatible with Laravel 13 for role/permission vocabulary and edition-scoped authority implementation
- database transactions around consequential multi-record state changes

Laravel 13 supports PHP 8.3+, while PHP 8.4 is the V1 deployment target.

## 3. Frontend

Application skeleton:
**Official Laravel 13 Vue Starter Kit**

Frozen frontend:
- Vue 3
- Composition API
- TypeScript
- Inertia 3
- Tailwind CSS 4
- shadcn-vue
- Vite
- Vue I18n
- Lucide Vue icons

Public and authenticated experience stay in one Laravel/Inertia codebase.

Public SSR:
- Inertia SSR is allowed and preferred for public SEO-sensitive pages when deployment setup is stable;
- SSR must not block V1 release if it creates deployment risk;
- public metadata, canonical URLs, Open Graph, and crawlable HTML remain required.

Do not introduce:
- React
- Next.js
- Nuxt as a second frontend
- Vue Router as application routing authority
- Livewire as a second primary UI paradigm
- Bootstrap/Vuetify/PrimeVue as parallel design systems

## 4. Design system

shadcn-vue is the primitive/component foundation.

Final visual authority is the **ICHES Design System**:
- typography
- institutional color tokens
- semantic states
- spacing
- radius
- shadows
- containers
- motion
- RTL behavior

The admin/backoffice may visually resemble modern shadcn-admin interaction patterns, but the React template architecture is never imported.

## 5. Database

Primary relational database:
**MySQL 8.4 LTS**

Rules:
- charset/collation must support full utf8mb4;
- database timestamps are stored in UTC;
- Conference Edition stores its IANA timezone, e.g. Asia/Jakarta;
- application displays event times in Edition timezone;
- foreign keys and meaningful unique constraints are required;
- JSON columns are reserved for snapshots, evidence payloads, scoring/configuration, or low-query flexible metadata — not core relational structure.

Local development should target MySQL behavior. SQLite may be used only for isolated tooling/tests that do not hide MySQL-specific behavior.

## 6. Primary identifiers

All first-class domain entities use:
- UUIDv7 as internal primary identity;
- stored as CHAR(36) for V1 implementation simplicity;
- no auto-increment ID as public/business identity.

Human-readable identifiers are separate and edition-scoped, for example:
- Registration: ICHES27-REG-00124
- Paper: ICHES27-P-0042
- LoA: configurable formal document number
- Certificate: configurable certificate number

UUIDs are never shown as the primary human-facing identifier.

## 7. Authorization model

Authorization = role/authority + edition scope + resource relationship + restrictions.

Baseline:
- Spatie Permission provides role/permission mechanics;
- edition-scoped assignments are mandatory for business roles;
- Laravel Policies/Gates enforce actual resource access;
- COI restriction overrides normal allow rules;
- super-admin technical access does not silently become business decision authority.

The implementation must support one user holding multiple edition-scoped authorities.

## 8. Audit

No event-sourcing architecture.

Use append-only audit/activity records for consequential changes.

Preferred package:
- Spatie Laravel Activitylog compatible with Laravel 13

Audit must preserve where relevant:
- actor
- edition
- subject/resource
- action/event
- before
- after
- reason
- source/IP/request context when appropriate
- timestamp

Business records are not hard-deleted merely to hide history.

## 9. Queue, scheduler, cache, session

Accelerated V1 intentionally avoids Redis dependency.

- Queue: Laravel database queue
- Failed jobs: database
- Scheduler: Laravel Scheduler + one server cron entry
- Cache: database cache for production baseline
- Session: database session
- Notifications: Laravel database notifications + email channel

Redis remains a future optimization, not a V1 dependency.

## 10. Mail

Laravel Mail with environment-configured SMTP/provider.

Business state is never derived from email delivery success.

Email jobs are queued.

Dashboard/application remains source of truth.

## 11. Files and storage

Use Laravel Filesystem abstraction.

V1 default:
- private local disk on the application server for protected uploads;
- signed/authorized application routes for private downloads;
- public disk only for genuinely public assets/files.

Architecture remains S3-compatible without code redesign.

Protected files include:
- payment proofs
- unpublished abstracts/manuscripts
- review-related documents
- generated pre-publication documents when not public

Each significant uploaded manuscript/payment-proof file preserves integrity metadata such as size, MIME, checksum, uploader, timestamp, and version/provenance.

## 12. PDF generation

Preferred abstraction:
- Spatie Laravel PDF

V1 default rendering driver:
- Browsershot / Chromium

Reason:
- HTML/CSS print fidelity
- institutional LoA/certificate layout
- multilingual/Unicode/RTL capability
- same web design vocabulary

PDF generation should run as queued jobs for bulk certificates.

The driver remains swappable through configuration if deployment later requires Dompdf, WeasyPrint, Cloudflare, Gotenberg, or another supported driver.

## 13. QR

Use server-side QR generation through a maintained PHP QR library such as Endroid QR Code.

QR content must be a verification/lookup URL or opaque token.

Never encode unrestricted PII, payment data, or confidential academic data directly in the QR.

## 14. Search

No external search service in accelerated V1.

Use indexed relational queries and MySQL search/filter patterns sufficient for the expected scale.

No Meilisearch/Elasticsearch dependency.

## 15. Export

V1:
- CSV where tabular export is needed;
- JSON for canonical scholarly diagnostic/archive package;
- PDF for official human-readable documents;
- adapter-produced OJS/Crossref payloads where implemented.

No Excel framework is required unless an actual V1 workflow requires XLSX.

## 16. Testing and quality

Backend:
- Pest
- Laravel feature/integration tests
- focused domain tests for business actions
- authorization tests
- file/privacy tests
- lifecycle transition tests

Static/quality:
- Laravel Pint
- Larastan / PHPStan
- Composer audit
- npm audit as advisory/security check

Frontend:
- TypeScript checking
- lint/format checks
- critical component/state tests where valuable

End-to-end:
- browser UAT for all critical flows
- Playwright may be introduced for the highest-value smoke paths, but is not required to block Day 1 bootstrap.

## 17. Deployment baseline

Target:
- Linux VPS
- Nginx
- PHP-FPM
- MySQL 8.4
- Node.js where build/PDF runtime requires it
- queue worker managed by systemd or Supervisor
- scheduler cron
- HTTPS mandatory

No Docker requirement for V1 production.

Deployment scripts/configuration must keep environment secrets outside Git.

## 18. Integration boundaries

Accelerated V1:
- ROR: institution-first UX with manual fallback; live API is optional/non-blocking
- ORCID: optional identifier; no mandatory OAuth
- OJS: manual/assisted adapter/export
- Crossref: readiness/adapter payload; no automatic deposit required
- WhatsApp: convenience link only
- Payment: manual transfer only

## 19. Freeze result

This stack is **FROZEN for V1**.

A stack change after this point requires:
- written rationale;
- impact on two-week delivery;
- migration/integration impact;
- explicit Product Owner approval.
