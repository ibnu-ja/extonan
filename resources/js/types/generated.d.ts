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
        export type LabelValue = {
            key: string;
            value: string;
        };
        export type MediaData = {
            id: number;
            filename: string;
            extension: string;
            directory: string;
            url: string;
            mediumUrl: string | null;
            largeUrl: string | null;
            size: number;
            mimeType: string;
            aggregateType: string;
            createdAt: string;
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
        export type PaginationData = {
            currentPage: number;
            lastPage: number;
            perPage: number;
            total: number;
            links: {
                url: string | null;
                label: string;
                active: boolean;
            }[];
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
        namespace Anime {
            export type AnimeAZResponse = {
                items: App.Data.Anime.AnimeListItemData[];
                canCreate: boolean;
            };
            export type AnimeFormData = {
                id: number | null;
                title: Record<string, string | null>;
                description: Record<string, string | null>;
                anilistId: number | null;
                metadata: object | null;
                isPublished: boolean;
                canPublish: boolean;
            };
            export type AnimeIndexResponse = {
                items: App.Data.Anime.AnimeListItemData[];
                pagination: App.Data.PaginationData;
                seasons: string[];
                canCreate: boolean;
                genres: App.Data.LabelValue[];
                tags: App.Data.LabelValue[];
                sortOptions: App.Data.LabelValue[];
            };
            export type AnimeListItemData = {
                id: number;
                title: Record<string, string | null>;
                slug: Record<string, string | null>;
                coverImage: App.Data.CoverImageData;
                isPublished: boolean;
                link: string;
                permissions: App.Data.PermissionsData;
            };
            export type AnimeShowResponse = {
                anime: App.Data.Anime.AnimeListItemData;
                episodes: App.Data.EpisodeSummaryData[];
                canCreateEpisode: boolean;
            };
        }
        namespace Home {
            export type HomePageResponse = {
                latestAnime: App.Data.AnimeSummaryData[];
                latestEpisodes: App.Data.EpisodeSummaryData[];
                latestMv: App.Data.MusicSummaryData[];
                latestAlbum: App.Data.MusicSummaryData[];
            };
        }
    }
    namespace Enums {
        export type Permission =
            | 'post.create'
            | 'post.read.any'
            | 'post.read.self'
            | 'post.update.any'
            | 'post.update.self'
            | 'post.delete.any'
            | 'post.delete.self'
            | 'post.publish.self'
            | 'post.publish.any'
            | 'user.invite'
            | 'user.read.any'
            | 'user.read.self'
            | 'user.delete.any'
            | 'user.delete.self'
            | 'user.edit.self'
            | 'user.edit.any';
        export type Role = 'admin' | 'editor' | 'author' | 'contributor';
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
