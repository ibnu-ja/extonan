import { computed } from 'vue'
import { useTheme } from 'vuetify'

export function useGradient() {
  const theme = useTheme()
  const gradient = computed(() => {
    if (theme.current.value.dark) {
      return '0deg, rgb(18,18,18) 10%, rgba(18,18,18,0.7) 70%, rgba(18,18,18,0.3) 100%'
    } else return '0deg, rgba(255,255,255,1) 10%, rgba(255,255,255,0.7) 70%, rgba(255,255,255,0.2) 100%'
  })

  return { gradient }
}
