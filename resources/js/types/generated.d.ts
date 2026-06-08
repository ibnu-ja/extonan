declare namespace App {
    namespace Data {
        export type AnimeSummaryData = {
            id: number;
            title: Record<string, string | null>;
            slug: Record<string, string | null>;
            genres: string[];
            bannerImage: string | null;
            coverImage: App.Data.CoverImageData;
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
            animeId: number;
            permissions: App.Data.PermissionsData;
        };
        export type LabelValue = {
            key: string;
            value: string;
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
            permissions: App.Data.PermissionsData;
        };
        export type PaginatedCollection<TValue> = {
            data: TValue[];
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
        export type TagItem = {
            id: number;
            name: string;
            isAdult: boolean;
        };
        export type UserSummaryData = {
            id: number;
            name: string | null;
            avatar: string | null;
        };
        namespace Anilist {
            export type AnilistMediaData = {
                id: number | null;
                idMal: number | null;
                coverImage: App.Data.Anilist.CoverImageData | null;
                title: App.Data.Anilist.TitleData | null;
                startDate: App.Data.Anilist.FuzzyDateData | null;
                endDate: App.Data.Anilist.FuzzyDateData | null;
                episodes: number | null;
                description: string | null;
                bannerImage: string | null;
                season: App.Enums.Season | null;
                seasonYear: number | null;
                seasonInt: number | null;
                genres: string[];
                tags: App.Data.Anilist.TagData[];
                studios: App.Data.Anilist.StudioConnectionData | null;
                characters: App.Data.Anilist.CharacterConnectionData | null;
            };
            export type CharacterConnectionData = {
                edges: App.Data.Anilist.CharacterEdgeData[];
            };
            export type CharacterData = {
                id: number;
                name: App.Data.Anilist.CharacterNameData | null;
                image: App.Data.Anilist.CharacterImageData | null;
            };
            export type CharacterEdgeData = {
                node: App.Data.Anilist.CharacterData | null;
                role: App.Enums.CharacterRole | null;
                voiceActors: App.Data.Anilist.StaffData[];
            };
            export type CharacterImageData = {
                large: string | null;
                medium: string | null;
            };
            export type CharacterNameData = {
                first: string | null;
                middle: string | null;
                last: string | null;
                full: string | null;
                native: string | null;
            };
            export type CoverImageData = {
                extraLarge: string | null;
                large: string | null;
                medium: string | null;
                color: string | null;
            };
            export type FuzzyDateData = {
                year: number | null;
                month: number | null;
                day: number | null;
            };
            export type StaffData = {
                id: number;
                name: App.Data.Anilist.StaffNameData | null;
                image: App.Data.Anilist.StaffImageData | null;
                languageV2: string | null;
            };
            export type StaffImageData = {
                large: string | null;
                medium: string | null;
            };
            export type StaffNameData = {
                first: string | null;
                middle: string | null;
                last: string | null;
                full: string | null;
                native: string | null;
            };
            export type StudioConnectionData = {
                edges: App.Data.Anilist.StudioEdgeData[];
            };
            export type StudioData = {
                id: number;
                name: string;
                isAnimationStudio: boolean;
            };
            export type StudioEdgeData = {
                node: App.Data.Anilist.StudioData | null;
                isMain: boolean;
            };
            export type TagData = {
                id: number;
                name: string;
                rank: number | null;
                isAdult: boolean | null;
                category: string | null;
                isMediaSpoiler: boolean | null;
                isGeneralSpoiler: boolean | null;
                description: string | null;
            };
            export type TitleData = {
                romaji: string | null;
                english: string | null;
                native: string | null;
            };
        }
        namespace Anime {
            export type AnimeAZResponse = {
                items: App.Data.Anime.AnimeListItemData[];
                canCreate: boolean;
            };
            export type AnimeCreateResponse = {
                anime: App.Data.Anime.AnimeFormData | null;
                genres: App.Data.LabelValue[];
                tags: App.Data.TagItem[];
                seasons: App.Enums.Season[];
                anilistQuery: string;
            };
            export type AnimeFilterData = {
                genreIn: string[];
                genreNotIn: string[];
                tagIn: string[];
                tagNotIn: string[];
                seasonIn: string[];
                seasonNotIn: string[];
                title: string | null;
                isPublished: boolean | null;
            };
            export type AnimeFormData = {
                id: number | null;
                title: Record<string, string | null>;
                description: Record<string, string | null>;
                anilistId: number | null;
                metadata: App.Data.Anilist.AnilistMediaData | null;
                isPublished: boolean;
                canPublish: boolean;
            };
            export type AnimeIndexRequest = {
                filter: App.Data.Anime.AnimeFilterData;
                sort: string | null;
                perPage: number | null;
            };
            export type AnimeIndexResponse = {
                anime: App.Data.PaginatedCollection<App.Data.Anime.AnimeListItemData>;
                seasons: string[];
                genres: App.Data.LabelValue[];
                tags: App.Data.TagItem[];
                sortOptions: App.Data.LabelValue[];
                perPageValues: number[];
            };
            export type AnimeListItemData = {
                id: number;
                title: Record<string, string | null>;
                slug: Record<string, string | null>;
                coverImage: App.Data.CoverImageData;
                isPublished: boolean;
                permissions: App.Data.PermissionsData;
            };
            export type AnimeShowResponse = {
                anime: App.Data.Anime.AnimeListItemData;
                episodes: App.Data.Anime.EpisodeShowData[];
                canCreateEpisode: boolean;
                metadata: App.Data.Anilist.AnilistMediaData | null;
            };
            export type AnimeStoreData = {
                title: Record<string, string | null>;
                description: Record<string, string | null>;
                anilistId: number | null;
                metadata: App.Data.Anilist.AnilistMediaData | null;
                isPublished: boolean;
            };
            export type EpisodeListItemData = {
                id: number;
                title: Record<string, string | null>;
                slug: Record<string, string | null>;
                epNo: string | null;
                thumbnail: App.Data.CoverImageData | null;
                publishedAt: string | null;
            };
            export type EpisodeShowData = {
                id: number;
                title: Record<string, string | null>;
                epNo: string | null;
                postType: string;
                thumbnail: App.Data.CoverImageData | null;
                author: App.Data.UserSummaryData | null;
                publishedAt: string | null;
                isPublished: boolean;
                permissions: App.Data.PermissionsData;
            };
            export type PostShowData = {
                id: number;
                title: Record<string, string | null>;
                description: Record<string, string | null>;
                slug: Record<string, string | null>;
                epNo: string | null;
                postType: string;
                thumbnail: App.Data.CoverImageData | null;
                author: App.Data.UserSummaryData | null;
                publishedAt: string | null;
                isPublished: boolean;
                permissions: App.Data.PermissionsData;
                links: App.Data.Anime.ResourceData[];
                saluran: App.Data.Anime.ResourceData[];
                embed: App.Data.Anime.ResourceData | null;
            };
            export type PostShowResponse = {
                anime: App.Data.Anime.AnimeListItemData;
                episodes: App.Data.Anime.EpisodeListItemData[];
                post: App.Data.Anime.PostShowData;
            };
            export type ResourceData = {
                id: number;
                name: string;
                type: App.Enums.ResourceType;
                value: Array<any>;
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
        export type CharacterRole = 'MAIN' | 'SUPPORTING' | 'BACKGROUND';
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
        export type ResourceType = 'link' | 'saluran' | 'embed';
        export type Role = 'admin' | 'editor' | 'author' | 'contributor';
        export type Season = 'WINTER' | 'SPRING' | 'SUMMER' | 'FALL';
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
