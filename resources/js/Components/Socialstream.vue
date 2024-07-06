<script setup lang="ts">
import { SocialstreamProviders } from '@/types'
import { siBitbucket, siFacebook, siGithub, siGitlab, siGoogle, siLinkedin, siSlack, siX } from 'simple-icons'

// {"id":"github","name":"GitHub","buttonLabel":"GitHub"}

withDefaults(
  defineProps<{
    prompt: string
    error: string
    providers: SocialstreamProviders[]
  }>(), {
    prompt: 'Or Login Via',
  },
)

const providerIcons = [
  {
    id: 'bitbucket',
    icon: siBitbucket,
  },
  {
    id: 'facebook',
    icon: siFacebook,
  },
  {
    id: 'github',
    icon: siGithub,
  },
  {
    id: 'gitlab',
    icon: siGitlab,
  },
  {
    id: 'google',
    icon: siGoogle,
  },
  {
    id: 'facebook',
    icon: siFacebook,
  },
  {
    id: 'linkedin',
    icon: siLinkedin,
  },
  {
    id: 'linkedin-openid',
    icon: siLinkedin,
  },
  {
    id: 'slack',
    icon: siSlack,
  },
  {
    id: 'twitter',
    icon: siX,
  },
  {
    id: 'twitter-oauth-2',
    icon: siX,
  },
]

const getIcon = (id: string) => {
  const item = providerIcons.find(item => item.id == id)
  return item?.icon?.path
}
</script>

<template>
  <div class="d-flex justify-center align-center my-4">
    <v-divider />
    <div
      class="mx-2"
      style="min-width: fit-content;"
    >
      {{ prompt }}
    </div>
    <v-divider />
  </div>

  <div class="d-flex flex-row flex-warp justify-center gap-4">
    <v-btn
      v-for="provider in providers"
      :key="provider.id"
      block
      variant="outlined"
      :text="provider.buttonLabel"
      :prepend-icon="getIcon(provider.id)"
      :href="route('oauth.redirect', provider.id)"
    />
  </div>
</template>
