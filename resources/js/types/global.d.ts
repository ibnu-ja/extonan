import { PageProps as InertiaPageProps } from '@inertiajs/core'
import { AxiosInstance } from 'axios'
import { PageProps as AppPageProps, Route } from './'

declare global {
  // const route: typeof Route
  interface Window {
    axios: AxiosInstance
  }
}

declare module 'vue' {
  interface ComponentCustomProperties {
    route: Route
  }
}

declare module '@inertiajs/core' {
  interface PageProps extends InertiaPageProps, AppPageProps {}
}
