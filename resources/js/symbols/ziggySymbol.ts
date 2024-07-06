import { InjectionKey } from 'vue'
import { route } from 'ziggy-js'

export const ziggySymbol: InjectionKey<typeof route> = Symbol('route')
