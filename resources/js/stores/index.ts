import { defineStore } from 'pinia'
import { useStorage } from '@vueuse/core'
import { reactive, ref } from 'vue'

export const useUserStore = defineStore('user', () => {
  const theme = useStorage<string>('theme', 'system')
  const displayMode = useStorage<'abc' | 'list' | 'tile'>('displayMode', 'abc')
  const displayModeMV = useStorage<'grid' | 'list'>('displayMode', 'grid')

  const videoQuality = useStorage('videoQuality', -1)

  return { theme, displayMode, displayModeMV, videoQuality }
})

export const useSnackbar = defineStore('snackbar', () => {
  const show = ref(false)
  type VSnackbarOptions = {
    color?: string
    variant?: 'text' | 'flat' | 'elevated' | 'tonal' | 'outlined' | 'plain'
    vertical?: boolean
    timeout?: string | number
    text?: string
    //   TODO add more
  }
  const params = reactive<VSnackbarOptions>({})

  return { params, show }
})
