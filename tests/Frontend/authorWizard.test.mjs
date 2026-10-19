import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { parse } from '@vue/compiler-sfc';
import { baseParse } from '@vue/compiler-dom';
import { createI18n } from 'vue-i18n';
import {
    wizardSteps,
    authorBlockers,
    configurationBlockers,
    findingStep,
    findingMessage,
    submissionBlocked,
    trackEmptyMessage,
} from '../../resources/js/lib/authorWizard.ts';
import { wizardUx } from '../../resources/js/i18n/wizardUx.ts';

const source = (path) =>
    readFileSync(
        new URL('../../resources/js/' + path, import.meta.url),
        'utf8',
    );
const sections = source('components/submissions/SubmissionWizardSections.vue');
const details = source('pages/submissions/Details.vue');
const i18n = createI18n({
    legacy: false,
    locale: 'en',
    messages: Object.fromEntries(
        Object.entries(wizardUx).map(([locale, messages]) => [
            locale,
            { wizardUx: messages },
        ]),
    ),
});
const warning = (code) => ({ code, severity: 'WARNING', field: 'metadata' });
const keywordBlocker = {
    code: 'KEYWORDS_COUNT_INVALID',
    severity: 'BLOCKED',
    field: 'keywords.id',
    context: { actual: 2, min: 3, max: 5 },
};

test('stepper has five ordered stages with localized labels and equal-height responsive buttons', () => {
    assert.deepEqual(
        wizardSteps.map((key) => wizardUx.en[key]),
        [
            'Details',
            'Upload Files',
            'Contributors',
            'References',
            'Review & Submit',
        ],
    );
    for (const messages of Object.values(wizardUx)) {
        assert.ok(
            wizardSteps.every(
                (key) => messages[key] && !/^\d/.test(messages[key]),
            ),
        );
    }
    assert.match(details, /const stepKeys = wizardSteps/);
    assert.match(details, /auto-rows-fr[^"\n]*md:grid-cols-5/);
    assert.match(details, /h-full! min-h-14/);
    for (const key of ['files', 'contributors', 'references', 'review']) {
        assert.ok(
            sections.includes(
                `<CardTitle>{{ t('wizardUx.${key}') }}</CardTitle>`,
            ),
        );
    }
});

test('contributor and final review text has no question-mark separators or broken encoding', () => {
    const template = parse(sections).descriptor.template.content;
    const visit = (node) => {
        if (node.type === 2)
            assert.doesNotMatch(node.content, /\?|\uFFFD|Ã|â€|Ø|Ù/);
        for (const child of node.children ?? []) visit(child);
    };
    visit(baseParse(template));
    assert.match(template, /aria-hidden="true"> · <\/span>/);
});

test('empty Tracks explain optional versus required Edition policy without fabricated options', () => {
    assert.equal(trackEmptyMessage(0, false), 'tracksOptionalEmpty');
    assert.equal(trackEmptyMessage(0, true), 'tracksRequiredEmpty');
    assert.equal(trackEmptyMessage(1, true), null);
    assert.match(
        details,
        /trackEmptyMessage\(\s*props.tracks.length,\s*props.policy\?\.trackRequired === true,?\s*\)/,
    );
    assert.match(details, /v-for="track in tracks"/);
    for (const messages of Object.values(wizardUx)) {
        assert.ok(messages.tracksRequiredEmpty && messages.tracksOptionalEmpty);
    }
    const missingTracks = {
        code: 'EDITION_TRACKS_UNAVAILABLE',
        severity: 'BLOCKED',
        field: 'edition.tracks',
    };
    const readiness = {
        status: 'BLOCKED',
        findings: [
            missingTracks,
            { code: 'TRACK_REQUIRED', severity: 'BLOCKED', field: 'track_id' },
        ],
    };
    assert.equal(submissionBlocked(readiness), true);
    assert.deepEqual(configurationBlockers(readiness), [missingTracks]);
    assert.deepEqual(authorBlockers(readiness), []);
});

test('optional DOI ORCID ROR and translations do not appear as Author blockers or prevent submit', () => {
    const readiness = {
        status: 'WARNING',
        findings: [
            'REFERENCE_DOI_MISSING',
            'CONTRIBUTOR_ORCID_MISSING',
            'AFFILIATION_ROR_MISSING',
            'OPTIONAL_TRANSLATIONS_MISSING',
        ].map(warning),
    };
    assert.deepEqual(authorBlockers(readiness), []);
    assert.equal(submissionBlocked(readiness), false);
    assert.match(sections, /v-for="\(item, i\) in blockers"/);
    assert.match(
        sections,
        /type="submit"\s+:disabled="blocked \|\| saving \|\| processing"/,
    );
    assert.match(sections, /if \(blocked.value \|\| processing.value\) return/);
});

test('insufficient keywords remain blocked and describe actual count and Edition limits in all locales', () => {
    const readiness = {
        status: 'BLOCKED',
        findings: [warning('OPTIONAL_TRANSLATIONS_MISSING'), keywordBlocker],
    };
    assert.equal(submissionBlocked(readiness), true);
    assert.deepEqual(authorBlockers(readiness), [keywordBlocker]);
    assert.equal(findingStep(keywordBlocker), 1);
    assert.equal(findingMessage(keywordBlocker), 'keywordsBelow');
    for (const locale of ['id', 'en', 'ar']) {
        i18n.global.locale.value = locale;
        const message = i18n.global.t(
            'wizardUx.' + findingMessage(keywordBlocker),
            { ...keywordBlocker.context, remaining: 1 },
        );
        assert.ok(
            message.includes('2') &&
                message.includes('1') &&
                message.includes('3–5'),
        );
        assert.doesNotMatch(message, /KEYWORDS|\{.*\}/);
    }
});

test('valid keyword count and optional warnings allow submit while genuine blockers and unknown state fail closed', () => {
    assert.equal(
        submissionBlocked({
            status: 'WARNING',
            findings: [warning('REFERENCE_DOI_MISSING')],
        }),
        false,
    );
    assert.equal(submissionBlocked({ status: 'READY', findings: [] }), false);
    assert.equal(submissionBlocked(null), true);
    const invalidOrcid = {
        code: 'CONTRIBUTOR_ORCID_INVALID',
        severity: 'BLOCKED',
        field: 'contributors.0.orcid',
    };
    assert.equal(
        submissionBlocked({ status: 'BLOCKED', findings: [invalidOrcid] }),
        true,
    );
    assert.equal(findingStep(invalidOrcid), 3);
    assert.equal(findingMessage(invalidOrcid), 'orcidInvalid');
    assert.equal(
        findingStep({ code: 'EDITION_POLICY_UNCONFIGURED', field: 'edition' }),
        null,
    );
    assert.equal(
        findingStep({
            code: 'EDITION_TRACKS_UNAVAILABLE',
            field: 'edition.tracks',
        }),
        null,
    );
    assert.equal(
        findingStep({
            code: 'ABSTRACT_FILE_REQUIRED',
            field: 'files.abstract',
        }),
        2,
    );
});
