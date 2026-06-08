<script lang="ts">
    import AlertCircle from 'lucide-svelte/icons/alert-circle';
    import { store as mediaStore } from '@/actions/App/Http/Controllers/MediaController';
    import * as Alert from '@/components/ui/alert';
    import { Button } from '@/components/ui/button';
    import DropZone from './drop-zone.svelte';
    import UploadProgress from './upload-progress.svelte';

    let progress = $state(0);
    let uploading = $state(false);
    let files = $state<File[]>([]);
    let inputEl: HTMLInputElement | undefined = $state();
    let errors = $state<string[]>([]);

    let {
        multiple = false,
        onuploaded,
    }: {
        multiple?: boolean;
        onuploaded?: (media: App.Data.MediaData[]) => void;
    } = $props();

    function onInputChange(e: Event) {
        const target = e.target as HTMLInputElement;

        if (target.files) {
            addFiles(target.files);
            target.value = '';
        }
    }

    function selectNewFile() {
        inputEl?.click();
    }

    async function addFiles(newFiles: FileList | File) {
        try {
            errors = [];
            uploading = true;
            const formData = new FormData();

            if (newFiles instanceof FileList) {
                for (let i = 0; i < newFiles.length; i++) {
                    files.push(newFiles[i]);
                    formData.append('media[]', newFiles[i]);
                }
            } else {
                files = [newFiles];
                formData.append('media[]', newFiles);
            }

            const xhr = new XMLHttpRequest();
            xhr.open('POST', mediaStore.url());
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
                xhr.send(formData);
            });

            uploading = false;
            files = [];
        } catch (error) {
            if (error instanceof Error && !errors.length) {
                errors = [error.message];
            }
        } finally {
            uploading = false;
        }
    }
</script>

{#if uploading}
    <UploadProgress {progress} length={files.length} />
{:else}
    <DropZone {multiple} onFilesDropped={addFiles}>
        {#snippet child({ active })}
            <div class="text-center">
                <input
                    bind:this={inputEl}
                    type="file"
                    {multiple}
                    class="hidden"
                    onchange={onInputChange}
                />
                <h4 class="font-heading text-lg font-semibold">
                    Drop File(s) to Upload
                </h4>
                {#if !active}
                    <p class="text-xs text-muted-foreground mb-3">or</p>
                    <Button variant="outline" size="sm" onclick={selectNewFile}>
                        Select File(s)
                    </Button>
                {/if}
            </div>
        {/snippet}
    </DropZone>
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
