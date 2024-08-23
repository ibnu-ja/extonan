<script lang="ts" setup>
import {
  ComponentOptionsBase,
  computed,
  ComputedOptions,
  CreateComponentPublicInstance,
  FunctionalComponent,
  h,
  MethodOptions,
  useAttrs,
  useSlots,
  VNode,
} from 'vue'

import {
  FormDataConvertible,
  mergeDataIntoQueryString,
  Method,
  PreserveStateOption,
  router,
  shouldIntercept,
} from '@inertiajs/core'
import { useBrowserLocation } from '@vueuse/core'

/* eslint-disable @typescript-eslint/no-explicit-any */
type CustomComponentType =
  (ComponentOptionsBase<any, any, any, ComputedOptions, MethodOptions, any, any, any, string, any> & ThisType<CreateComponentPublicInstance<any, any, any, ComputedOptions, MethodOptions, any, any, any, Readonly<any>>>)
  | FunctionalComponent<any, any>

/* eslint-enable */

interface ModifiedInertiaLinkProps {
  method?: Method
  replace?: boolean
  preserveScroll?: PreserveStateOption
  preserveState?: PreserveStateOption
  only?: Array<string>
  headers?: Record<string, string>
  errorBag?: string | null
  forceFormData?: boolean
  queryStringArrayFormat?: 'indices' | 'brackets'
  href?: string | null
  as?: string | CustomComponentType
  active?: boolean
  exactActive?: boolean
  data?: Record<string, FormDataConvertible>
}

const props = withDefaults(defineProps<ModifiedInertiaLinkProps>(), {
  href: null,
  method: 'get',
  data: () => ({}),
  as: 'a',
  preserveScroll: false,
  preserveState: false,
  replace: false,
  headers: () => ({}),
  errorBag: null,
  queryStringArrayFormat: () => ('brackets'),
  active: () => false,
  exactActive: () => false,
  boolean: () => false,
  only: () => ([]),
})

const slots = useSlots()
let as = props.as
const method = props.method

const attrs = useAttrs()
let component: string | VNode
const [href, data] = mergeDataIntoQueryString(props.method, props.href || '', props.data, props.queryStringArrayFormat)
const browserLocation = useBrowserLocation()

const computedActive = computed(() => {
  if (!browserLocation.value.pathname || props.href) {
    return false
  }
  const currentUrl = browserLocation.value.origin + browserLocation.value.pathname.replace(/\/$/, '')
  const hrefWithoutTrailingSlash = props.href.replace(/\/$/, '')
  if (props.active) {
    return true
  }
  if (props.exactActive) {
    // bad
    // return `${browserLocation.value.origin}${browserLocation.value.pathname}` === props.href
    return currentUrl === hrefWithoutTrailingSlash
  }
  return currentUrl.startsWith(props.href)
})

// vuetify-specific props

const onClick = (event: KeyboardEvent) => {
  if (shouldIntercept(event)) {
    event.preventDefault()
    router.visit(href, {
      data: data,
      method: method,
      replace: props.replace,
      preserveScroll: props.preserveScroll,
      preserveState: props.preserveState ?? method !== 'get',
      only: props.only,
      headers: props.headers,
      // @ts-expect-error TODO: Fix this
      onCancelToken: attrs.onCancelToken || (() => ({})),
      // @ts-expect-error TODO: Fix this
      onBefore: attrs.onBefore || (() => ({})),
      // @ts-expect-error TODO: Fix this
      onStart: attrs.onStart || (() => ({})),
      // @ts-expect-error TODO: Fix this
      onProgress: attrs.onProgress || (() => ({})),
      // @ts-expect-error TODO: Fix this
      onFinish: attrs.onFinish || (() => ({})),
      // @ts-expect-error TODO: Fix this
      onCancel: attrs.onCancel || (() => ({})),
      // @ts-expect-error TODO: Fix this
      onSuccess: attrs.onSuccess || (() => ({})),
      // @ts-expect-error TODO: Fix this
      onError: attrs.onError || (() => ({})),
    })
  }
}
if (typeof as === 'string') {
  as = as.toLowerCase()
  if (as === 'a' && method !== 'get') {
    console.warn(
      `Creating POST/PUT/PATCH/DELETE <a> links is discouraged as it causes "Open Link in New Tab/Window" accessibility issues.\n\nPlease specify a more appropriate element using the "as" attribute. For example:\n\n<LinkComponent href="${href}" method="${method}" as="button">...</LinkComponent>`,
    )
  }

  component = h(
    as,
    {
      active: computedActive.value,
      ...attrs,
      ...(as === 'a' ? { href } : {}),
      onClick,
    },
    slots,
  )
} else {
  component = h(
    as,
    {
      active: computedActive.value,
      ...attrs,
      href,
      onClick,
    },
    slots,
  )
}
</script>

<template>
  <component :is="component" />
</template>
