import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { parse, compileTemplate } from '@vue/compiler-sfc';
import { reviewerTopicCopy } from '../../resources/js/i18n/reviewerTopics.ts';

const read = (path) =>
    readFileSync(
        new URL('../../resources/js/' + path, import.meta.url),
        'utf8',
    );

test('Reviewer Topic and routing labels have matching id en ar keys and non-empty translations', () => {
    for (const locale of ['id', 'en', 'ar']) {
        const copy = reviewerTopicCopy(locale);
        assert.deepEqual(
            Object.keys(copy),
            Object.keys(reviewerTopicCopy('en')),
        );
        for (const value of Object.values(copy)) assert.ok(value.trim());
    }
});

test('Topic management and routing templates compile with Lyra primitives and fluid safe wrapping', () => {
    for (const filename of [
        'pages/academic/ReviewerTopics.vue',
        'components/academic/TopicRoutingEditor.vue',
    ]) {
        const source = read(filename);
        const { descriptor } = parse(source);
        const compiled = compileTemplate({
            source: descriptor.template.content,
            filename,
            id: filename,
        });
        assert.deepEqual(compiled.errors, []);
        assert.doesNotMatch(
            source,
            /v-html|@lucide\/vue|<(select|textarea)\b|max-w-(?:prose|[2-7]xl)/,
        );
        assert.match(source, /w-full min-w-0/);
        assert.match(source, /overflow-wrap:anywhere/);
    }
});

test('one reviewer row and one editor save the complete Topic set without repeated confirmations', () => {
    const source = read('pages/academic/ReviewerTopics.vue');
    assert.match(source, /form\.track_ids\.includes\(track.id\)/);
    assert.match(source, /<DialogDescription>/);
    assert.match(source, /v-for="reviewer in reviewers"/);
    assert.match(source, /:key="reviewer.id"/);
    assert.match(source, /reviewer_id: null as string \| null/);
    assert.match(source, /reviewer\?\.stateToken/);
    assert.match(source, /expected_state/);
    assert.match(source, /\.put\(/);
    assert.match(source, /form\.processing/);
    assert.match(source, /@submit.prevent="save"/);
    assert.match(source, /v-model="form.reviewer_id"/);
    assert.match(source, /v-for="account in reviewerOptions"/);
    assert.match(source, /account.name/);
    assert.match(source, /account.email/);
    assert.match(source, /v-if="editing"/);
    assert.match(source, /c.noReviewers/);
    assert.doesNotMatch(source, /reviewer_email|type="email"|<Input/);
    assert.match(source, /<DataTablePagination :pagination="pagination"/);
    assert.match(source, /locale.startsWith\('ar'\)/);
    assert.match(source, /scope="row"/);
    assert.match(source, /role="alert"/);
    assert.doesNotMatch(
        source,
        /confirmGrant|confirmDisable|change\.delete|v-for="row in eligibilities"/,
    );
    assert.doesNotMatch(source, /\/claim|fileUrl|confidential_comments/);
    const routing = read('components/academic/TopicRoutingEditor.vue');
    assert.match(routing, /v-if="routing.canCorrect"/);
    assert.match(routing, /routing.snapshotTopic/);
    assert.match(routing, /routing.currentTopic/);
    assert.match(routing, /props.routing.currentTrackId \?\? ''/);
    assert.match(routing, /form.reason.trim/);
    assert.doesNotMatch(routing, /'T01'|'T02'|checksum.*=/);
});
