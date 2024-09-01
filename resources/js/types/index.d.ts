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

type ApiToken = {
  id: number
  tokenable_type: string
  tokenable_id: number
  name: string
  abilities: string[]
  last_used_at: string | null
  expires_at: string | null
  created_at: string | null
  updated_at: string | null
  last_used_ago: string | null
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
    user: User | null
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
  canLogin: boolean
  canRegister: boolean
  appVersion: string
  appCommitHash: string
  appBranch: string
  appGitOriginRepo
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

// Define TypeScript types for the GraphQL response
export type GraphQLResponse<T> = {
  data: T
  errors?: never[]
}

type Link = {
  url: string | null
  label: string
  active: boolean
}

export type Media = {
  id: number
  disk: string
  directory: string
  filename: string
  extension: string
  mime_type: string
  aggregate_type: string
  size: number
  created_at: Date
  updated_at: Date
  variant_name: null | number
  original_media_id: null | number
  alt: string | null
}

export type PaginatedResponse<T> = {
  current_page: number
  data: T[]
  first_page_url: string
  from: number
  last_page: number
  last_page_url: string
  links: Link[]
  next_page_url: string | null
  path: string
  per_page: number
  prev_page_url: string | null
  to: number
  total: number
}

export type Permissions = {
  update: boolean
  delete: boolean
  publish: boolean
}

export type Author = {
  created_at: Date | null
  current_team_id: null
  email: string
  email_verified_at: null | string
  id: number
  name: string | null
  profile_photo_path: string | null
  two_factor_confirmed_at: Date | null
  updated_at: Date | null
}

export type Post = {
  author: Author
  author_id: number
  created_at: Date | string | null
  updated_at: Date | string | null
  published_at: Date | string | null
  uuid: string | null
  is_published: boolean
  is_current: boolean
  publisher_type: string | null
  publisher_id: number | null
  can: Permissions
}
