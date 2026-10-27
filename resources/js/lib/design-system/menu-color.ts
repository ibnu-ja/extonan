import type { MenuColorValue } from './config.js';

/**
 * Keeps rendered menus in sync with `menuColor` (toggling `dark` on `.cn-menu-target` for inverted menus).
 * Returns a cleanup function.
 */
export function syncMenuColor(menuColor: MenuColorValue): () => void {
    if (typeof window === 'undefined' || typeof document === 'undefined') {
        return () => {};
    }

    const isInvertedMenu = menuColor === 'inverted';

    let frameId = 0;

    const updateMenuElements = () => {
        const allElements =
            document.querySelectorAll<HTMLElement>('.cn-menu-target');

        if (allElements.length === 0) {
            return;
        }

        allElements.forEach((element) => {
            element.style.transition = 'none';
        });

        allElements.forEach((element) => {
            if (element.classList.contains('cn-menu-target')) {
                if (isInvertedMenu) {
                    element.classList.add('dark');
                } else {
                    element.classList.remove('dark');
                }
            }

            if (element.classList.contains('cn-menu-translucent')) {
                element.classList.remove('cn-menu-translucent');
            }

            element.removeAttribute('data-menu-translucent');
        });

        void document.body.offsetHeight;

        allElements.forEach((element) => {
            element.style.transition = '';
        });
    };

    updateMenuElements();

    const observer = new MutationObserver(() => {
        if (frameId) {
            return;
        }

        frameId = window.requestAnimationFrame(() => {
            frameId = 0;
            updateMenuElements();
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });

    return () => {
        observer.disconnect();

        if (frameId) {
            window.cancelAnimationFrame(frameId);
            frameId = 0;
        }
    };
}
