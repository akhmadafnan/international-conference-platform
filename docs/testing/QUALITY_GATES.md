# Quality Gates

## Gate A — Analysis
No material unresolved policy hidden by implementation assumptions.

## Gate B — Ready
Definition of Ready satisfied.

## Gate C — Implementation
Scope-compliant implementation.

## Gate D — Targeted Regression
Changed behavior GREEN.

## Gate E — Broader / Full Regression
Relevant suite GREEN when required.

## Gate F — Architecture / Security / Localization Audit
Review authorization, isolation, integration boundaries, auditability, and `id/en/ar` including RTL.

## Gate G — UAT
Human verifies actual workflow.

## Gate H — Closeout
Docs synchronized, Git state recorded, limitations documented.

## No-repeat-mistake rule
A recurring-risk defect must add a durable guardrail: test, validation, checklist, ADR, agent rule, UAT case, or CI gate.
