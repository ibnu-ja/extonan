<template>
  <web-layout>
    <v-row
      justify="center"
      class="py-4 mb-10 pt-0"
      align="center"
    >
      <v-col
        cols="12"
        md="10"
        order="first"
        class="align-center pr-6 "
      >
        <h1
          class="text-h4 text-sm-h3 text-lg-h2 mt-xl-4 font-weight-black"
          v-text="title"
        />
        <h2
          class="text-h6 text-sm-h5 secondary--text mt-4 pr-6"
          v-text="description"
        />
        <v-btn
          x-large
          color="secondary"
          class="mt-6"
          @click="back()"
        >
          Go Back
        </v-btn>
      </v-col>
    </v-row>
  </web-layout>
</template>

<script>
import WebLayout from '@/Layouts/Web/Index.vue'
// import ValidationErrors from '@/Components/ValidationErrors'
// import SvgBusinessAnalytics from '@/Components/svg/BusinessAnalytics'

export default {
  components: { WebLayout },
  props: {
    // eslint-disable-next-line vue/require-default-prop
    status: Number
  },
  computed: {
    title () {
      return {
        503: '503: Service Unavailable',
        500: '500: Server Error',
        404: '404: Page Not Found',
        403: '403: Forbidden'
      }[this.status]
    },
    description () {
      return {
        503: 'Sorry, we are doing some maintenance. Please check back soon.',
        500: 'Whoops, something went wrong on our servers.',
        404: 'Sorry, the page you are looking for could not be found.',
        403: 'Sorry, you are forbidden from accessing this page.'
      }[this.status]
    }
  },
  methods: {
    back () {
      window.history.back()
    }
  }
}
</script>
