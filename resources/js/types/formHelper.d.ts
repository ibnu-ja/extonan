export type Language = 'en' | 'id' | 'romaji' | 'native'

export type LanguageItem = {
  label: string
  value: Language
}

export type TranslatableField<L extends string = Language> = {
  [key in L]?: string | null;
}
