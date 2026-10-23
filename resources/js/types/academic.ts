export type AcademicPacket = {
    paperCode: string;
    edition: string;
    locale: string;
    title: string;
    abstract: string;
    keywords: string[];
    authors: {
        id: string;
        display_name: string;
        email: string;
        affiliations: { institution_name: string; country_code: string }[];
    }[];
    references: { id: string; raw_citation: string }[];
    fileUrl: string | null;
};
export type AcademicPaper = {
    id: string;
    paperCode: string;
    edition: string;
    status: string;
    version: number;
    packet: AcademicPacket;
};

export type AcademicTopicRouting = {
    currentTrackId: string | null;
    currentTopic: { code: string; name_i18n: Record<string, string> } | null;
    snapshotTopic: { code: string; name_i18n: Record<string, string> } | null;
    canCorrect: boolean;
    tracks: { id: string; code: string; name_i18n: Record<string, string> }[];
};
