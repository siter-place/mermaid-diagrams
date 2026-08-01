import { DownloadArtifact } from './types';
import { sanitizeSvg } from './sanitize-svg';

export function sanitizeFilename(title: string, fallback = 'diagram'): string {
  const cleaned = (title || fallback)
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9_-]+/g, '-')
    .replace(/^-+|-+$/g, '');
  return cleaned || fallback;
}

export function exportSourceDownload(
  title: string,
  source: string,
  id?: number | string
): DownloadArtifact {
  const baseName = sanitizeFilename(title);
  const suffix = id ? `-${id}` : '';
  const filename = `${baseName}${suffix}.mmd`;
  const mimeType = 'text/plain;charset=utf-8';

  let blobUrl: string | undefined;
  let revoke: (() => void) | undefined;

  if (typeof Blob !== 'undefined' && typeof URL !== 'undefined' && typeof URL.createObjectURL === 'function') {
    const blob = new Blob([source], { type: mimeType });
    blobUrl = URL.createObjectURL(blob);
    revoke = () => URL.revokeObjectURL(blobUrl!);
  }

  return {
    filename,
    content: source,
    mimeType,
    ...(blobUrl ? { blobUrl, revoke } : {}),
  };
}

export function exportSvgDownload(
  title: string,
  svgString: string,
  id?: number | string
): DownloadArtifact {
  const baseName = sanitizeFilename(title);
  const suffix = id ? `-${id}` : '';
  const filename = `${baseName}${suffix}.svg`;
  const mimeType = 'image/svg+xml;charset=utf-8';
  const sanitized = sanitizeSvg(svgString);

  let blobUrl: string | undefined;
  let revoke: (() => void) | undefined;

  if (typeof Blob !== 'undefined' && typeof URL !== 'undefined' && typeof URL.createObjectURL === 'function') {
    const blob = new Blob([sanitized], { type: mimeType });
    blobUrl = URL.createObjectURL(blob);
    revoke = () => URL.revokeObjectURL(blobUrl!);
  }

  return {
    filename,
    content: sanitized,
    mimeType,
    ...(blobUrl ? { blobUrl, revoke } : {}),
  };
}
