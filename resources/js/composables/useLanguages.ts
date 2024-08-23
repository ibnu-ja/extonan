import { LanguageItem } from '@/types/formHelper'
import { ref } from 'vue'

export function useLanguages() {
  const languages: LanguageItem[] = [
    {
      label: 'English',
      value: 'en',
    }, {
      label: 'Indonesia',
      value: 'id',
    }, {
      label: 'Native',
      value: 'native',
    }, {
      label: 'Romaji',
      value: 'romaji',
    },
  ]
  const selectedLanguage = ref<LanguageItem>(languages[0])

  return { languages, selectedLanguage }
}
