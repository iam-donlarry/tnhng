/**
 * promoter-wheel.js
 * The Next Highlandah (TNH Season 3) - William Lawson's On-Site Spin the Wheel
 * On-Site Ambassador & Game Master Challenge Engine
 */

(function () {
  'use strict';

  // The 9 official William Lawson's campaign challenge segments matching the scorecard & wheel graphic
  const WHEEL_SLICES = [
    {
      id: 3,
      label: "DO SQUATS FOR 15 SECS",
      shortTitle: "Do squats",
      points: 5,
      category: "Unconventional",
      color: "#00c853", // Vibrant Highlandah Green
      textColor: "#ffffff",
      timeLimit: 15,
      rule: "Do squats continuously for 15 seconds without stopping."
    },
    {
      id: 4,
      label: "STAND ON ONE FOOT FOR 15 SECS",
      shortTitle: "Stand on one foot",
      points: 5,
      category: "Bold",
      color: "#023412", // Deep Hunter Green
      textColor: "#ffffff",
      timeLimit: 15,
      rule: "Stand steady on one foot for 15 seconds without losing balance."
    },
    {
      id: 5,
      label: "FREESTYLE RAP ABOUT WL",
      shortTitle: "Do a Freestyle Rap",
      points: 6,
      category: "Bold",
      color: "#005518", // Dark Green
      textColor: "#ffffff",
      timeLimit: 0,
      rule: "Perform a creative freestyle rap celebrating William Lawson's Scotch Whisky."
    },
    {
      id: 6,
      label: "1 MINUTE WITHOUT BLINKING",
      shortTitle: "No blinking",
      points: 3,
      category: "Bold",
      color: "#00c853", // Vibrant Green
      textColor: "#ffffff",
      timeLimit: 60,
      rule: "Keep eyes wide open for a full 60 seconds without blinking under promoter observation."
    },
    {
      id: 7,
      label: "LICK A LEMON & LOOK NORMAL",
      shortTitle: "Lemon challenge",
      points: 3,
      category: "Bold",
      color: "#023412", // Deep Hunter Green
      textColor: "#ffffff",
      timeLimit: 0,
      rule: "Lick or chew a fresh lemon slice and maintain a completely straight, normal face."
    },
    {
      id: 15,
      label: "SPIN AGAIN",
      shortTitle: "Spin again",
      points: 0,
      category: "Good humored",
      color: "#005518", // Dark Green
      textColor: "#ffffff",
      timeLimit: 0,
      rule: "Lucky Highlandah Re-Spin! No points added or lost; spin the wheel again immediately!"
    },
    {
      id: 9,
      label: "SPELL WL BACKWARDS",
      shortTitle: "Spell WL Backwards",
      points: 2,
      category: "Good humored",
      color: "#00c853", // Vibrant Green
      textColor: "#ffffff",
      timeLimit: 0,
      rule: "Spell out 'WILLIAM LAWSON'S' in reverse order (S-N-O-S-W-A-L M-A-I-L-L-I-W) accurately."
    },
    {
      id: 1,
      label: "ANSWER 5 FOOTBALL QUESTIONS",
      shortTitle: "Football trivia",
      points: 5,
      category: "Good humored",
      color: "#023412", // Deep Hunter Green
      textColor: "#ffffff",
      timeLimit: 0,
      rule: "Answer 5 football trivia questions posed by the ambassador without failing."
    },
    {
      id: 2,
      label: "SING YOUR FAVOURITE SONG",
      shortTitle: "Sing your fav song",
      points: 5,
      category: "Unconventional",
      color: "#005518", // Dark Green
      textColor: "#ffffff",
      timeLimit: 0,
      rule: "Sing a chorus or verse of your favorite song boldly in front of the Clan."
    }
  ];

  class HighlandahWheel {
    constructor(canvasId, options = {}) {
      this.canvas = document.getElementById(canvasId);
      if (!this.canvas) return;

      this.ctx = this.canvas.getContext('2d');
      this.options = options;
      this.slices = WHEEL_SLICES;
      this.numSlices = this.slices.length;
      this.arc = (2 * Math.PI) / this.numSlices;

      this.currentRotation = 0; // in radians
      this.isSpinning = false;
      this.selectedSlice = null;
      this.lastPegIndex = -1;

      // Audio Context for mechanical clicks
      this.audioCtx = null;

      // Official William Lawson's brand logo for wheel center hub
      this.logoImg = new Image();
      this.logoImg.src = 'images/logo.svg';
      this.logoImg.onload = () => {
        this.drawWheel();
      };

      this.setupCanvas();
      this.drawWheel();

      window.addEventListener('resize', () => {
        this.setupCanvas();
        this.drawWheel();
      });
    }

    initAudio() {
      if (!this.audioCtx) {
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        if (AudioContext) {
          this.audioCtx = new AudioContext();
        }
      }
      if (this.audioCtx && this.audioCtx.state === 'suspended') {
        this.audioCtx.resume();
      }
    }

    playTickSound() {
      if (!this.audioCtx) return;
      try {
        const osc = this.audioCtx.createOscillator();
        const gain = this.audioCtx.createGain();
        osc.type = 'triangle';
        osc.frequency.setValueAtTime(580, this.audioCtx.currentTime);
        osc.frequency.exponentialRampToValueAtTime(140, this.audioCtx.currentTime + 0.04);
        gain.gain.setValueAtTime(0.12, this.audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, this.audioCtx.currentTime + 0.04);
        osc.connect(gain);
        gain.connect(this.audioCtx.destination);
        osc.start();
        osc.stop(this.audioCtx.currentTime + 0.04);
      } catch (e) {
        // Audio tick silent fallback
      }
    }

    playWinSound() {
      if (!this.audioCtx) return;
      try {
        const now = this.audioCtx.currentTime;
        const notes = [440, 554, 659, 880];
        notes.forEach((freq, idx) => {
          const osc = this.audioCtx.createOscillator();
          const gain = this.audioCtx.createGain();
          osc.type = 'sine';
          osc.frequency.setValueAtTime(freq, now + idx * 0.08);
          gain.gain.setValueAtTime(0.15, now + idx * 0.08);
          gain.gain.exponentialRampToValueAtTime(0.001, now + idx * 0.08 + 0.25);
          osc.connect(gain);
          gain.connect(this.audioCtx.destination);
          osc.start(now + idx * 0.08);
          osc.stop(now + idx * 0.08 + 0.25);
        });
      } catch (e) {}
    }

    setupCanvas() {
      const rect = this.canvas.getBoundingClientRect();
      const dpr = window.devicePixelRatio || 1;
      const size = Math.min(rect.width || 420, 520);

      this.canvas.width = size * dpr;
      this.canvas.height = size * dpr;
      this.ctx.scale(dpr, dpr);

      this.size = size;
      this.center = size / 2;
      // 28px clearance for North pointer at top
      this.radius = this.center - 28;
    }

    drawWheel() {
      const ctx = this.ctx;
      const center = this.center;
      const radius = this.radius;

      ctx.clearRect(0, 0, this.size, this.size);

      // Outer drop shadow
      ctx.save();
      ctx.beginPath();
      ctx.arc(center, center, radius + 10, 0, 2 * Math.PI);
      ctx.fillStyle = '#0a0e0c';
      ctx.shadowColor = 'rgba(0, 200, 83, 0.35)';
      ctx.shadowBlur = 24;
      ctx.fill();
      ctx.restore();

      // Outer metallic & white border ring (matching graphic)
      ctx.save();
      ctx.beginPath();
      ctx.arc(center, center, radius + 8, 0, 2 * Math.PI);
      ctx.lineWidth = 14;
      ctx.strokeStyle = '#ffffff';
      ctx.stroke();

      ctx.beginPath();
      ctx.arc(center, center, radius + 1, 0, 2 * Math.PI);
      ctx.lineWidth = 4;
      ctx.strokeStyle = '#00c853';
      ctx.stroke();
      ctx.restore();

      // Slices
      ctx.save();
      ctx.translate(center, center);
      ctx.rotate(this.currentRotation);

      for (let i = 0; i < this.numSlices; i++) {
        const slice = this.slices[i];
        const startAngle = i * this.arc;
        const endAngle = startAngle + this.arc;

        // Slice wedge
        ctx.beginPath();
        ctx.moveTo(0, 0);
        ctx.arc(0, 0, radius, startAngle, endAngle);
        ctx.closePath();
        ctx.fillStyle = slice.color;
        ctx.fill();

        // Wedge separator border
        ctx.lineWidth = 2.5;
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.4)';
        ctx.stroke();

        // Slice radial text
        ctx.save();
        ctx.rotate(startAngle + this.arc / 2);
        ctx.textAlign = 'right';
        ctx.textBaseline = 'middle';
        ctx.fillStyle = slice.textColor;

        // Dynamic font sizing
        ctx.font = 'bold 11px "Outfit", "Syne", sans-serif';
        ctx.shadowColor = 'rgba(0, 0, 0, 0.9)';
        ctx.shadowBlur = 4;

        // Text formatting: wrap if long
        const words = slice.label.split(' ');
        if (words.length > 3) {
          const line1 = words.slice(0, 2).join(' ');
          const line2 = words.slice(2).join(' ');
          ctx.fillText(line1, radius - 26, -7);
          ctx.fillText(line2, radius - 26, 8);
        } else {
          ctx.fillText(slice.label, radius - 26, 0);
        }

        ctx.restore();
      }

      // Perimeter Rivet Pegs
      for (let i = 0; i < this.numSlices; i++) {
        const angle = i * this.arc;
        const px = Math.cos(angle) * (radius + 2);
        const py = Math.sin(angle) * (radius + 2);

        ctx.beginPath();
        ctx.arc(px, py, 4, 0, 2 * Math.PI);
        ctx.fillStyle = '#ffd54f';
        ctx.fill();
        ctx.lineWidth = 1;
        ctx.strokeStyle = '#000000';
        ctx.stroke();
      }

      ctx.restore(); // restore wheel rotation

      // Fixed Center Hub with William Lawson's Scotch Badge
      this.drawCenterHub();

      // Fixed Pointer Needle at North (12 o'clock position)
      this.drawPointer();
    }

    drawCenterHub() {
      const ctx = this.ctx;
      const center = this.center;
      const hubRadius = this.radius * 0.32;

      ctx.save();
      // Outer brass hub ring with rich drop shadow
      ctx.beginPath();
      ctx.arc(center, center, hubRadius + 6, 0, 2 * Math.PI);
      ctx.fillStyle = '#000000';
      ctx.shadowColor = 'rgba(0, 0, 0, 0.95)';
      ctx.shadowBlur = 18;
      ctx.fill();
      ctx.lineWidth = 4;
      ctx.strokeStyle = '#ffd54f'; // Gold trim
      ctx.stroke();

      // Inner Badge circle - solid black background (replacing green)
      ctx.beginPath();
      ctx.arc(center, center, hubRadius, 0, 2 * Math.PI);
      ctx.fillStyle = '#000000'; // Black background
      ctx.fill();
      ctx.lineWidth = 2;
      ctx.strokeStyle = 'rgba(255, 213, 79, 0.6)'; // Gold inner border
      ctx.stroke();

      // Official William Lawson's logo.svg in the middle of the wheel
      if (this.logoImg && this.logoImg.complete && this.logoImg.naturalWidth > 0) {
        // logo.svg natural aspect ratio is 136 x 83
        const logoW = hubRadius * 1.55;
        const logoH = logoW * (83 / 136);
        ctx.drawImage(this.logoImg, center - (logoW / 2), center - (logoH / 2), logoW, logoH);
      } else {
        // Fallback typography on black background if image still loading
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillStyle = '#ffffff';
        ctx.font = '900 12px "Bebas Neue", sans-serif';
        ctx.fillText("WILLIAM", center, center - 12);
        ctx.fillText("LAWSON'S", center, center + 2);

        ctx.fillStyle = '#ffd54f';
        ctx.font = 'bold 8px "Outfit", sans-serif';
        ctx.fillText("ESTP 1849", center, center + 15);
      }

      ctx.restore();
    }

    drawPointer() {
      const ctx = this.ctx;
      const center = this.center;
      const radius = this.radius;

      // Pointer is positioned at NORTH (12 o'clock, top) pointing DOWN into the wheel
      ctx.save();

      const tipX = center;
      const tipY = center - radius + 14; // Penetrates slightly past the perimeter into the winning slice
      const baseY = center - radius - 18; // Base sits at top rim

      // Drop shadow for 3D depth
      ctx.shadowColor = 'rgba(0, 0, 0, 0.85)';
      ctx.shadowBlur = 12;
      ctx.shadowOffsetY = 4;

      // 1. Triangular Pointer Needle pointing South from North edge
      ctx.beginPath();
      ctx.moveTo(tipX, tipY);
      ctx.lineTo(center - 18, baseY);
      ctx.lineTo(center + 18, baseY);
      ctx.closePath();

      // Premium metallic gold gradient
      const ptrGrad = ctx.createLinearGradient(center - 18, baseY, center + 18, tipY);
      ptrGrad.addColorStop(0, '#ffe082');
      ptrGrad.addColorStop(0.45, '#ffd54f');
      ptrGrad.addColorStop(1, '#ff8f00');
      ctx.fillStyle = ptrGrad;
      ctx.fill();

      ctx.lineWidth = 2.5;
      ctx.strokeStyle = '#ffffff';
      ctx.stroke();

      // 2. Center Spine Highlight for metallic 3D crease
      ctx.beginPath();
      ctx.moveTo(tipX, tipY);
      ctx.lineTo(center, baseY);
      ctx.lineWidth = 1.5;
      ctx.strokeStyle = 'rgba(0, 0, 0, 0.28)';
      ctx.stroke();

      // 3. Top Anchor Cap & Emerald Rivet
      ctx.beginPath();
      ctx.arc(center, baseY, 9, 0, 2 * Math.PI);
      ctx.fillStyle = '#ffd54f';
      ctx.fill();
      ctx.lineWidth = 2;
      ctx.strokeStyle = '#ffffff';
      ctx.stroke();

      // Highlandah Green emerald center jewel
      ctx.beginPath();
      ctx.arc(center, baseY, 4.5, 0, 2 * Math.PI);
      ctx.fillStyle = '#00c853';
      ctx.fill();

      ctx.restore();
    }

    /**
     * Determines which slice is currently under the pointer at North (12 o'clock / 270 degrees)
     */
    getCurrentSliceAtPointer() {
      const twoPi = 2 * Math.PI;
      const pointerPos = 1.5 * Math.PI; // North (12 o'clock / 270 degrees)
      const normalizedRotation = ((this.currentRotation % twoPi) + twoPi) % twoPi;
      const localAngle = ((pointerPos - normalizedRotation) % twoPi + twoPi) % twoPi;
      const sliceIndex = Math.floor(localAngle / this.arc) % this.numSlices;
      return this.slices[sliceIndex];
    }

    /**
     * Spins the wheel and selects a random challenge landing precisely at North
     */
    spin(onComplete) {
      if (this.isSpinning) return;
      this.initAudio();

      this.isSpinning = true;
      this.selectedSlice = null;

      // Pick a random target slice index
      const targetIndex = Math.floor(Math.random() * this.numSlices);
      const targetSlice = this.slices[targetIndex];

      // Calculate required final rotation angle so target slice lands at North (1.5 * Math.PI)
      const twoPi = 2 * Math.PI;
      const pointerPos = 1.5 * Math.PI; // North (12 o'clock)
      const targetCenterAngle = (targetIndex + 0.5) * this.arc;
      // Add slight jitter (+-12% of arc) so it doesn't land dead center every time
      const jitter = (Math.random() - 0.5) * (this.arc * 0.35);
      
      const fullRotations = (6 + Math.floor(Math.random() * 3)) * twoPi; // 6 to 8 full revolutions
      const currentNorm = ((this.currentRotation % twoPi) + twoPi) % twoPi;
      
      const neededEndAngle = ((pointerPos - (targetCenterAngle + jitter)) % twoPi + twoPi) % twoPi;
      let deltaAngle = (neededEndAngle - currentNorm);
      if (deltaAngle < 0) deltaAngle += twoPi;

      const totalSpinAngle = fullRotations + deltaAngle;
      const startRotation = this.currentRotation;
      const finalRotation = startRotation + totalSpinAngle;

      const duration = 4800 + Math.random() * 800; // ~5 seconds
      const startTime = performance.now();

      // Custom smooth deceleration cubic-bezier easing
      const easeOut = (t) => 1 - Math.pow(1 - t, 3.8);

      const animate = (now) => {
        const elapsed = now - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const easedProgress = easeOut(progress);

        this.currentRotation = startRotation + (totalSpinAngle * easedProgress);
        this.drawWheel();

        // Mechanical tick sound detection as pegs pass pointer
        const currentSlice = this.getCurrentSliceAtPointer();
        if (currentSlice && currentSlice.id !== this.lastPegIndex) {
          this.lastPegIndex = currentSlice.id;
          this.playTickSound();
        }

        if (progress < 1) {
          requestAnimationFrame(animate);
        } else {
          this.currentRotation = finalRotation;
          this.drawWheel();
          this.isSpinning = false;
          this.selectedSlice = targetSlice;
          this.playWinSound();

          if (typeof onComplete === 'function') {
            onComplete(targetSlice);
          }
        }
      };

      requestAnimationFrame(animate);
    }
  }

  // Expose to window
  window.HighlandahWheel = HighlandahWheel;
  window.WHEEL_SLICES = WHEEL_SLICES;
})();
