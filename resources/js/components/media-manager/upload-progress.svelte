<script lang="ts">
    import * as Card from '@/components/ui/card';
    import { Progress } from '@/components/ui/progress';

    let {
        length,
        progress,
    }: {
        length: number;
        progress: number;
    } = $props();
</script>

<Card.Root class="flex flex-col items-center justify-center gap-4 p-8">
    <p class="text-sm text-muted-foreground">
        Uploading {length} file(s)
    </p>
    <Progress value={progress} class="w-40 h-2" />
    <strong class="text-lg">{progress}%</strong>
</Card.Root>
