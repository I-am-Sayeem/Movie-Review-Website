export const clamp = (v, min, max) => Math.min(Math.max(v, min), max);
export const normalizeAngle = d => ((d % 360) + 360) % 360;
export const wrapAngleSigned = deg => {
  const a = (((deg + 180) % 360) + 360) % 360;
  return a - 180;
};
export const getDataNumber = (el, name, fallback) => {
  const attr = el.dataset[name] ?? el.getAttribute(`data-${name}`);
  const n = attr == null ? NaN : parseFloat(attr);
  return Number.isFinite(n) ? n : fallback;
};

export function buildItems(pool, seg) {
  const xCols = Array.from({ length: seg }, (_, i) => -37 + i * 2);
  const evenYs = [-4, -2, 0, 2, 4];
  const oddYs = [-3, -1, 1, 3, 5];
  const coords = xCols.flatMap((x, c) => {
    const ys = c % 2 === 0 ? evenYs : oddYs;
    return ys.map(y => ({ x, y, sizeX: 2, sizeY: 2 }));
  });
  const totalSlots = coords.length;
  if (pool.length === 0) return coords.map(c => ({ ...c, src: '', alt: '' }));
  const normalizedImages = pool.map(image =>
    typeof image === 'string'
      ? { src: image, alt: '', title: '', rating: 0, genre: '' }
      : { src: image.src || '', alt: image.alt || '', title: image.title || '', rating: image.rating || 0, genre: image.genre || '' }
  );
  const usedImages = Array.from({ length: totalSlots }, (_, i) => normalizedImages[i % normalizedImages.length]);
  for (let i = 1; i < usedImages.length; i++) {
    if (usedImages[i].src === usedImages[i - 1].src) {
      for (let j = i + 1; j < usedImages.length; j++) {
        if (usedImages[j].src !== usedImages[i].src) {
          const tmp = usedImages[i]; usedImages[i] = usedImages[j]; usedImages[j] = tmp; break;
        }
      }
    }
  }
  return coords.map((c, i) => ({ ...c, src: usedImages[i].src, alt: usedImages[i].alt, title: usedImages[i].title, rating: usedImages[i].rating, genre: usedImages[i].genre }));
}

export function computeItemBaseRotation(offsetX, offsetY, sizeX, sizeY, segments) {
  const unit = 360 / segments / 2;
  return { rotateX: unit * (offsetY - (sizeY - 1) / 2), rotateY: unit * (offsetX + (sizeX - 1) / 2) };
}
