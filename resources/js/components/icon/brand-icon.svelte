<script lang="ts">
    import type { SimpleIcon } from 'simple-icons';
    import type { SVGAttributes } from 'svelte/elements';

    interface Props extends SVGAttributes<SVGSVGElement> {
        icon: SimpleIcon;
        class?: string;
    }

    let { icon, class: className = 'size-4', ...restProps }: Props = $props();
</script>

<svg
    role="img"
    viewBox="0 0 24 24"
    fill="currentColor"
    class={className}
    aria-label={icon.title}
    {...restProps}
>
    <path d={icon.path} />
</svg>
