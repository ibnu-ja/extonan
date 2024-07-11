import { PageProps as InertiaPageProps } from '@inertiajs/core'
import { AxiosInstance } from 'axios'
import { route as ziggyRoute } from 'ziggy-js'
import { PageProps as AppPageProps } from './'

declare global {
  const route: typeof ziggyRoute
  interface Window {
    axios: AxiosInstance
  }
}

declare module 'vue' {
  interface ComponentCustomProperties {
    route: ziggyRoute
  }
}

declare module '@inertiajs/core' {
  interface PageProps extends InertiaPageProps, AppPageProps {}
}
