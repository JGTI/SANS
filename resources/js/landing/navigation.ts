import { base } from './paths';
export const usePathname = () => window.location.pathname.slice(base.length) || '/';
