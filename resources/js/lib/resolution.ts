export function getResolution(name: string): {
    label: string;
    variant: 'default' | 'secondary' | 'destructive' | 'outline';
} | null {
    const match = name.match(/(\d+x\d+|\d+p|4K)/i);

    if (!match) {
        return null;
    }

    const res = match[1];
    const map: Record<string, string> = {
        '3840x2160': '4K',
        '1920x1080': '1080p',
        '1440x1080': '1080p',
        '1280x720': '720p',
        '960x720': '720p',
        '848x480': '480p',
    };

    const label = res.includes('x') ? (map[res] ?? res) : res;
    const variantMap: Record<
        string,
        'default' | 'secondary' | 'destructive' | 'outline'
    > = {
        '4K': 'default',
        '1080p': 'secondary',
        '720p': 'outline',
        '480p': 'outline',
    };

    return { label, variant: variantMap[label] ?? 'outline' };
}
