<script lang="ts">
    import AlertCircle from 'lucide-svelte/icons/alert-circle';
    import { store as mediaStore } from '@/actions/App/Http/Controllers/MediaController';
    import * as Alert from '@/components/ui/alert';
    import { Button } from '@/components/ui/button';
    import { Label } from '@/components/ui/label';
    import { Spinner } from '@/components/ui/spinner';
    import { Textarea } from '@/components/ui/textarea';
    import UploadProgress from './upload-progress.svelte';

    let urls = $state('');
    let progress = $state(0);
    let processing = $state(false);
    let errors = $state<string[]>([]);
    let splitted = $state<string[]>([]);

    let {
        onuploaded,
    }: {
        onuploaded?: (media: App.Data.MediaData[]) => void;
    } = $props();

    function checkURL(url: string) {
        return /\.(jpeg|jpg|gif|png|webp|avif)$/i.test(url);
    }

    function isValidImageLink(urlToCheck: string) {
        try {
            const url = new URL(urlToCheck);

            if (!checkURL(urlToCheck)) {
                return false;
            }

            return url.protocol === 'http:' || url.protocol === 'https:';
        } catch {
            return false;
        }
    }

    async function upload() {
        try {
            errors = [];
            processing = true;
            splitted = urls.split(/\r?\n|\r|\n/g).filter((url) => url.trim());
            const validated = splitted.filter((url) => isValidImageLink(url));

            if (validated.length === 0) {
                errors = ["There's no valid Image URL"];

                return;
            }

            const body: App.Data.MediaStoreData = { url: validated, media: null };

            const xhr = new XMLHttpRequest();
            xhr.open('POST', mediaStore.url());
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.upload.onprogress = (e) => {
                if (e.lengthComputable) {
                    progress = Math.round((e.loaded * 100) / e.total);
                }
            };

            await new Promise<void>((resolve, reject) => {
                xhr.onload = () => {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        const response = JSON.parse(xhr.responseText);
                        onuploaded?.(response.data ?? []);
                        resolve();
                    } else if (xhr.status === 422) {
                        const response = JSON.parse(xhr.responseText);
                        const messages: string[] = [];

                        for (const key in response.errors) {
                            messages.push(...response.errors[key]);
                        }

                        errors = messages;
                        reject(new Error('Validation failed'));
                    } else {
                        reject(new Error(xhr.statusText || 'Upload failed'));
                    }
                };
                xhr.onerror = () => reject(new Error('Network error'));
                xhr.send(JSON.stringify(body));
            });
        } catch (error) {
            if (error instanceof Error && !errors.length) {
                errors = [error.message];
            }
        } finally {
            urls = '';
            splitted = [];
            progress = 0;
            processing = false;
        }
    }
</script>

{#if processing}
    <UploadProgress {progress} length={splitted.length} />
{:else}
    <form onsubmit={upload} class="space-y-4">
        <div class="grid gap-2">
            <Label for="remote-urls">One URL per line</Label>
            <Textarea
                id="remote-urls"
                bind:value={urls}
                rows={4}
                disabled={processing}
                placeholder="https://example.com/image.jpg"
            />
        </div>
        <Button type="submit" disabled={processing}>
            {#if processing}
                <Spinner class="mr-2 size-4" />
                Uploading...
            {:else}
                Upload
            {/if}
        </Button>
    </form>
{/if}

{#if errors.length > 0}
    <Alert.Root variant="destructive">
        <AlertCircle />
        <Alert.Title>Upload failed</Alert.Title>
        <Alert.Description>
            <ul class="list-inside list-disc text-sm">
                {#each errors as error (error)}
                    <li>{error}</li>
                {/each}
            </ul>
        </Alert.Description>
    </Alert.Root>
{/if}
