export function keywordTags(value: string): string[] {
    const seen = new Set<string>();
    return value
        .split(/[,،\r\n]+/u)
        .map((word) => word.trim())
        .filter((word) => {
            const key = word.toLowerCase();
            if (!word || seen.has(key)) return false;
            seen.add(key);
            return true;
        });
}

export function serializeKeywords(words: string[]): string {
    return keywordTags(words.join('\n')).join('\n');
}

export type Citation = {
    id: string | null;
    raw_citation: string;
    doi: string | null;
    url: string | null;
};
const citationLine = (text: string) => text.replace(/\r?\n/g, ' ').trim();

export function referencesText(references: Citation[]): string {
    return references
        .map((reference) => citationLine(reference.raw_citation))
        .join('\n');
}

// Only unchanged citations establish identity in bulk mode. Use individual
// editing to update a known reference without losing its identity or metadata.
// Omitted persisted IDs are confirmed by the caller before replacement.
export function bulkReferences(text: string, existing: Citation[]): Citation[] {
    const lines = text
        .split(/\r?\n/)
        .map((line) => line.trim())
        .filter(Boolean);
    const used = new Set<number>();
    const matches = lines.map((line) => {
        const index = existing.findIndex(
            (reference, i) =>
                !used.has(i) && citationLine(reference.raw_citation) === line,
        );
        if (index >= 0) used.add(index);
        return index;
    });
    return lines.map((line, i) => {
        const index = matches[i];
        const previous = index >= 0 ? existing[index] : null;
        if (previous) return { ...previous };
        return {
            id: null,
            raw_citation: line,
            doi: null,
            url: null,
        };
    });
}

export function removedReferences(
    next: Citation[],
    previous: Citation[],
): Citation[] {
    const ids = new Set(next.map((reference) => reference.id).filter(Boolean));
    return previous.filter(
        (reference) => reference.id !== null && !ids.has(reference.id),
    );
}

export function wordCount(text: string): number {
    return text.trim() ? text.trim().split(/\s+/u).length : 0;
}

export function academicUrl(value: string): string | null {
    try {
        const url = new URL(value);
        return ['https:', 'http:'].includes(url.protocol) ? url.href : null;
    } catch {
        return null;
    }
}
