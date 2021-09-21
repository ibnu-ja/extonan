<script>
import Heading from './Heading'
import Header from '@/Mixins/heading'

export default {
  name: 'BaseTitle',

  extends: Heading,

  // eslint-disable-next-line no-undef
  mixins: [Header],

  props: {
    size: {
      type: String,
      default: 'text-h6'
    },
    mobileSize: {
      type: String,
      default: 'text-subtitle-1'
    },
    tag: {
      type: String,
      default: 'h3'
    },
    weight: {
      type: String,
      default: 'bold'
    }
  }
}
</script>
