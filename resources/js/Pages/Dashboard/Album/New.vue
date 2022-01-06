<script>
import Forms from './Forms.vue'
// import { union } from 'lodash'

export default {
  name: 'DashboardAlbum',
  extends: Forms,
  methods: {
    submit () {
      this.album.post(this.route('album.store'))
    }
  }
}
</script>
