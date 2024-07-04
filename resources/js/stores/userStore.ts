import { defineStore } from 'pinia'
import { useStorage } from '@vueuse/core'

export const useUserStore = defineStore('user', () => {
  const theme = useStorage<string>('theme', 'system')

  return { theme }
})
