import { ref, shallowRef } from 'vue'

export type TextFieldProps = {
  variant?: 'outlined' | 'plain' | 'solo' | 'filled' | 'underlined'
  label?: string
  placeholder?: string
  autocomplete?: string
}

export type DialogInstance = {
  title?: string
  desc?: string
  textFieldProps?: TextFieldProps
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  resolve: (value: any) => void
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  reject: (reason: any) => void
}

export const dialogRef = shallowRef<DialogInstance>()

export const dialogModel = ref(false)

/**
 * @param data Resolved value
 * Closes the currently opened dialog, resolving the promise with the return value of the dialog, or with the given
 * data if any.
 */
export function closeDialog(data?: boolean | string | null) {
  dialogModel.value = false
  if (!data) {
    dialogRef.value!.reject('cancelled')
  } else {
    dialogRef.value!.resolve(data)
    dialogRef.value = undefined
  }
}

/**
 * Opens a dialog.
 * @param title The dialog title
 * @param desc The dialog content
 * @return A promise that resolves to a boolean when the dialog is closed
 */
/**
 * Opens a dialog.
 * @param title The dialog title
 * @param desc The dialog content
 * @return A promise that resolves to a boolean when the dialog is closed
 */
export function openConfirmationDialog(title: string, desc?: string): Promise<boolean> {
  dialogModel.value = true
  return new Promise((resolve, reject) => {
    dialogRef.value = {
      title,
      desc,
      resolve,
      reject,
    }
  })
}

/**
 * Opens a dialog.
 * @param title The dialog title
 * @param desc The dialog content
 * @param textFieldProps The form body
 * @return A promise that resolves to a string when the dialog is closed
 */
export function openFormDialog(title: string, desc?: string, textFieldProps: TextFieldProps = { variant: 'outlined' }): Promise<string> {
  dialogModel.value = true
  return new Promise((resolve, reject) => {
    dialogRef.value = {
      title,
      desc,
      textFieldProps,
      resolve,
      reject,
    }
  })
}
