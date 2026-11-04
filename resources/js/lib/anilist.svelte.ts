type MediaApiResponse<T> = {
    Media: T;
};

type GraphQLResponse<T> = {
    data: T;
    errors?: { message: string }[];
};

export async function animeApi(
    id: number,
    useMalId = false,
    query?: string,
): Promise<App.Data.Anilist.AnilistMediaData | null> {
    if (!query) {
        console.error('anilistQuery not provided');

        return null;
    }

    const variables = useMalId ? { idMal: id } : { id };

    try {
        const response = await fetch('https://graphql.anilist.co/', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            body: JSON.stringify({ query, variables }),
        });

        if (!response.ok) {
            const text = await response.text();

            throw new Error(`AniList API error (${response.status}): ${text}`);
        }

        const json: GraphQLResponse<
            MediaApiResponse<App.Data.Anilist.AnilistMediaData>
        > = await response.json();

        if (json.errors) {
            throw new Error(
                'AniList GraphQL errors: ' + JSON.stringify(json.errors),
            );
        }

        return json.data.Media;
    } catch (error) {
        console.error('AniList fetch failed:', error);

        return null;
    }
}
