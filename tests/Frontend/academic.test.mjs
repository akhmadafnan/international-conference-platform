import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { parse, compileTemplate } from '@vue/compiler-sfc';
import { academic } from '../../resources/js/i18n/academic.ts';

const source = (path) =>
    readFileSync(
        new URL('../../resources/js/' + path, import.meta.url),
        'utf8',
    );
const keys = (object, prefix = '') =>
    Object.entries(object)
        .flatMap(([key, value]) =>
            typeof value === 'object'
                ? keys(value, prefix + key + '.')
                : [prefix + key],
        )
        .sort();

test('academic labels states and validation feedback have matching id en ar translations', () => {
    assert.deepEqual(keys(academic.id), keys(academic.en));
    assert.deepEqual(keys(academic.ar), keys(academic.en));
    for (const messages of Object.values(academic)) {
        assert.ok(
            messages.errors.revisionLimit &&
                messages.errors.coi &&
                messages.errors.stale,
        );
        assert.ok(
            messages.loaHelp && messages.decisionHelp && messages.coiHelp,
        );
        for (const state of [
            'PASS',
            'CORRECTION_REQUIRED',
            'INELIGIBLE',
            'ACCEPT',
            'REVISION',
            'REJECT',
            'ACCEPTED',
            'REVISION_REQUIRED',
            'REJECTED',
        ])
            assert.ok(messages.states[state]);
    }
});

test('academic Vue templates compile and use escaped metadata with existing Lyra form primitives', () => {
    for (const name of ['Index', 'Submission', 'Reviews', 'Review', 'Author']) {
        const filename = `pages/academic/${name}.vue`;
        const vue = source(filename);
        const descriptor = parse(vue).descriptor;
        const result = compileTemplate({
            source: descriptor.template.content,
            filename,
            id: name,
        });
        assert.deepEqual(result.errors, []);
        assert.doesNotMatch(vue, /v-html|@lucide\/vue|<(select|textarea)\b/);
    }
    assert.doesNotMatch(
        source('components/academic/AcademicPacket.vue'),
        /v-html/,
    );
});

test('author interface contains neither reviewer identity nor confidential review and decision fields', () => {
    const vue = source('pages/academic/Author.vue');
    assert.doesNotMatch(
        vue,
        /reviewer_id|reviewer_email|confidential_comments|rationale/,
    );
    assert.match(vue, /:href="loaUrl"/);
    assert.match(vue, /v-if="canRevise"/);
    assert.match(
        source('pages/academic/Review.vue'),
        /assignment.status === 'COMPLETED'\s*\|\|\s*report.processing/,
    );
});
