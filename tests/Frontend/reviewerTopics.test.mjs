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

test('multiple Topic grants and confirmed deactivation/reactivation do not create Claim or download controls', () => {
    const source = read('pages/academic/ReviewerTopics.vue');
    assert.match(source, /form\.track_ids\.includes\(track.id\)/);
    assert.match(source, /<DialogDescription>/);
    assert.match(source, /change\.delete/);
    assert.match(source, /change\.processing/);
    assert.doesNotMatch(source, /\/claim|fileUrl|confidential_comments/);
    const routing = read('components/academic/TopicRoutingEditor.vue');
    assert.match(routing, /v-if="routing.canCorrect"/);
    assert.match(routing, /routing.snapshotTopic/);
    assert.match(routing, /routing.currentTopic/);
    assert.match(routing, /props.routing.currentTrackId \?\? ''/);
    assert.match(routing, /form.reason.trim/);
    assert.doesNotMatch(routing, /'T01'|'T02'|checksum.*=/);
});
