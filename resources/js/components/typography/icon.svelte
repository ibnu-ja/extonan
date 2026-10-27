<script lang="ts">
    import { Icon } from '@/components/icon';

    interface Props {
        name: string;
        class?: string;
        size?: number | string;
        color?: string;
        strokeWidth?: number | string;
        [key: string]: any;
    }

    let {
        name,
        class: className,
        ...restProps
    }: Props = $props();

    const lucideName = $derived.by(() => {
        const iconName = name.charAt(0).toUpperCase() + name.slice(1);

        return iconName.endsWith('Icon') ? iconName : `${iconName}Icon`;
    });
</script>

<Icon lucide={lucideName} class={className} {...restProps} />
