<template>
  <v-card>
    <!-- TODO: data from VGMDB/URL -->
    <v-dialog
      v-model="modal"
      overlay-opacity="80"
    >
      <div class="relative">
        <v-carousel
          v-model="img"
          height="90vh"
          :hide-delimiters="delimit"
        >
          <v-btn
            icon
            large
            primary
            class="top-right ma-2 ma-md-3"
            @click="modal = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
          <v-btn
            icon
            large
            primary
            class="top-left ma-2 ma-md-3"
            @click="contain = !contain"
          >
            <v-icon>mdi-fullscreen</v-icon>
          </v-btn>
          <v-btn
            icon
            large
            primary
            class="bottom-left ma-1 ml-md-3"
            @click="delimit = !delimit"
          >
            <v-icon v-text="!delimit ? 'mdi-chevron-down' : 'mdi-chevron-up'" />
          </v-btn>
          <v-carousel-item
            v-for="n in urls"
            :key="n"
            :contain="contain"
            :src="n"
          >
            <!-- TODO: image name/other meta edit -->
          </v-carousel-item>
        </v-carousel>
      </div>
    </v-dialog>
    <v-toolbar
      dense
      flat
    >
      <v-toolbar-title>
        <h4>Images Gallery</h4>
      </v-toolbar-title>
      <!-- <v-spacer />
      <v-btn @click="reset">
        Reset
      </v-btn> -->
    </v-toolbar>
    <v-card-text>
      <v-row dense>
        <v-col cols="12">
          <v-file-input
            v-bind="$attrs"
            ref="input"
            label="New Uploads"
            accept="image/*"
            @change="test"
          />
        </v-col>
        <v-col
          v-for="(url, index) in urls"
          :key="url"
          class="relative"
          cols="4"
        >
          <!-- eslint-disable -->
          <v-hover v-slot="{ hover }">
            <!-- eslint-enable -->
            <v-img
              :src="url"
              aspect-ratio="1"
              class="relative"
              :class="{ 'on-hover': hover }"
              @click="showModal(index)"
            >
              <template #placeholder>
                <v-row
                  class="fill-height ma-0"
                  align="center"
                  justify="center"
                >
                  <v-progress-circular
                    indeterminate
                    color="grey lighten-5"
                  />
                </v-row>
              </template>
            </v-img>
          </v-hover>
        </v-col>
        <!-- TODO remove file di server -->
        <v-col
          v-if="onServerFiles"
          cols="12"
        >
          <v-row
            align="center"
          >
            <v-divider class="ml-8 mr-4" />
            <h5 class="text-center">
              Existing Files
            </h5>
            <v-divider class="mr-8 ml-4" />
          </v-row>
          {{ onServerFiles }}
          <v-btn @click="remove" />
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  model: {
    prop: 'files',
    event: 'change'
  },
  props: {
    files: {
      type: [FileList, Array],
      default: null
    },
    onServerFiles: {
      type: [Array],
      default: null
    }
  },
  data () {
    return {
      modal: false,
      img: null,
      contain: true,
      delimit: true,
      dragover: false,
      urls: []
    }
  },
  methods: {
    reset () {
      this.urls = []
      this.uploadedFiles = []
    },
    remove () {
      this.$emit('remove', this.onServerFiles[0])
    },
    click () {
      return this.$refs.file.click()
    },
    showModal (img) {
      this.modal = true
      this.img = img
    },
    test (e) {
      this.$emit('change', e)
      this.reset()
      Array.from(e).forEach((element) => {
        this.urls.push(URL.createObjectURL(element))
      })
      // this.$emit('change', this.$event.target.files)
    }
  }
}
</script>

<style>
.on-hover {
  cursor: pointer;
}
.relative {
  position: relative;
}
.top {
  position: absolute;
  top: 0;
}
.top-right {
  position: absolute;
  top: 0;
  right: 0;
  z-index: 50;
}
.top-left {
  position: absolute;
  top: 0;
  left: 0;
  z-index: 50;
}
.bottom-right {
  position: absolute;
  bottom: 0;
  right: 0;
  z-index: 20;
}
.bottom-left {
  position: absolute;
  bottom: 0;
  left: 0;
  z-index: 20;
}
</style>
