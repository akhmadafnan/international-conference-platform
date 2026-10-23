import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { parse, compileTemplate } from '@vue/compiler-sfc';
import { submissionPolicyCopy } from '../../resources/js/i18n/submissionPolicy.ts';

const source = readFileSync(
    new URL('../../resources/js/pages/submissions/Index.vue', import.meta.url),
    'utf8',
);
const normalized = source.replace(/\s+/g, ' ');

test('submission policy and irreversible deletion confirmation are available in id en ar', () => {
    for (const locale of ['id', 'en', 'ar']) {
        const copy = submissionPolicyCopy(locale);
        assert.deepEqual(
            Object.keys(copy),
            Object.keys(submissionPolicyCopy('en')),
        );
        for (const value of Object.values(copy)) assert.ok(value.trim());
    }
    assert.match(
        submissionPolicyCopy('en').confirmDescription,
        /cannot be undone/,
    );
    assert.match(
        submissionPolicyCopy('id').confirmDescription,
        /tidak dapat dibatalkan/,
    );
    assert.match(
        submissionPolicyCopy('ar').confirmDescription,
        /لا يمكن التراجع/,
    );
});

test('submission list compiles with a real guarded Inertia delete and confirmation dialog', () => {
    const { descriptor } = parse(source);
    const result = compileTemplate({
        source: descriptor.template.content,
        filename: 'Index.vue',
        id: 'submission-policy',
    });
    assert.deepEqual(result.errors, []);
    assert.match(
        normalized,
        /v-if="\s*item.status === 'DRAFT' && item.destroyUrl\s*"/,
    );
    assert.match(
        source,
        /deletion\.processing \|\| !pendingDelete\.value\?\.destroyUrl/,
    );
    assert.match(source, /deletion\.delete\(pendingDelete\.value\.destroyUrl/);
    assert.match(
        source,
        /<DialogDescription>\s*{{\s*policy.confirmDescription\s*}}/,
    );
    assert.match(source, /:disabled="!registration.canCreateDraft"/);
    assert.match(source, /!selectedRegistration\.value\?\.canCreateDraft/);
    assert.match(source, /SUBMISSION_ALREADY_EXISTS/);
    assert.match(
        normalized,
        /item.status === 'DRAFT' \? policy.edit : policy.view/,
    );
});
