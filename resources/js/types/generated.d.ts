declare namespace App {
    namespace Data {
        export type AnimeSummaryData = {
            id: number;
            title: Record<string, string | null>;
            slug: Record<string, string | null>;
            genres: string[];
            bannerImage: string | null;
            coverImage: App.Data.CoverImageData;
            link: string;
            permissions: App.Data.PermissionsData;
        };
        export type CoverImageData = {
            extraLarge: string;
            large: string;
            medium: string;
            color: string;
        };
        export type EpisodeSummaryData = {
            id: number;
            title: Record<string, string | null>;
            slug: Record<string, string | null>;
            epNo: string | null;
            postType: string;
            thumbnail: App.Data.CoverImageData | null;
            animeTitle: Record<string, string | null>;
            author: App.Data.UserSummaryData | null;
            publishedAt: string | null;
            isPublished: boolean;
            isCurrent: boolean;
            link: string;
            permissions: App.Data.PermissionsData;
        };
        export type MusicSummaryData = {
            id: number;
            title: Record<string, string | null>;
            slug: Record<string, string | null>;
            postType: string;
            thumbnail: App.Data.CoverImageData | null;
            author: App.Data.UserSummaryData | null;
            publishedAt: string | null;
            isPublished: boolean;
            isCurrent: boolean;
            link: string;
            permissions: App.Data.PermissionsData;
        };
        export type PermissionsData = {
            update: boolean;
            delete: boolean;
            publish: boolean;
        };
        export type UserSummaryData = {
            id: number;
            name: string | null;
            avatar: string | null;
        };
        namespace Home {
            export type HomePageResponse = {
                latestAnime: App.Data.AnimeSummaryData[];
                latestEpisodes: App.Data.EpisodeSummaryData[];
                latestMv: App.Data.MusicSummaryData[];
                latestAlbum: App.Data.MusicSummaryData[];
            };
        }
    }
    namespace Http {
        namespace Requests {
            export type PostType = 'tv' | 'bd' | 'movie';
            export type ShinraiPostType = 'mv' | 'album' | 'single';
        }
    }
}
declare namespace Illuminate {
    export type CursorPaginator<TKey, TValue> = {
        data: TKey extends string ? Record<TKey, TValue> : TValue[];
        links: {
            url: string | null;
            label: string;
            active: boolean;
        }[];
        meta: {
            path: string;
            per_page: number;
            next_cursor: string | null;
            next_page_url: string | null;
            prev_cursor: string | null;
            prev_page_url: string | null;
        };
    };
    export type CursorPaginatorInterface<TKey, TValue> =
        Illuminate.CursorPaginator<TKey, TValue>;
    export type LengthAwarePaginator<TKey, TValue> = {
        data: TKey extends string ? Record<TKey, TValue> : TValue[];
        links: {
            url: string | null;
            label: string;
            active: boolean;
        }[];
        meta: {
            total: number;
            current_page: number;
            first_page_url: string;
            from: number | null;
            last_page: number;
            last_page_url: string;
            next_page_url: string | null;
            path: string;
            per_page: number;
            prev_page_url: string | null;
            to: number | null;
        };
    };
    export type LengthAwarePaginatorInterface<TKey, TValue> =
        Illuminate.LengthAwarePaginator<TKey, TValue>;
}
declare namespace Spatie {
    namespace LaravelData {
        export type CursorPaginatedDataCollection<TKey, TValue> =
            Illuminate.CursorPaginator<TKey, TValue>;
        export type PaginatedDataCollection<TKey, TValue> =
            Illuminate.LengthAwarePaginator<TKey, TValue>;
    }
}
