<?php

use App\Support\Scholarly\DoiNormalizer;

test('a null or blank doi normalizes to null', function () {
    expect(DoiNormalizer::normalize(null))->toBeNull()
        ->and(DoiNormalizer::normalize(''))->toBeNull()
        ->and(DoiNormalizer::normalize('   '))->toBeNull()
        ->and(DoiNormalizer::normalize("\t\n"))->toBeNull();
});

test('a conventional bare doi is kept in its canonical bare form', function () {
    expect(DoiNormalizer::normalize('10.1000/xyz123'))->toBe('10.1000/xyz123')
        ->and(DoiNormalizer::normalize(' 10.1000/xyz123 '))->toBe('10.1000/xyz123')
        ->and(DoiNormalizer::normalize('10.12345/reh.(2024)12:3'))->toBe('10.12345/reh.(2024)12:3')
        ->and(DoiNormalizer::normalize('10.1000/SICI-1099-0844'))->toBe('10.1000/SICI-1099-0844')
        ->and(DoiNormalizer::normalize('10.1000/ABC'))->toBe('10.1000/ABC');
});

test('doi prefix and doi.org resolver variants normalize to the same bare doi', function () {
    $canonical = '10.1000/xyz123';

    foreach ([
        'doi:10.1000/xyz123',
        'DOI:10.1000/xyz123',
        'doi: 10.1000/xyz123',
        'https://doi.org/10.1000/xyz123',
        'http://doi.org/10.1000/xyz123',
        'https://dx.doi.org/10.1000/xyz123',
        'http://dx.doi.org/10.1000/xyz123',
        'https://www.doi.org/10.1000/xyz123',
        'doi.org/10.1000/xyz123',
        'dx.doi.org/10.1000/xyz123',
        'HTTPS://DOI.ORG/10.1000/xyz123',
    ] as $variant) {
        expect(DoiNormalizer::normalize($variant))->toBe($canonical);
    }
});

test('terminal slashes in the doi suffix are preserved for bare and resolver forms alike', function () {
    $canonical = '10.1000/example/';

    expect(DoiNormalizer::normalize('10.1000/example/'))->toBe($canonical)
        ->and(DoiNormalizer::normalize(' 10.1000/example/ '))->toBe($canonical)
        ->and(DoiNormalizer::normalize('doi:10.1000/example/'))->toBe($canonical)
        ->and(DoiNormalizer::normalize('https://doi.org/10.1000/example/'))->toBe($canonical)
        ->and(DoiNormalizer::normalize('http://doi.org/10.1000/example/'))->toBe($canonical)
        ->and(DoiNormalizer::normalize('https://dx.doi.org/10.1000/example/'))->toBe($canonical)
        ->and(DoiNormalizer::normalize('http://dx.doi.org/10.1000/example/'))->toBe($canonical)
        ->and(DoiNormalizer::normalize('https://www.doi.org/10.1000/example/'))->toBe($canonical)
        ->and(DoiNormalizer::normalize('doi.org/10.1000/example/'))->toBe($canonical)
        ->and(DoiNormalizer::normalize('dx.doi.org/10.1000/example/'))->toBe($canonical)
        ->and(DoiNormalizer::normalize('HTTPS://DOI.ORG/10.1000/example/'))->toBe($canonical);
});

test('multiple slashes inside the doi suffix are preserved exactly by both extraction paths', function () {
    foreach ([
        '10.1000/a//b/',
        '10.1000/a//b//',
        '10.1000//leading',
        '10.1000/trailing//',
    ] as $canonical) {
        expect(DoiNormalizer::normalize($canonical))->toBe($canonical)
            ->and(DoiNormalizer::normalize('https://doi.org/'.$canonical))->toBe($canonical)
            ->and(DoiNormalizer::normalize('http://dx.doi.org/'.$canonical))->toBe($canonical)
            ->and(DoiNormalizer::normalize('doi.org/'.$canonical))->toBe($canonical)
            ->and(DoiNormalizer::normalize('doi:'.$canonical))->toBe($canonical);
    }
});

test('bare and resolver inputs with a terminal slash normalize to the same identity', function () {
    $bare = DoiNormalizer::normalize('10.1000/example/');
    $resolver = DoiNormalizer::normalize('https://doi.org/10.1000/example/');
    $prefixed = DoiNormalizer::normalize('doi:10.1000/example/');

    expect($bare)->toBe('10.1000/example/')
        ->and($resolver)->toBe($bare)
        ->and($prefixed)->toBe($bare);
});

test('malformed doi values are rejected instead of being invented or looked up', function () {
    $invalidDois = [
        'not-a-doi',
        '10.1000',
        '10.1000/',
        '10.1/xyz',
        '10.1234567890/xyz',
        'doi:garbage',
        'doi:',
        'doi:10.1000/with space',
        'doi:https://doi.org/10.1000/xyz123',
        'https://doi.org/',
        'https://doi.org/not-a-doi',
        'https://example.org/10.1000/xyz123',
        '10.1000/with space',
        '10.1000/xyz123?foo=bar',
        '10.1000/xyz123#fragment',
        'https://doi.org/10.1000/xyz123?foo=bar',
        'https://doi.org/10.1000/xyz123#fragment',
        '  10.1000  ',
    ];

    foreach ($invalidDois as $doi) {
        expect(fn () => DoiNormalizer::normalize($doi))->toThrow(DomainException::class);
    }
});

test('doi length is bounded by the schema string length', function () {
    $atLimit = '10.1000/'.str_repeat('a', 247);
    $overLimit = '10.1000/'.str_repeat('a', 248);

    expect(strlen($atLimit))->toBe(DoiNormalizer::MAX_LENGTH)
        ->and(DoiNormalizer::normalize($atLimit))->toBe($atLimit)
        ->and(strlen($overLimit))->toBe(DoiNormalizer::MAX_LENGTH + 1)
        ->and(fn () => DoiNormalizer::normalize($overLimit))->toThrow(DomainException::class);
});
