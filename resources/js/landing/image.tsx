import React from 'react';
import { assetUrl } from './paths';

export default function Image({ src, fill, priority, quality, placeholder, blurDataURL, style, ...props }: React.ImgHTMLAttributes<HTMLImageElement> & { src: string; fill?: boolean; priority?: boolean; quality?: number; placeholder?: string; blurDataURL?: string }) {
    return <img {...props} src={assetUrl(src)} loading={priority ? 'eager' : props.loading ?? 'lazy'} style={fill ? { position: 'absolute', inset: 0, height: '100%', width: '100%', ...style } : style} />;
}
