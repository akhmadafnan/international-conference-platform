export interface PaginationMeta {
    total: number;
    from: number | null;
    to: number | null;
    perPage: number;
    currentPage: number;
    lastPage: number;
    firstUrl: string | null;
    lastUrl: string | null;
    previousUrl: string | null;
    nextUrl: string | null;
    pages: { page: number | null; url: string | null }[];
}
