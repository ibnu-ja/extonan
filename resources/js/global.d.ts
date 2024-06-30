import { route as routeFn } from 'ziggy-js'
import axios from "axios";

declare global {
    let route: typeof routeFn
    interface Window {
        axios: typeof axios
    }
}
