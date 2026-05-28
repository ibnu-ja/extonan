<script lang="ts">
    import type { Snippet } from "svelte";
    import type { ButtonAttrs, DivAttrs, AnchorAttrs } from "../misc/typing-utils";

    type ActionProps = ButtonAttrs | DivAttrs | AnchorAttrs;

    let {
        variant,
        children,
        image,
        headline,
        subhead,
        supporting,
        ...extra
    }: {
        variant: "elevated" | "filled" | "outlined";
        children?: Snippet;
        image?: {
            src: string;
            alt: string;
        };
        headline?: string;
        subhead?: string;
        supporting?: string;
    } & ActionProps = $props();
</script>

{#if extra.onclick}
    <button type="button" class="m3-container {variant} m3-layer" {...extra}>
        {#if image}
            <img src={image.src} alt={image.alt} loading="lazy" class="card-image"/>
        {/if}
        <div class="card-content">
            {#if headline}
                <div class="headline">{headline}</div>
            {/if}
            {#if subhead}
                <div class="subhead">{subhead}</div>
            {/if}
            {#if supporting}
                <div class="supporting">{supporting}</div>
            {/if}
            {#if children}
                {@render children()}
            {/if}
        </div>
    </button>
{:else if extra.href}
    <a class="m3-container {variant} m3-layer" {...extra}>
        {#if image}
            <img src={image.src} alt={image.alt} loading="lazy" class="card-image"/>
        {/if}
        <div class="card-content">
            {#if headline}
                <div class="headline">{headline}</div>
            {/if}
            {#if subhead}
                <div class="subhead">{subhead}</div>
            {/if}
            {#if supporting}
                <div class="supporting">{supporting}</div>
            {/if}
            {#if children}
                {@render children()}
            {/if}
        </div>
    </a>
{:else}
    <div class="m3-container {variant}" {...extra}>
        {#if image}
            <img src={image.src} alt={image.alt} loading="lazy" class="card-image"/>
        {/if}
        <div class="card-content">
            {#if headline}
                <div class="headline">{headline}</div>
            {/if}
            {#if subhead}
                <div class="subhead">{subhead}</div>
            {/if}
            {#if supporting}
                <div class="supporting">{supporting}</div>
            {/if}
            {#if children}
                {@render children()}
            {/if}
        </div>
    </div>
{/if}

<style>
    @layer tokens {
        :root {
            --m3-card-shape: var(--m3-shape-large);
        }
    }

    .m3-container {
        display: flex;
        flex-direction: column;
        padding: 0;
        border: none;
        border-radius: var(--m3-card-shape);
        background-color: var(--m3c-surface);
        --m3v-background: var(--m3c-surface);
        color: var(--m3c-on-surface);
        transition:
            border-radius var(--m3-easing-fast-spatial),
            box-shadow var(--m3-easing-fast);
    }

    button {
        text-align: inherit;
        font: inherit;
        letter-spacing: inherit;
        cursor: pointer;
    }
    @media (hover: hover) {
        button:hover {
            box-shadow: var(--m3-elevation-1);
        }
        button.elevated:hover {
            box-shadow: var(--m3-elevation-2);
        }
    }

    a {
        text-align: inherit;
        font: inherit;
        letter-spacing: inherit;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
    }
    @media (hover: hover) {
        a:hover {
            box-shadow: var(--m3-elevation-1);
        }
        a.elevated:hover {
            box-shadow: var(--m3-elevation-2);
        }
    }

    button:active,
    a:active {
        border-radius: var(--m3-shape-extra-large) !important;
    }

    .elevated {
        background-color: var(--m3c-surface-container-low);
        --m3v-background: var(--m3c-surface-container-low);
        box-shadow: var(--m3-elevation-1);
    }
    .filled {
        background-color: var(--m3c-surface-container-highest);
        --m3v-background: var(--m3c-surface-container-highest);
    }
    .outlined {
        border: solid 1px var(--m3c-outline-variant);
    }

    .card-image {
        width: 100%;
        height: auto;
        aspect-ratio: 16 / 9;
        object-fit: cover;
        margin: 0;
        border-radius: var(--m3-card-shape);
    }

    .card-content {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        padding: 0.25rem 0.75rem 0.75rem 0.75rem ;
    }

    .headline {
        font-family: var(--m3-font);
        font-size: 1.375rem;
        font-weight: 500;
        line-height: 1.636;
        letter-spacing: 0;
        color: var(--m3c-on-surface);
    }

    .subhead {
        font-family: var(--m3-font);
        font-size: 0.875rem;
        font-weight: 500;
        line-height: 1.25;
        letter-spacing: 0.4px;
        color: var(--m3c-on-surface-variant);
    }

    .supporting {
        font-family: var(--m3-font);
        font-size: 0.875rem;
        font-weight: 400;
        line-height: 1.25;
        letter-spacing: 0.4px;
        color: var(--m3c-on-surface-variant);
    }
</style>
