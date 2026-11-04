import { page } from '@inertiajs/svelte';

export type LabelValue = {
    key: string;
    value: string;
};

export type AnimeFilterState = App.Data.Anime.AnimeFilterData;

export function buildFilterQuery(
    request: App.Data.Anime.AnimeIndexRequest,
): Record<string, unknown> {
    const query: Record<string, unknown> = {};

    if (request.sort) {
        query.sort = request.sort;
    }

    if (request.filter) {
        const filter: Record<string, unknown> = {};

        for (const [key, value] of Object.entries(request.filter)) {
            if (
                value == null ||
                value === '' ||
                (Array.isArray(value) && value.length === 0)
            ) {
                continue;
            }

            filter[key] = value;
        }

        if (Object.keys(filter).length > 0) {
            query.filter = filter;
        }
    }

    return query;
}

export function useAnime() {
    const genres = $derived((page.props.genres as LabelValue[]) ?? []);
    const rawTags = $derived((page.props.tags as App.Data.TagItem[]) ?? []);
    const seasons = $derived((page.props.seasons as string[]) ?? []);

    const tags = $derived(
        rawTags
            .filter((t) => !t.isAdult)
            .map(
                (t) =>
                    ({ key: String(t.id), value: t.name }) satisfies LabelValue,
            ),
    );

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
