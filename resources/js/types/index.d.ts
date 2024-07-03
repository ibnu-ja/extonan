import { Config } from 'ziggy-js'

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
}
