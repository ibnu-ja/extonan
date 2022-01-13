<script>
import Forms from './Forms.vue'
// import { union } from 'lodash'

export default {
  name: 'DashboardAlbum',
  extends: Forms,
  data () {
    return {
      album: this.$inertia.form({
        images: null,
        imageLabel: [],
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
  methods: {
    submit () {
      this.album.post(this.route('album.store'))
    }
  }
}
</script>
