// @ts-check
import eslint from '@eslint/js'
import tseslint from 'typescript-eslint'
import pluginVue from 'eslint-plugin-vue'
import vueParser from 'vue-eslint-parser'
import stylistic from '@stylistic/eslint-plugin'

export default tseslint.config(
  eslint.configs.recommended,
  ...tseslint.configs.recommended,
  ...pluginVue.configs['flat/recommended'],
  {
    languageOptions: {
      parser: vueParser,
      parserOptions: {
        parser: tseslint.parser,
        sourceType: 'module',
      },
    },
  },
  stylistic.configs.customize({
    // the following options are the default values
    indent: 2,
    quotes: 'single',
    semi: false,
    braceStyle: '1tbs',
    // ...
  }),
  {
    rules: {
      'no-undef': 'off',
      'vue/multi-word-component-names': 'off',
      // 'sort-imports': ['error', {
      //   ignoreCase: false,
      //   ignoreDeclarationSort: false,
      //   ignoreMemberSort: false,
      //   memberSyntaxSortOrder: ['none', 'all', 'multiple', 'single'],
      //   allowSeparatedGroups: false,
      // }],
    },
  },
  {
    ignores: [
      'node_modules/',
      'public/',
      'vendor/',
      'bootstrap/',
    ],
  },
)
