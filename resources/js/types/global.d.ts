import { PageProps as InertiaPageProps } from '@inertiajs/core'
import { route as ziggyRoute } from 'ziggy-js'
import { PageProps as AppPageProps } from './'

// declare global {
//   const route: typeof ziggyRoute
//   const axios: AxiosStatic
//
//   interface Window {
//     axios: AxiosStatic
//   }
// }

declare module 'vue' {
  interface ComponentCustomProperties {
    route: ziggyRoute
  }
}

declare module '@inertiajs/core' {
  interface PageProps extends InertiaPageProps, AppPageProps {
  }
}
