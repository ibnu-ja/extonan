const lucideCache = new Map<string, any>();
const hugeiconsCache = new Map<string, any>();

let lucideBundlePromise: Promise<Record<string, any>> | null = null;
let hugeiconsBundlePromise: Promise<Record<string, any>> | null = null;

function resolveIcon(mod: Record<string, any>, name: string): any {
	if (mod[name]) {
return mod[name];
}

	const base = name.endsWith("Icon") ? name.slice(0, -4) : name;

	return mod[base] ?? mod[`${base}Icon`] ?? null;
}

export const lucideIconLoader = {
	peek(name: string): any {
		if (lucideCache.has(name)) {
return lucideCache.get(name);
}

		const base = name.endsWith("Icon") ? name.slice(0, -4) : name;

		return lucideCache.get(base) ?? lucideCache.get(`${base}Icon`);
	},
	async load(name: string): Promise<any> {
		const cached = this.peek(name);

		if (cached !== undefined) {
return cached;
}

		if (!lucideBundlePromise) {
			lucideBundlePromise = import("lucide-svelte");
		}

		try {
			const mod = await lucideBundlePromise;
			const icon = resolveIcon(mod, name);
			lucideCache.set(name, icon);

			return icon;
		} catch {
			lucideCache.set(name, null);

			return null;
		}
	},
};

export const hugeiconsIconLoader = {
	peek(name: string): any {
		if (hugeiconsCache.has(name)) {
return hugeiconsCache.get(name);
}

		const base = name.endsWith("Icon") ? name.slice(0, -4) : name;

		return hugeiconsCache.get(base) ?? hugeiconsCache.get(`${base}Icon`);
	},
	async load(name: string): Promise<any> {
		const cached = this.peek(name);

		if (cached !== undefined) {
return cached;
}

		if (!hugeiconsBundlePromise) {
			hugeiconsBundlePromise = import("@hugeicons/core-free-icons");
		}

		try {
			const mod = await hugeiconsBundlePromise;
			const icon = resolveIcon(mod, name);
			hugeiconsCache.set(name, icon);

			return icon;
		} catch {
			hugeiconsCache.set(name, null);

			return null;
		}
	},
};
