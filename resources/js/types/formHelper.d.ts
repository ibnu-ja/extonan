export type Language = 'en' | 'id' | 'romaji' | 'native'

export type LanguageItem = {
  label: string
  value: Language
}

export type TranslatableField = {
  [key in Language]?: string | null;
}
