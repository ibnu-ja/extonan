import { inertia } from '@inertiajs/svelte';
import type { Attachment } from 'svelte/attachments';
import type { LinkComponentBaseProps } from '@inertiajs/core';

export const inertiaNav: Attachment = (element) => {
  const href = element.getAttribute('href');
  if (!href) return;

  const params: LinkComponentBaseProps = { href };

  const preserveScroll = element.getAttribute('data-preserve-scroll');
  if (preserveScroll) params.preserveScroll = preserveScroll === 'true';

  const preserveState = element.getAttribute('data-preserve-state');
  if (preserveState) params.preserveState = preserveState === 'true';

  const replace = element.getAttribute('data-replace');
  if (replace) params.replace = replace === 'true';

  const viewTransition = element.getAttribute('data-view-transition');
  if (viewTransition) params.viewTransition = viewTransition === 'true';

  const only = element.getAttribute('data-only');
  if (only) params.only = only.split(',').map(s => s.trim());

  const except = element.getAttribute('data-except');
  if (except) params.except = except.split(',').map(s => s.trim());

  const headers = element.getAttribute('data-headers');
  if (headers) {
    try {
      params.headers = JSON.parse(headers);
    } catch (e) {
      console.error('[inertiaNav] Invalid JSON in data-headers:', e);
    }
  }

  const data = element.getAttribute('data-data');
  if (data) {
    try {
      params.data = JSON.parse(data);
    } catch (e) {
      console.error('[inertiaNav] Invalid JSON in data-data:', e);
    }
  }

  const result = inertia(element as HTMLElement, params);

  return () => result?.destroy?.();
};
