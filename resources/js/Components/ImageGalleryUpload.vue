<template>
  <v-card>
    <!-- TODO: data from VGMDB/URL -->
    <!-- Preview IMG -->
    <v-dialog
      v-model="modal"
      overlay-opacity="80"
    >
      <div class="relative">
        <v-carousel
          v-model="previewIndex"
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
            v-for="n in previewUrls"
            :key="n"
            :contain="contain"
            :src="n"
          >
            <!-- TODO: image name/other meta edit -->
          </v-carousel-item>
        </v-carousel>
      </div>
    </v-dialog>
    <!-- server images -->
    <v-dialog
      v-if="onServerFiles"
      v-model="modal2"
      overlay-opacity="80"
    >
      <div class="relative">
        <v-carousel
          v-model="serverIndex"
          height="90vh"
          width="100%"
          :hide-delimiters="delimit"
        >
          <v-btn
            icon
            large
            primary
            class="top-right ma-2 ma-md-3"
            @click="modal2 = false"
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
            v-for="file in onServerFiles"
            :key="file.id"
            :contain="contain"
            :src="file.original_url"
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
    </v-toolbar>
    <v-card-text>
      <v-row dense>
        <v-col cols="12">
          <v-file-input
            v-bind="$attrs"
            ref="input"
            label="New Uploads"
            accept="image/*"
            @change="change"
          />
        </v-col>
        <v-col
          v-for="(url, index) in previewUrls"
          :key="url"
          class="relative"
          cols="4"
        >
          <v-hover v-slot="{ hover }">
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
        <template v-if="onServerFiles">
          <v-col cols="12">
            <v-row
              no-gutters
              align="center"
            >
              <v-divider class="ml-8 mr-4" />
              <h5 class="text-center">
                Existing Files
              </h5>
              <v-divider class="mr-8 ml-4" />
            </v-row>
          </v-col>
          <v-col
            v-for="(file, index) in onServerFiles"
            :key="file.id"
            class="relative"
            cols="4"
          >
            <v-hover v-slot="{ hover }">
              <v-img
                :src="file.original_url"
                aspect-ratio="1"
                class="relative"
                :class="{ 'on-hover': hover }"
                @click="showModalUploaded(index)"
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
            <v-btn
              class="bottom-left"
              dark
              fab
              small
              color="error"
              @click="remove(file)"
            >
              <v-icon>
                mdi-delete
              </v-icon>
            </v-btn>
          </v-col>
        </template>
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
      modal2: false,
      previewIndex: null,
      serverIndex: null,
      contain: true,
      delimit: true,
      dragover: false,
      previewUrls: []
    }
  },
  methods: {
    reset () {
      this.previewUrls = []
      this.uploadedFiles = []
    },
    remove (file) {
      this.$emit('remove', file)
    },
    click () {
      return this.$refs.file.click()
    },
    showModal (img) {
      this.modal = true
      this.previewIndex = img
    },
    showModalUploaded (img) {
      this.modal2 = true
      this.serverIndex = img
    },
    change (e) {
      this.$emit('change', e)
      this.reset()
      Array.from(e).forEach((element) => {
        this.previewUrls.push(URL.createObjectURL(element))
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
