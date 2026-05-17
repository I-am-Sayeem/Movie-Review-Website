import React from 'react';
import { createRoot } from 'react-dom/client';
import Particles from './Particles';
import DomeGallery from './DomeGallery';

document.addEventListener('DOMContentLoaded', () => {
  // Mount Particles background
  const particlesEl = document.getElementById('particles-bg');
  if (particlesEl) {
    const root = createRoot(particlesEl);
    root.render(
      <Particles
        particleColors={['#ffffff', '#ffffff', '#ffffff']}
        particleCount={300}
        particleSpread={10}
        speed={0.1}
        particleBaseSize={100}
        moveParticlesOnHover={true}
        alphaParticles={false}
        disableRotation={false}
      />
    );
  }

  // Mount DomeGallery in the trending section
  const domeEl = document.getElementById('dome-gallery-container');
  if (domeEl) {
    const imagesData = domeEl.getAttribute('data-images');
    let images = [];
    try {
      images = JSON.parse(imagesData);
    } catch (e) {
      console.warn('Failed to parse dome gallery images:', e);
    }

    const root = createRoot(domeEl);
    root.render(
      <DomeGallery
        images={images}
        grayscale={false}
        imageBorderRadius="16px"
        openedImageBorderRadius="16px"
        openedImageWidth="400px"
        openedImageHeight="400px"
        overlayBlurColor="var(--bg-primary)"
      />
    );
  }
});
