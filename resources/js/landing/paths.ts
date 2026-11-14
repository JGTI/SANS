const appUrl = document.querySelector<HTMLMetaElement>('meta[name="app-base"]')?.content ?? window.location.origin;
export const base = new URL(appUrl).pathname.replace(/\/$/, '');
export const localUrl = (path: string) => path.startsWith('/') && !path.startsWith('//') ? base + path : path;
export const assetUrl = (path: string) => path.startsWith('/') && !path.startsWith('//') ? base + '/landing' + path : path;
