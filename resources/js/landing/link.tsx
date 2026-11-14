import React from 'react';
import { localUrl } from './paths';

export default function Link({ href, prefetch, replace, scroll, ...props }: React.AnchorHTMLAttributes<HTMLAnchorElement> & { href: string; prefetch?: boolean; replace?: boolean; scroll?: boolean }) {
    return <a {...props} href={localUrl(href)} />;
}
