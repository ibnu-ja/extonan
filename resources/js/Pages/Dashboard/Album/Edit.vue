<script>
import Forms from './Forms.vue'
// import { union } from 'lodash'

export default {
  name: 'DashboardAlbum',
  extends: Forms,
  created () {
    if (this.$page.props.album) {
      this.track_lang = Object.keys(
        this.$page.props.album.discs[0].tracks[0].names
      )
      this.album = this.$inertia.form(this.$page.props.album)
    }
  },
  methods: {
    submit () {
      this.$inertia.post(
        // route
        this.route('album.storeGallery', {
          album: this.album.id
        }),
        // request data
        { images: this.album.images },
        // callback
        {
          onSuccess: () => {
            this.album.put(this.route('album.update', {
              album: this.album.id
            }))
          }
        })
    }
  }
}
</script>
