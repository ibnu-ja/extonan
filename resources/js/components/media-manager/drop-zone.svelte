<script lang="ts">
    import { onMount } from 'svelte';
    import type { Snippet } from 'svelte';

    type Props = {
        multiple?: boolean;
        onFilesDropped?: (files: FileList) => void;
        children?: Snippet<[{ active: boolean }]>;
        child?: Snippet<[{ active: boolean }]>;
    };

    let { onFilesDropped, children, child }: Props = $props();

    let active = $state(false);
    let inActiveTimeout: ReturnType<typeof setTimeout>;

    function setActive() {
        active = true;
        clearTimeout(inActiveTimeout);
    }

    function setInactive() {
        inActiveTimeout = setTimeout(() => {
            active = false;
        }, 50);
    }

    function onDrop(e: DragEvent) {
        setInactive();

        if (e.dataTransfer?.files) {
            onFilesDropped?.(e.dataTransfer.files);
        }
    }

    function preventDefaults(e: Event) {
        e.preventDefault();
    }

    const events = ['dragenter', 'dragover', 'dragleave', 'drop'];

    onMount(() => {
        events.forEach((eventName) => {
            document.body.addEventListener(eventName, preventDefaults);
        });

        return () => {
            events.forEach((eventName) => {
                document.body.removeEventListener(eventName, preventDefaults);
            });
        };
    });
</script>

<!-- svelte-ignore a11y_no_static_element_interactions -->
<div
    class="border-2 border-dashed rounded-lg p-8 flex items-center justify-center transition-colors data-[active=true]:border-primary data-[active=true]:bg-muted"
    data-active={active}
    ondragenter={setActive}
    ondragover={setActive}
    ondragleave={setInactive}
    ondrop={onDrop}
>
    {#if child}
        {@render child({ active })}
    {:else if children}
        {@render children({ active })}
    {/if}
</div>
