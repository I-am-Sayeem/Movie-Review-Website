import { useEffect, useMemo, useRef, useCallback } from 'react';
import { useGesture } from '@use-gesture/react';
import { clamp, normalizeAngle, wrapAngleSigned, getDataNumber, buildItems, computeItemBaseRotation } from './domeUtils';


const DEFAULTS = { maxVerticalRotationDeg: 5, dragSensitivity: 20, enlargeTransitionMs: 300, segments: 35 };

export default function DomeGallery({
  images = [], fit = 0.5, fitBasis = 'auto', minRadius = 600, maxRadius = Infinity,
  padFactor = 0.25, overlayBlurColor = '#120F17',
  maxVerticalRotationDeg = DEFAULTS.maxVerticalRotationDeg,
  dragSensitivity = DEFAULTS.dragSensitivity, enlargeTransitionMs = DEFAULTS.enlargeTransitionMs,
  segments = DEFAULTS.segments, dragDampening = 2,
  openedImageWidth = '250px', openedImageHeight = '350px',
  imageBorderRadius = '30px', openedImageBorderRadius = '30px', grayscale = true
}) {
  const rootRef = useRef(null), mainRef = useRef(null), sphereRef = useRef(null);
  const frameRef = useRef(null), viewerRef = useRef(null), scrimRef = useRef(null);
  const focusedElRef = useRef(null), originalTilePositionRef = useRef(null);
  const rotationRef = useRef({ x: 0, y: 0 }), startRotRef = useRef({ x: 0, y: 0 });
  const startPosRef = useRef(null), draggingRef = useRef(false), movedRef = useRef(false);
  const inertiaRAF = useRef(null), openingRef = useRef(false), openStartedAtRef = useRef(0);
  const lastDragEndAt = useRef(0), scrollLockedRef = useRef(false), lockedRadiusRef = useRef(null);

  const lockScroll = useCallback(() => {
    if (scrollLockedRef.current) return;
    scrollLockedRef.current = true;
    document.body.classList.add('dg-scroll-lock');
  }, []);
  const unlockScroll = useCallback(() => {
    if (!scrollLockedRef.current) return;
    if (rootRef.current?.getAttribute('data-enlarging') === 'true') return;
    scrollLockedRef.current = false;
    document.body.classList.remove('dg-scroll-lock');
  }, []);

  const items = useMemo(() => buildItems(images, segments), [images, segments]);

  const applyTransform = (xDeg, yDeg) => {
    const el = sphereRef.current;
    if (el) el.style.transform = `translateZ(calc(var(--radius) * -1)) rotateX(${xDeg}deg) rotateY(${yDeg}deg)`;
  };

  useEffect(() => {
    const root = rootRef.current;
    if (!root) return;
    const ro = new ResizeObserver(entries => {
      const cr = entries[0].contentRect;
      const w = Math.max(1, cr.width), h = Math.max(1, cr.height);
      const minDim = Math.min(w, h), maxDim = Math.max(w, h), aspect = w / h;
      let basis;
      switch (fitBasis) {
        case 'min': basis = minDim; break;
        case 'max': basis = maxDim; break;
        case 'width': basis = w; break;
        case 'height': basis = h; break;
        default: basis = aspect >= 1.3 ? w : minDim;
      }
      let radius = basis * fit;
      radius = Math.min(radius, h * 1.35);
      radius = clamp(radius, minRadius, maxRadius);
      lockedRadiusRef.current = Math.round(radius);
      const viewerPad = Math.max(8, Math.round(minDim * padFactor));
      root.style.setProperty('--radius', `${lockedRadiusRef.current}px`);
      root.style.setProperty('--viewer-pad', `${viewerPad}px`);
      root.style.setProperty('--overlay-blur-color', overlayBlurColor);
      root.style.setProperty('--tile-radius', imageBorderRadius);
      root.style.setProperty('--enlarge-radius', openedImageBorderRadius);
      root.style.setProperty('--image-filter', grayscale ? 'grayscale(1)' : 'none');
      applyTransform(rotationRef.current.x, rotationRef.current.y);
    });
    ro.observe(root);
    return () => ro.disconnect();
  }, [fit, fitBasis, minRadius, maxRadius, padFactor, overlayBlurColor, grayscale, imageBorderRadius, openedImageBorderRadius]);

  useEffect(() => { applyTransform(rotationRef.current.x, rotationRef.current.y); }, []);

  const stopInertia = useCallback(() => {
    if (inertiaRAF.current) { cancelAnimationFrame(inertiaRAF.current); inertiaRAF.current = null; }
  }, []);

  const startInertia = useCallback((vx, vy) => {
    const MAX_V = 1.4;
    let vX = clamp(vx, -MAX_V, MAX_V) * 80, vY = clamp(vy, -MAX_V, MAX_V) * 80;
    let frames = 0;
    const d = clamp(dragDampening ?? 0.6, 0, 1);
    const frictionMul = 0.94 + 0.055 * d, stopThreshold = 0.015 - 0.01 * d;
    const maxFrames = Math.round(90 + 270 * d);
    const step = () => {
      vX *= frictionMul; vY *= frictionMul;
      if ((Math.abs(vX) < stopThreshold && Math.abs(vY) < stopThreshold) || ++frames > maxFrames) { inertiaRAF.current = null; return; }
      const nextX = clamp(rotationRef.current.x - vY / 200, -maxVerticalRotationDeg, maxVerticalRotationDeg);
      const nextY = wrapAngleSigned(rotationRef.current.y + vX / 200);
      rotationRef.current = { x: nextX, y: nextY };
      applyTransform(nextX, nextY);
      inertiaRAF.current = requestAnimationFrame(step);
    };
    stopInertia();
    inertiaRAF.current = requestAnimationFrame(step);
  }, [dragDampening, maxVerticalRotationDeg, stopInertia]);

  useGesture({
    onDragStart: ({ event }) => {
      if (focusedElRef.current) return;
      stopInertia();
      draggingRef.current = true; movedRef.current = false;
      startRotRef.current = { ...rotationRef.current };
      startPosRef.current = { x: event.clientX, y: event.clientY };
    },
    onDrag: ({ event, last, velocity = [0, 0], direction = [0, 0], movement }) => {
      if (focusedElRef.current || !draggingRef.current || !startPosRef.current) return;
      const dxTotal = event.clientX - startPosRef.current.x;
      const dyTotal = event.clientY - startPosRef.current.y;
      if (!movedRef.current && dxTotal * dxTotal + dyTotal * dyTotal > 16) movedRef.current = true;
      const nextX = clamp(startRotRef.current.x - dyTotal / dragSensitivity, -maxVerticalRotationDeg, maxVerticalRotationDeg);
      const nextY = wrapAngleSigned(startRotRef.current.y + dxTotal / dragSensitivity);
      if (rotationRef.current.x !== nextX || rotationRef.current.y !== nextY) {
        rotationRef.current = { x: nextX, y: nextY }; applyTransform(nextX, nextY);
      }
      if (last) {
        draggingRef.current = false;
        let [vMagX, vMagY] = velocity; const [dirX, dirY] = direction;
        let vx = vMagX * dirX, vy = vMagY * dirY;
        if (Math.abs(vx) < 0.001 && Math.abs(vy) < 0.001 && Array.isArray(movement)) {
          vx = clamp((movement[0] / dragSensitivity) * 0.02, -1.2, 1.2);
          vy = clamp((movement[1] / dragSensitivity) * 0.02, -1.2, 1.2);
        }
        if (Math.abs(vx) > 0.005 || Math.abs(vy) > 0.005) startInertia(vx, vy);
        if (movedRef.current) lastDragEndAt.current = performance.now();
        movedRef.current = false;
      }
    }
  }, { target: mainRef, eventOptions: { passive: true } });

  const openItemFromElement = useCallback(el => {
    if (openingRef.current) return;
    openingRef.current = true; openStartedAtRef.current = performance.now(); lockScroll();
    const parent = el.parentElement;
    focusedElRef.current = el;
    el.setAttribute('data-focused', 'true');
    const offsetX = getDataNumber(parent, 'offsetX', 0), offsetY = getDataNumber(parent, 'offsetY', 0);
    const sizeX = getDataNumber(parent, 'sizeX', 2), sizeY = getDataNumber(parent, 'sizeY', 2);
    const parentRot = computeItemBaseRotation(offsetX, offsetY, sizeX, sizeY, segments);
    const parentY = normalizeAngle(parentRot.rotateY), globalY = normalizeAngle(rotationRef.current.y);
    let rotY = -(parentY + globalY) % 360;
    if (rotY < -180) rotY += 360;
    const rotX = -parentRot.rotateX - rotationRef.current.x;
    parent.style.setProperty('--rot-y-delta', `${rotY}deg`);
    parent.style.setProperty('--rot-x-delta', `${rotX}deg`);
    const refDiv = document.createElement('div');
    refDiv.className = 'item__image item__image--reference';
    refDiv.style.opacity = '0';
    refDiv.style.transform = `rotateX(${-parentRot.rotateX}deg) rotateY(${-parentRot.rotateY}deg)`;
    parent.appendChild(refDiv);
    void refDiv.offsetHeight;
    const tileR = refDiv.getBoundingClientRect();
    const mainR = mainRef.current?.getBoundingClientRect();
    const frameR = frameRef.current?.getBoundingClientRect();
    if (!mainR || !frameR || tileR.width <= 0) { openingRef.current = false; focusedElRef.current = null; parent.removeChild(refDiv); unlockScroll(); return; }
    originalTilePositionRef.current = { left: tileR.left, top: tileR.top, width: tileR.width, height: tileR.height };
    el.style.visibility = 'hidden'; el.style.zIndex = 0;
    const overlay = document.createElement('div');
    overlay.className = 'enlarge';
    overlay.style.cssText = `position:absolute;left:${frameR.left - mainR.left}px;top:${frameR.top - mainR.top}px;width:${frameR.width}px;height:${frameR.height}px;opacity:0;z-index:30;will-change:transform,opacity;transform-origin:top left;transition:transform ${enlargeTransitionMs}ms ease,opacity ${enlargeTransitionMs}ms ease;`;
    const rawSrc = parent.dataset.src || el.querySelector('img')?.src || '';
    const img = document.createElement('img'); img.src = rawSrc;
    overlay.appendChild(img);
    // Add movie info caption
    const movieTitle = parent.dataset.title || '';
    const movieRating = parent.dataset.rating || '';
    const movieGenre = parent.dataset.genre || '';
    if (movieTitle) {
      const caption = document.createElement('div');
      caption.className = 'enlarge-caption';
      const stars = movieRating ? '⭐ ' + movieRating : '';
      const genre = movieGenre ? `<span class="enlarge-genre">${movieGenre}</span>` : '';
      caption.innerHTML = `<div class="enlarge-title">${movieTitle}</div><div class="enlarge-meta">${genre}${stars}</div>`;
      overlay.appendChild(caption);
    }
    viewerRef.current.appendChild(overlay);
    const sx0 = tileR.width / frameR.width || 1, sy0 = tileR.height / frameR.height || 1;
    overlay.style.transform = `translate(${tileR.left - frameR.left}px,${tileR.top - frameR.top}px) scale(${sx0},${sy0})`;
    setTimeout(() => {
      if (!overlay.parentElement) return;
      overlay.style.opacity = '1'; overlay.style.transform = 'translate(0,0) scale(1,1)';
      rootRef.current?.setAttribute('data-enlarging', 'true');
    }, 16);
    if (openedImageWidth || openedImageHeight) {
      const onFirstEnd = ev => {
        if (ev.propertyName !== 'transform') return;
        overlay.removeEventListener('transitionend', onFirstEnd);
        const prev = overlay.style.transition;
        overlay.style.transition = 'none';
        const tw = openedImageWidth || `${frameR.width}px`, th = openedImageHeight || `${frameR.height}px`;
        overlay.style.width = tw; overlay.style.height = th;
        const nr = overlay.getBoundingClientRect();
        overlay.style.width = frameR.width + 'px'; overlay.style.height = frameR.height + 'px';
        void overlay.offsetWidth;
        overlay.style.transition = `left ${enlargeTransitionMs}ms ease,top ${enlargeTransitionMs}ms ease,width ${enlargeTransitionMs}ms ease,height ${enlargeTransitionMs}ms ease`;
        requestAnimationFrame(() => {
          overlay.style.left = `${frameR.left - mainR.left + (frameR.width - nr.width) / 2}px`;
          overlay.style.top = `${frameR.top - mainR.top + (frameR.height - nr.height) / 2}px`;
          overlay.style.width = tw; overlay.style.height = th;
        });
        overlay.addEventListener('transitionend', () => { overlay.style.transition = prev; }, { once: true });
      };
      overlay.addEventListener('transitionend', onFirstEnd);
    }
  }, [enlargeTransitionMs, lockScroll, openedImageHeight, openedImageWidth, segments, unlockScroll]);

  useEffect(() => {
    const scrim = scrimRef.current; if (!scrim) return;
    const close = () => {
      if (performance.now() - openStartedAtRef.current < 250) return;
      const el = focusedElRef.current; if (!el) return;
      const parent = el.parentElement;
      const overlay = viewerRef.current?.querySelector('.enlarge');
      if (!overlay) return;
      const refDiv = parent.querySelector('.item__image--reference');
      const originalPos = originalTilePositionRef.current;
      if (!originalPos) {
        overlay.remove(); if (refDiv) refDiv.remove();
        parent.style.setProperty('--rot-y-delta', '0deg'); parent.style.setProperty('--rot-x-delta', '0deg');
        el.style.visibility = ''; el.style.zIndex = 0;
        focusedElRef.current = null; rootRef.current?.removeAttribute('data-enlarging');
        openingRef.current = false; unlockScroll(); return;
      }
      const currentRect = overlay.getBoundingClientRect();
      const rootRect = rootRef.current.getBoundingClientRect();
      const oRel = { left: originalPos.left - rootRect.left, top: originalPos.top - rootRect.top, width: originalPos.width, height: originalPos.height };
      const cRel = { left: currentRect.left - rootRect.left, top: currentRect.top - rootRect.top, width: currentRect.width, height: currentRect.height };
      const anim = document.createElement('div');
      anim.className = 'enlarge-closing';
      anim.style.cssText = `position:absolute;left:${cRel.left}px;top:${cRel.top}px;width:${cRel.width}px;height:${cRel.height}px;z-index:9999;border-radius:var(--enlarge-radius,32px);overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,.35);transition:all ${enlargeTransitionMs}ms ease-out;pointer-events:none;`;
      const oImg = overlay.querySelector('img');
      if (oImg) { const c = oImg.cloneNode(); c.style.cssText = 'width:100%;height:100%;object-fit:cover;'; anim.appendChild(c); }
      overlay.remove(); rootRef.current.appendChild(anim);
      void anim.getBoundingClientRect();
      requestAnimationFrame(() => {
        anim.style.left = oRel.left + 'px'; anim.style.top = oRel.top + 'px';
        anim.style.width = oRel.width + 'px'; anim.style.height = oRel.height + 'px'; anim.style.opacity = '0';
      });
      const cleanup = () => {
        anim.remove(); originalTilePositionRef.current = null;
        if (refDiv) refDiv.remove();
        parent.style.transition = 'none'; el.style.transition = 'none';
        parent.style.setProperty('--rot-y-delta', '0deg'); parent.style.setProperty('--rot-x-delta', '0deg');
        requestAnimationFrame(() => {
          el.style.visibility = ''; el.style.opacity = '0'; el.style.zIndex = 0; focusedElRef.current = null;
          rootRef.current?.removeAttribute('data-enlarging');
          requestAnimationFrame(() => {
            parent.style.transition = ''; el.style.transition = 'opacity 300ms ease-out';
            requestAnimationFrame(() => {
              el.style.opacity = '1';
              setTimeout(() => { el.style.transition = ''; el.style.opacity = ''; openingRef.current = false; unlockScroll(); }, 300);
            });
          });
        });
      };
      anim.addEventListener('transitionend', cleanup, { once: true });
    };
    scrim.addEventListener('click', close);
    const onKey = e => { if (e.key === 'Escape') close(); };
    window.addEventListener('keydown', onKey);
    return () => { scrim.removeEventListener('click', close); window.removeEventListener('keydown', onKey); };
  }, [enlargeTransitionMs, unlockScroll]);

  const onTileClick = useCallback(e => {
    if (draggingRef.current || movedRef.current || performance.now() - lastDragEndAt.current < 80 || openingRef.current) return;
    openItemFromElement(e.currentTarget);
  }, [openItemFromElement]);

  useEffect(() => () => { document.body.classList.remove('dg-scroll-lock'); }, []);

  return (
    <div ref={rootRef} className="sphere-root"
      style={{ '--segments-x': segments, '--segments-y': segments, '--overlay-blur-color': overlayBlurColor,
        '--tile-radius': imageBorderRadius, '--enlarge-radius': openedImageBorderRadius,
        '--image-filter': grayscale ? 'grayscale(1)' : 'none' }}>
      <main ref={mainRef} className="sphere-main">
        <div className="stage">
          <div ref={sphereRef} className="sphere">
            {items.map((it, i) => (
              <div key={`${it.x},${it.y},${i}`} className="item" data-src={it.src}
                data-offset-x={it.x} data-offset-y={it.y} data-size-x={it.sizeX} data-size-y={it.sizeY}
                data-title={it.title || ''} data-rating={it.rating || ''} data-genre={it.genre || ''}
                style={{ '--offset-x': it.x, '--offset-y': it.y, '--item-size-x': it.sizeX, '--item-size-y': it.sizeY }}>
                <div className="item__image" role="button" tabIndex={0}
                  aria-label={it.alt || 'Open image'} onClick={onTileClick}>
                  <img src={it.src} draggable={false} alt={it.alt} />
                </div>
              </div>
            ))}
          </div>
        </div>
        <div className="overlay" /><div className="overlay overlay--blur" />
        <div className="edge-fade edge-fade--top" /><div className="edge-fade edge-fade--bottom" />
        <div className="viewer" ref={viewerRef}>
          <div ref={scrimRef} className="scrim" /><div ref={frameRef} className="frame" />
        </div>
      </main>
    </div>
  );
}
