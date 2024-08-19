export type Languages = 'en' | 'id' | 'romaji' | 'native'

export type LanguageItem = {
  label: string
  value: Languages
}

export type TranslatableField = {
  [key in Languages]?: string | null;
}
