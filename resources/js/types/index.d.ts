type SocialstreamProviders = {
  id: string
  name: string
  buttonLabel: string
}

type ConnectedAccount = {
  id: number
  provider: string
  avatar_path?: string
  created_at: string
}

type Socialstream = {
  show: boolean
  prompt: string
  providers: SocialstreamProviders[]
  hasPassword: boolean
  connectedAccounts: ConnectedAccount[]
}

type SessionAgent = {
  is_desktop: boolean
  platform: string
  browser: string
}

type UserSession = {
  agent: SessionAgent
  ip_address: string
  is_current_device: boolean
  last_active: string
}

export interface User {
  connected_accounts?: ConnectedAccount & {
    user_id: number
    provider_id: string
    name: string | null
    nickname: string | null
    email: string | null
    telephone: string | null
    token: string | null
    secret: string | null
    refresh_token: string | null
    expires_at: string | null
    updated_at: string | null
  }
  created_at: string
  current_team_id: string | null
  profile_photo_path: string | null
  two_factor_confirmed_at: string | null
  two_factor_enabled: boolean
  id: number
  name: string
  email: string
  email_verified_at: string | null
  profile_photo_url: string | null
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
  auth: {
    user: User
  }
  jetstream: {
    canCreateTeams?: boolean
    canManageTwoFactorAuthentication?: boolean
    canUpdatePassword?: boolean
    canUpdateProfileInformation?: boolean
    flash: { [key: string]: string | string[] | unknown }
    hasAccountDeletionFeatures?: boolean
    hasEmailVerification: boolean
    hasApiFeatures?: boolean
    hasTeamFeatures?: boolean
    hasTermsAndPrivacyPolicyFeature?: boolean
    managesProfilePhotos?: boolean
  }
  ziggy: Config & { location: string }
  socialstream: Socialstream
  sessions: UserSession[]
}

type LinkProps = {
  href: string | undefined
  replace: boolean | undefined
  to: RouteLocationRaw | undefined
  exact: boolean | undefined
}

type BreadcrumbItem = (string | (Partial<LinkProps> & { title: string, disabled: boolean }))
