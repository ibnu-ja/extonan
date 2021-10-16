<template>
  <div>
    <v-row
      class="my-6"
      align="center"
    >
      <v-divider class="ml-8 mr-4" />
      <h5 class="text-center">
        OR
      </h5>
      <v-divider class="mr-8 ml-4" />
    </v-row>
    <v-btn
      v-if="$page.props.socialstream.providers.includes('google')"
      block
      outlined
      color="primary"
      class="my-2"
      :href="route('oauth.redirect', 'google')"
    >
      <v-icon left>
        mdi-google
      </v-icon>
      {{ action }} with Google
    </v-btn>
    <v-btn
      v-if="$page.props.socialstream.providers.includes('facebook')"
      block
      color="#3b5998"
      dark
      class="my-2"
      :href="route('oauth.redirect', 'facebook')"
    >
      <v-icon left>
        mdi-facebook
      </v-icon>
      {{ action }} with Facebook
    </v-btn>
    <v-btn
      v-if="$page.props.socialstream.providers.includes('github')"
      block
      color="#161b22"
      dark
      class="my-2"
      :href="route('oauth.redirect', 'github')"
    >
      <v-icon left>
        mdi-github
      </v-icon>
      {{ action }} with GitHub
    </v-btn>
    <v-btn
      v-if="$page.props.socialstream.providers.includes('twitter')"
      block
      color="#69ace8"
      dark
      class="my-2"
      :href="route('oauth.redirect', 'twitter')"
    >
      <v-icon left>
        mdi-twitter
      </v-icon>
      {{ action }} with Twitter
    </v-btn>
    <v-btn
      v-if="$page.props.socialstream.providers.includes('linkedin')"
      block
      color="#0077b5"
      dark
      class="my-2"
      :href="route('oauth.redirect', 'linkedin')"
    >
      <v-icon left>
        mdi-linkedin
      </v-icon>
      {{ action }} with LinkedIn
    </v-btn>
    <v-btn
      v-if="$page.props.socialstream.providers.includes('gitlab')"
      block
      color="#e2532a"
      dark
      class="my-2"
      :href="route('oauth.redirect', 'gitlab')"
    >
      <v-icon left>
        mdi-gitlab
      </v-icon>
      {{ action }} with GitLab
    </v-btn>
    <v-btn
      v-if="$page.props.socialstream.providers.includes('bitbucket')"
      block
      color="#0357d1"
      dark
      class="my-2"
      :href="route('oauth.redirect', 'bitbucket')"
    >
      <v-icon left>
        mdi-bitbucket
      </v-icon>
      {{ action }} with Bitbucket
    </v-btn>
  </div>
</template>

<script>
export default {
  props: {
    action: {
      type: String,
      default: 'Sign in'
    }
  }
}
</script>
