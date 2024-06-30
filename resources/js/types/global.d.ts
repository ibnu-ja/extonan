import { route as ziggyRoute } from 'ziggy-js';
import { AxiosInstance } from "axios";

declare global {
    var route: typeof ziggyRoute;
    interface Window {
        axios: AxiosInstance;
    }
}

declare module 'vue' {
    interface ComponentCustomProperties {
        route: typeof ziggyRoute;
    }
}

declare module '@inertiajs/core' {
    interface PageProps extends InertiaPageProps, AppPageProps {}
}
