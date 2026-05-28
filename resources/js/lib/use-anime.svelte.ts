import { page } from '@inertiajs/svelte';

export type LabelValue = {
    key: string;
    value: string;
};

export type AnimeFilterState = {
    title: string;
    genreIn: string[];
    genreNotIn: string[];
    tagIn: string[];
    tagNotIn: string[];
    seasonIn: string[];
    seasonNotIn: string[];
};

export function buildFilterQuery(
    filters: AnimeFilterState,
): Record<string, unknown> {
    const query: Record<string, unknown> = {};

    if (filters.title) {
        query.title = filters.title;
    }

    if (filters.genreIn.length > 0) {
        query.genre_in = filters.genreIn;
    }

    if (filters.genreNotIn.length > 0) {
        query.genre_not_in = filters.genreNotIn;
    }

    if (filters.tagIn.length > 0) {
        query.tag_in = filters.tagIn;
    }

    if (filters.tagNotIn.length > 0) {
        query.tag_not_in = filters.tagNotIn;
    }

    if (filters.seasonIn.length > 0) {
        query.season_in = filters.seasonIn;
    }

    if (filters.seasonNotIn.length > 0) {
        query.season_not_in = filters.seasonNotIn;
    }

    return query;
}

export function useAnime() {
    const genres = $derived((page.props.genres as LabelValue[]) ?? []);
    const tags = $derived((page.props.tags as LabelValue[]) ?? []);
    const seasons = $derived((page.props.seasons as string[]) ?? []);

    return {
        get genres() {
            return genres;
        },
        get tags() {
            return tags;
        },
        get seasons() {
            return seasons;
        },
        buildFilterQuery,
    };
}
