export type Pagination<T = unknown> = {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: PageLinks[];
};

export type PageLinks = {
    active: boolean;
    label: string;
    page: number | null;
    url: string | null;
};
