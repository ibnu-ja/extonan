<script>
import Forms from './Forms.vue'
import { sync } from 'vuex-pathify'
import DashLayout from '@/Layouts/Dash/Index.vue'
import { VBtn } from 'vuetify/lib'
import ValidationErrors from '@/Components/ValidationErrors.vue'
import ImageGalleryUpload from '@/Components/ImageGalleryUpload.vue'
// import { union } from 'lodash'

export default {
  name: 'DashboardAlbum',
  components: {
    DashLayout,
    ImageGalleryUpload,
    // eslint-disable-next-line vue/no-unused-components
    VBtn,
    ValidationErrors
  },
  extends: Forms,
  data () {
    return {
      vgmdb_id: '',
      menu: false,
      date: '',
      track_lang: [''],
      // orgg: ['label', 'publisher', 'distributor', 'manufacturer'],
      artists: this.$page.props.artists,
      products: this.$page.props.products,
      events: this.$page.props.events,
      organizations: this.$page.props.organizations,
      album: this.$inertia.form({
        images: null,
        name: '',
        name_real: '',
        name_trans: '',
        event_id: '',
        barcode: '',
        catalog: '',
        release_date: '',
        desc: '',
        roles: {
          arrangers: [],
          performers: [],
          lyricists: [],
          composers: []
        },
        orgs: {
          label: [],
          publisher: [],
          distributor: [],
          manufacturer: []
        },
        discs: [
          {
            disc_length: '',
            name: '',
            tracks: [
              {
                names: {},
                track_length: ''
              }
            ]
          }
        ],
        media_format: ''
      })
    }
  },
  computed: {
    ...sync('dashboard', ['loading']),
    hasErrors () {
      return Object.keys(this.$page.props.errors).length > 0
    }
  },
  methods: {
    submit () {
      // const data = this.album

      this.album.post('/dashboard/album/')
    }
  }
}
</script>
