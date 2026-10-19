import test from 'node:test';
import assert from 'node:assert/strict';
import {
    keywordTags,
    serializeKeywords,
    bulkReferences,
    referencesText,
    removedReferences,
    academicUrl,
    wordCount,
} from '../../resources/js/lib/submissionInputs.ts';

test('comma separated academic phrases become independent structured keyword values', () => {
    const tags = keywordTags(
        'artificial intelligence, outcome-based education',
    );
    assert.deepEqual(tags, [
        'artificial intelligence',
        'outcome-based education',
    ]);
    assert.equal(
        serializeKeywords(tags),
        'artificial intelligence\noutcome-based education',
    );
});
test('paste handles newlines and Arabic commas without splitting multi-word keywords', () => {
    assert.deepEqual(
        keywordTags(
            ' Education,education\nasset based development، التعليم\r\n',
        ),
        ['Education', 'asset based development', 'التعليم'],
    );
    assert.deepEqual(
        keywordTags(serializeKeywords(['one two', 'three four'])),
        ['one two', 'three four'],
    );
});
const original = [
    {
        id: 'first',
        raw_citation: 'Author A. (2025). Title A.',
        doi: '10.1234/a',
        url: 'https://example.com/a',
    },
    {
        id: 'second',
        raw_citation: 'Author B. (2026). Title B.',
        doi: '10.1234/b',
        url: 'https://example.com/b',
    },
];
test('bulk paste preserves UUID and DOI/URL when references are reordered or inserted', () => {
    const result = bulkReferences(
        'New citation\n\n' + referencesText([...original].reverse()),
        original,
    );
    assert.equal(result[0].id, null);
    assert.deepEqual(result[1], original[1]);
    assert.deepEqual(result[2], original[0]);
    assert.deepEqual(removedReferences(result, original), []);
});
test('replacing a bulk citation at the same position starts without inherited metadata', () => {
    const result = bulkReferences(
        'Corrected citation\n' + original[1].raw_citation,
        original,
    );
    assert.deepEqual(result[0], {
        id: null,
        raw_citation: 'Corrected citation',
        doi: null,
        url: null,
    });
    assert.deepEqual(result[1], original[1]);
    assert.deepEqual(removedReferences(result, original), [original[0]]);
});

test('replacing every citation at unchanged cardinality removes all old identities', () => {
    const result = bulkReferences('Different citation A\nDifferent citation B', original);
    assert.ok(result.every((entry) => entry.id === null && entry.doi === null && entry.url === null));
    assert.deepEqual(removedReferences(result, original), original);
});

test('unchanged and reordered citations preserve identity and metadata without mutating the source', () => {
    const snapshot = structuredClone(original);
    assert.deepEqual(bulkReferences(referencesText(original), original), original);
    assert.deepEqual(
        bulkReferences(referencesText([...original].reverse()), original),
        [...original].reverse(),
    );
    assert.deepEqual(original, snapshot);
});

test('inserting between existing citations preserves both old identities', () => {
    const result = bulkReferences(
        `${original[0].raw_citation}\nInserted citation\n${original[1].raw_citation}`,
        original,
    );
    assert.deepEqual(result, [original[0], {
        id: null, raw_citation: 'Inserted citation', doi: null, url: null,
    }, original[1]]);
    assert.deepEqual(removedReferences(result, original), []);
});

test('reorder with a replacement preserves only the matching citation identity', () => {
    const result = bulkReferences(`${original[1].raw_citation}\nNew citation`, original);
    assert.deepEqual(result[0], original[1]);
    assert.deepEqual(result[1], { id: null, raw_citation: 'New citation', doi: null, url: null });
    assert.deepEqual(removedReferences(result, original), [original[0]]);
});

test('individual edits retain their explicit identity and updated metadata through bulk roundtrip', () => {
    const edited = [{ ...original[0], raw_citation: 'Corrected citation', doi: '10.1234/corrected', url: 'https://example.com/corrected' }];
    assert.deepEqual(bulkReferences(referencesText(edited), edited), edited);
    assert.deepEqual(removedReferences(edited, [original[0]]), []);
});
test('omitted persisted references are surfaced for explicit removal confirmation', () => {
    const result = bulkReferences(original[1].raw_citation, original);
    assert.deepEqual(removedReferences(result, original), [original[0]]);
    assert.deepEqual(original[0].doi, '10.1234/a');
});
test('unchanged legacy multiline and duplicate citations preserve their own identity', () => {
    const existing = [
        { ...original[0], raw_citation: 'Author A.\nLong title.' },
        { ...original[1], raw_citation: 'Author A.\nLong title.' },
    ];
    assert.deepEqual(
        bulkReferences(referencesText(existing), existing),
        existing,
    );
});

test('extra duplicates cannot reuse a persisted UUID or its DOI/URL', () => {
    const existing = [original[0], { ...original[1], raw_citation: original[0].raw_citation }];
    const result = bulkReferences(Array(3).fill(original[0].raw_citation).join('\n'), existing);
    assert.deepEqual(result.slice(0, 2), existing);
    assert.deepEqual(result[2], { id: null, raw_citation: original[0].raw_citation, doi: null, url: null });
    assert.deepEqual(removedReferences(result, existing), []);
    assert.deepEqual(removedReferences(bulkReferences(original[0].raw_citation, existing), existing), [existing[1]]);
});

test('legacy multiline citations retain their original text and metadata when reordered via CRLF input', () => {
    const existing = [{ ...original[0], raw_citation: 'Author A.\r\nLong title.' }, original[1]];
    const result = bulkReferences(referencesText([...existing].reverse()).replaceAll('\n', '\r\n'), existing);
    assert.deepEqual(result, [...existing].reverse());
    assert.deepEqual(removedReferences(result, existing), []);
});
test('dozens of citations stay one citation per nonblank line without parsing metadata', () => {
    const citations = Array.from(
        { length: 50 },
        (_, index) => `Author ${index}. A DOI-looking string 10.1234/text.`,
    );
    const result = bulkReferences(citations.join('\n'), []);
    assert.equal(result.length, 50);
    assert.equal(result[49].raw_citation, citations[49]);
    assert.ok(
        result.every(
            (entry) =>
                entry.id === null && entry.doi === null && entry.url === null,
        ),
    );
});
test('academic editor inserts only plain HTTP URLs and counts Unicode text', () => {
    assert.equal(academicUrl('javascript:alert(1)'), null);
    assert.equal(academicUrl('data:text/html,<script>alert(1)</script>'), null);
    assert.equal(
        academicUrl('https://example.com/paper'),
        'https://example.com/paper',
    );
    assert.equal(wordCount('التعليم والمجتمع\nacademic abstract'), 4);
});
