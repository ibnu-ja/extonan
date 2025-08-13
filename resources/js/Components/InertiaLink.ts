import {
  ComponentOptionsBase,
  computed,
  ComputedOptions,
  CreateComponentPublicInstance,
  defineComponent,
  FunctionalComponent,
  h,
  MethodOptions,
  PropType,
  useAttrs,
  useSlots,
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
export default defineComponent({
  props: {
    href: {
      type: String as PropType<string | null>,
      default: null,
    },
    method: {
      type: String as PropType<Method>,
      default: 'get',
    },
    data: {
      type: Object as PropType<Record<string, FormDataConvertible>>,
      default: () => ({}),
    },
    as: {
      type: [String, Object] as PropType<string | CustomComponentType>,
      default: 'a',
    },
    preserveScroll: {
      type: [Boolean, String] as PropType<PreserveStateOption>,
      default: false,
    },
    preserveState: {
      type: [Boolean, String] as PropType<PreserveStateOption>,
      default: false,
    },
    replace: {
      type: Boolean,
      default: false,
    },
    headers: {
      type: Object as PropType<Record<string, string>>,
      default: () => ({}),
    },
    errorBag: {
      type: String as PropType<string | null>,
      default: null,
    },
    queryStringArrayFormat: {
      type: String as PropType<'indices' | 'brackets'>,
      default: 'brackets',
    },
    active: {
      type: Boolean,
      default: false,
    },
    exactActive: {
      type: Boolean,
      default: false,
    },
    only: {
      type: Array as PropType<string[]>,
      default: () => ([]),
    },
  },
  setup(props) {
    const slots = useSlots()
    const attrs = useAttrs()
    // const browserLocation = useBrowserLocation()

    const [href, data] = mergeDataIntoQueryString(
      props.method,
      props.href || '',
      props.data,
      props.queryStringArrayFormat,
    )

    const browserLocation = useBrowserLocation()

    const computedActive = computed(() => {
      // if (!browserLocation.value.pathname || !props.href) {
      if (!browserLocation.value.pathname || !props.href) {
        return false
      }
      const currentUrl = browserLocation.value.origin + browserLocation.value.pathname.replace(/\/$/, '')
      const hrefWithoutTrailingSlash = props.href!.replace(/\/$/, '')
      if (props.active) {
        return true
      }
      if (props.exactActive) {
        // bad
        // return `${browserLocation.value.origin}${browserLocation.value.pathname}` === props.href
        return currentUrl === hrefWithoutTrailingSlash
      }
      return currentUrl.startsWith(props.href!)
    })

    const onClick = (event: Pick<MouseEvent, 'button' | 'altKey' | 'ctrlKey' | 'defaultPrevented' | 'target' | 'currentTarget' | 'metaKey' | 'shiftKey'>) => {
      if (shouldIntercept(event)) {
        // event.preventDefault()
        router.visit(href, {
          data,
          method: props.method,
          replace: props.replace,
          preserveScroll: props.preserveScroll,
          preserveState: props.preserveState ?? props.method !== 'get',
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

    return () => {
      let component

      if (typeof props.as === 'string') {
        const as = props.as.toLowerCase()

        if (as === 'a' && props.method !== 'get') {
          console.warn(
            `Creating POST/PUT/PATCH/DELETE <a> links is discouraged as it causes "Open Link in New Tab/Window" accessibility issues.\n\nPlease specify a more appropriate element using the "as" attribute. For example:\n\n<LinkComponent href="${href}" method="${props.method}" as="button">...</LinkComponent>`,
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
          props.as,
          {
            active: computedActive.value,
            ...attrs,
            href,
            onClick,
          },
          slots,
        )
      }
      return component
    }
  },
})
