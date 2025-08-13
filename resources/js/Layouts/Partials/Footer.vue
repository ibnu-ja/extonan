<script lang="ts" setup>

import { siDiscord, siFacebook, siGithub } from 'simple-icons'
import { usePage } from '@inertiajs/vue3'

const page = usePage()

const links = [
  {
    url: 'https://facebook.com/extonan',
    target: 'facebook_extonan',
    icon: siFacebook.path,
  },
  {
    url: 'https://discord.extonan.id',
    target: 'discord_extonan',
    icon: siDiscord.path,
  }, {
    url: 'https://github.com/shinrai',
    target: 'github_shinrai',
    icon: siGithub.path,
  },
]

const branchURL = `${page.props.appGitOriginRepo.replace(/\.git$/, '')}/tree/${page.props.appBranch}`
const commitURL = `${page.props.appGitOriginRepo.replace(/\.git$/, '')}/commit/${page.props.appCommitHash}`
</script>

<template>
  <v-footer class="text-center flex flex-col">
    <div>
      <v-btn
        v-for="link in links"
        :key="link.url"
        class="mx-4"
        :icon="link.icon"
        variant="text"
      />
    </div>
    <v-divider />
    <div class="pt-0">
      <p class="text-center">
        Running extonan Web <b class="font-weight-bold">{{ page.props.appVersion }}</b> on
        branch <a
          target="shinrai_branch"
          :href="branchURL"
        >{{ page.props.appBranch }}</a>
        (<a
          target="shinrai_commit_hash"
          :href="commitURL"
        >{{ page.props.appCommitHash }}</a>)
      </p>
      <p class="text-center">
        Copyright &copy; {{ new Date().getFullYear() }} IJI All Rights Reserved.
      </p>
    </div>
  </v-footer>
</template>
