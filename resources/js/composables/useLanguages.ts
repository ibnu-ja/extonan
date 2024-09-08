import { Language, LanguageItem, TranslatableField } from '@/types/formHelper'
import { ref } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function useLanguages() {
  const languages: LanguageItem[] = [
    // TODO use proper translations
    {
      label: 'English',
      value: 'en',
    },
    {
      label: 'Indonesia',
      value: 'id',
    },
    // {
    //   label: 'Native',
    //   value: 'native',
    // },
    // {
    //   label: 'Romaji',
    //   value: 'romaji',
    // },
  ]
  const selectedLanguage = ref<LanguageItem>(languages.find(i => i.value === usePage().props.locale))

  function translate(value: TranslatableField, useFallback: boolean = true, locale?: Language): string {
    const page = usePage().props

    return (
      (locale && value[locale])
      || value[page.locale]
      || (useFallback && value[page.fallbackLocale])
    )
  }

  function hasTranslation(value: TranslatableField, useFallback: boolean = true, locale?: Language): boolean {
    const page = usePage().props

    return Boolean(
      (locale && value[locale])
      || value[page.locale]
      || (useFallback && value[page.fallbackLocale]),
    )
  }

  return { languages, selectedLanguage, translate, hasTranslation }
}
