<script>
import Forms from './Forms.vue'
// import { union } from 'lodash'

export default {
  name: 'DashboardAlbum',
  extends: Forms,
  data () {
    return {
      album: this.$inertia.form(this.$page.props.album)
    }
  },
  created () {
    this.track_lang = Object.keys(
      this.$page.props.album.discs[0].tracks[0].names
    )
    this.album.imageLabel = []
  },
  methods: {
    submit () {
      if (this.album.imageLabel.length > 0) {
        this.$inertia.post(
        // route
          this.route('dashboard.album.storeGallery', {
            album: this.album.id
          }),
          // request data
          { images: this.album.images, imageLabel: this.album.imageLabel },
          // callback
          {
            onSuccess: () => {
              this.album.put(this.route('dashboard.album.update', {
                album: this.album.id
              }))
            }
          })
      } else {
        this.album.put(this.route('dashboard.album.update', {
          album: this.album.id
        }))
      }
    }
  }
}
</script>
