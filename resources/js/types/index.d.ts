import { Config, route as ziggyRoute } from 'ziggy-js'

type SocialstreamProviders = {
  id: string
  name: string
  buttonLabel: string
}

type Socialstream = {
  show: boolean
  prompt: string
  providers: SocialstreamProviders[]
  hasPassword: false
  connectedAccounts: []
}
export interface User {
  id: number
  name: string
  email: string
  email_verified_at: string
  profile_photo_url?: string
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
  auth: {
    user: User
  }
  jetstream?: {
    canCreateTeams?: boolean
    canManageTwoFactorAuthentication?: boolean
    canUpdatePassword?: boolean
    canUpdateProfileInformation?: boolean
    flash: { [key: string]: string | string[] | unknown }
    hasAccountDeletionFeatures?: boolean
    hasApiFeatures?: boolean
    hasTeamFeatures?: boolean
    hasTermsAndPrivacyPolicyFeature?: boolean
    managesProfilePhotos?: boolean
  }
  ziggy: Config & { location: string }
  socialstream: Socialstream
}

type Route = typeof ziggyRoute

type LinkProps = {
  href: string | undefined
  replace: boolean | undefined
  to: RouteLocationRaw | undefined
  exact: boolean | undefined
}

type BreadcrumbItem = (string | (Partial<LinkProps> & { title: string, disabled: boolean }))
