export const wizardSteps = [
    'details',
    'files',
    'contributors',
    'references',
    'review',
] as const;

export type ReadinessFinding = {
    code: string;
    field: string;
    severity: string;
    context?: Record<string, number>;
};
export type AuthorReadiness = { status: string; findings: ReadinessFinding[] };

export function submissionBlocked(readiness: AuthorReadiness | null): boolean {
    return (
        readiness === null ||
        readiness.status === 'BLOCKED' ||
        readiness.findings.some((item) => item.severity === 'BLOCKED')
    );
}

// Warnings remain in the domain response. Edition setup is an Admin action,
// so keep it separate from the Author's actionable corrections.
export function authorBlockers(
    readiness: AuthorReadiness | null,
): ReadinessFinding[] {
    const tracksUnavailable = readiness?.findings.some(
        (item) =>
            item.code === 'EDITION_TRACKS_UNAVAILABLE' &&
            item.severity === 'BLOCKED',
    );
    return (
        readiness?.findings.filter(
            (item) =>
                item.severity === 'BLOCKED' &&
                findingStep(item) !== null &&
                !(tracksUnavailable && item.code === 'TRACK_REQUIRED'),
        ) ?? []
    );
}

export function configurationBlockers(
    readiness: AuthorReadiness | null,
): ReadinessFinding[] {
    return (
        readiness?.findings.filter(
            (item) => item.severity === 'BLOCKED' && findingStep(item) === null,
        ) ?? []
    );
}

export function findingStep(item: ReadinessFinding): number | null {
    if (
        item.field === 'edition' ||
        item.field.startsWith('edition.') ||
        item.code === 'PRIMARY_LOCALE_INVALID'
    )
        return null;
    if (item.field.startsWith('contributors')) return 3;
    if (item.field.startsWith('files')) return 2;
    if (item.field.startsWith('references')) return 4;
    return 1;
}

export function findingMessage(item: ReadinessFinding): string {
    const code = item.code;
    if (code === 'EDITION_TRACKS_UNAVAILABLE') return 'tracksRequiredEmpty';
    if (
        code === 'EDITION_POLICY_UNCONFIGURED' ||
        code === 'PRIMARY_LOCALE_INVALID'
    )
        return 'adminConfiguration';
    if (code === 'PRIMARY_TITLE_MISSING') return 'titleRequired';
    if (code === 'PRIMARY_ABSTRACT_MISSING') return 'abstractRequired';
    if (code === 'KEYWORDS_COUNT_INVALID') {
        return (item.context?.actual ?? 0) < (item.context?.min ?? 0)
            ? 'keywordsBelow'
            : 'keywordsAbove';
    }
    if (code.startsWith('KEYWORDS_')) return 'keywordsInvalid';
    if (code.startsWith('TRACK_')) return 'trackInvalid';
    if (code === 'CONTRIBUTORS_MISSING') return 'authorsRequired';
    if (code === 'CORRESPONDING_AUTHOR_INVALID') return 'correspondingRequired';
    if (code === 'CONTRIBUTOR_ORCID_INVALID') return 'orcidInvalid';
    if (code.includes('AFFILIATION')) return 'affiliationInvalid';
    if (code === 'CONTRIBUTOR_EMAIL_INVALID') return 'emailInvalid';
    if (code === 'CONTRIBUTOR_NAME_INVALID') return 'nameInvalid';
    if (code === 'CONTRIBUTOR_COUNTRY_INVALID') return 'countryInvalid';
    if (code === 'CONTRIBUTOR_ORDER_INVALID') return 'authorOrderInvalid';
    if (code.startsWith('ABSTRACT_FILE_')) return 'fileRequired';
    return 'reviewRequired';
}

export function trackEmptyMessage(
    count: number,
    required: boolean,
): string | null {
    return count > 0
        ? null
        : required
          ? 'tracksRequiredEmpty'
          : 'tracksOptionalEmpty';
}
