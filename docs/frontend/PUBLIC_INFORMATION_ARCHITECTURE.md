# ICHES Public Information Architecture

**Workstream:** PF-00  
**Status:** BASELINE FOR PUBLIC FRONTEND PROTOTYPING

This document refines the public information architecture in `FRONTEND_PRODUCT_SPEC_V1.md`. It does not replace product rules.

## 1. Primary public navigation

### Conference

- About ICHES
- Theme & Tracks
- Speakers
- Important Dates
- Program / Schedule
- Venue

### Authors

- Call for Papers
- Submission Guidelines
- Templates / Downloads
- Review Process
- Publication

### Attend

- Registration
- Fees
- Participant Information
- Accommodation Information
- FAQ

### Additional primary destination

- News

### Header utilities

- locale switcher: ID / EN / العربية
- Login or Dashboard depending on authentication state
- primary CTA: Submit Abstract / Register according to edition state

The final CTA behavior must follow domain state and must not be invented in frontend code.

## 2. Homepage section architecture

Recommended baseline order:

1. Announcement bar
2. Header / navigation
3. Hero
4. Conference introduction
5. Important dates
6. Conference theme & tracks
7. Keynote / featured speakers
8. Submission journey
9. Featured program
10. Registration / participation
11. Publication opportunities
12. Venue
13. Organizers / host / partners
14. Latest news
15. FAQ
16. Final CTA
17. Footer

This order may be adjusted during UAT if hierarchy or content density requires it, but any major change must remain traceable.

## 3. Hero requirements

The hero must communicate immediately:

- ICHES identity;
- edition/year;
- current conference theme;
- date;
- location;
- one primary action;
- one secondary action where useful.

Use one strong visual or editorial composition.

Do not use a carousel.

## 4. Important dates

Important dates should be edition-driven and chronological.

Potential entries include:

- registration opening/closing;
- abstract deadline;
- review result;
- full article deadline;
- presentation/slides deadline if configured;
- conference date;
- revision deadline;
- public publication milestone.

Do not expose internal unpublished dates.

## 5. Tracks / sub-themes

Use numbered or editorial treatment instead of heavy card grids where practical.

Each track should support:

- order;
- title;
- short description;
- optional detail route;
- current-edition availability.

## 6. Speakers

Homepage shows a curated subset only.

Speaker representation may include:

- name;
- title;
- institution;
- country;
- speaker type;
- session/topic;
- photo.

The full Speakers page owns complete browsing.

## 7. Submission journey

Public-facing journey should explain the experience without exposing technical enums.

Example conceptual stages:

1. Register;
2. Complete required participation/payment gate;
3. Submit abstract;
4. Administrative/review process;
5. Academic decision;
6. Full article / presenter confirmation when accepted;
7. Presentation;
8. Revision/final academic process;
9. Publication handoff/readiness.

Exact wording must stay aligned with canonical lifecycle rules.

## 8. Program

Homepage shows highlights, not the entire schedule.

Before publication:

- program overview;
- keynote blocks;
- major activities.

After schedule publication:

- highlight selected published sessions;
- link to full schedule.

Never expose draft scheduling.

## 9. Registration and fees

Homepage uses concise participation/registration presentation.

Detailed policy belongs on Registration & Fees.

No pricing or package assumptions may be invented in UI fixtures; mock data must clearly state that values are illustrative.

## 10. Publication

Communicate the distinction between:

- proceedings;
- selected journal pathway;
- publication readiness;
- external journal acceptance.

Never imply that Selected for Journal means Accepted by Journal.

## 11. Venue

Public presentation may include:

- venue name;
- city/region/country;
- address;
- accessibility information;
- directions/map CTA;
- optional accommodation notes.

Do not build hotel booking.

## 12. News and announcement hierarchy

Announcement bar is for urgent/high-value current-edition notices.

News is for:

- deadline changes;
- speaker announcements;
- schedule releases;
- award announcements;
- post-event reporting.

Homepage should show a small curated set only.

## 13. Footer

Footer should provide:

- ICHES identity;
- conference navigation;
- author/attendee links;
- contact;
- social channels if configured;
- edition/past-edition access;
- privacy/legal links when defined.

## 14. Post-event transformation

The homepage must support a completed-edition state.

After an edition ends, conversion-heavy CTA may shift toward:

- proceedings;
- highlights;
- awardees;
- certificate/document verification;
- past edition archive.

This transition must be data/state driven.
