document.addEventListener('DOMContentLoaded', () => {

    // Stagger reveal timings (no extra markup)
    const revealEls = document.querySelectorAll('.reveal');
    revealEls.forEach((el, i) => {
        const d = Math.min(i * 60, 600); // cap delay
        el.style.setProperty('--d', `${d}ms`);
    });

    // Custom cursor + spotlight (pointer devices + no reduced-motion)
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const isFinePointer = window.matchMedia('(pointer: fine)').matches;

    const cursorDot  = document.getElementById('cursor-dot');
    const cursorRing = document.getElementById('cursor-ring');
    const cursorGlow = document.getElementById('cursor-glow');

    if (isFinePointer && cursorDot && cursorRing && cursorGlow) {
        let mx = -400, my = -400; // start offscreen
        let rx = -400, ry = -400;
        let visible = false;

        const lerp = (a, b, t) => a + (b - a) * t;

        const showCursor = () => {
            cursorDot.style.opacity  = '1';
            cursorRing.style.opacity = '0.9';
            cursorGlow.style.opacity = '0.9';
        };
        const hideCursor = () => {
            cursorDot.style.opacity  = '0';
            cursorRing.style.opacity = '0';
            cursorGlow.style.opacity = '0';
        };

        // Move dot + glow instantly (GPU transform, no reflow)
        const moveDot = (x, y) => {
            cursorDot.style.transform  = `translate(${x - 3}px, ${y - 3}px)`;
            cursorGlow.style.transform = `translate(${x - 350}px, ${y - 350}px)`;
        };

        window.addEventListener('mousemove', (e) => {
            mx = e.clientX;
            my = e.clientY;
            moveDot(mx, my);
            // Reveal on first move — fixes disappear after page navigation
            if (!visible) { visible = true; showCursor(); }
        }, { passive: true });

        // Hide when pointer leaves the viewport
        document.addEventListener('mouseleave', hideCursor);
        document.addEventListener('mouseenter', showCursor);

        // Ring lags with lerp — smooth trailing feel
        // JS owns both position AND size so offset = size/2 is always in sync
        let ringSize = 38;
        const RING_DEFAULT = 38, RING_HOVER = 60;

        if (!reduce) {
            const animateRing = () => {
                rx = lerp(rx, mx, 0.12);
                ry = lerp(ry, my, 0.12);
                const targetSize = document.body.classList.contains('cursor-hover') ? RING_HOVER : RING_DEFAULT;
                ringSize = lerp(ringSize, targetSize, 0.15);
                const half = ringSize / 2;
                cursorRing.style.width  = `${ringSize}px`;
                cursorRing.style.height = `${ringSize}px`;
                cursorRing.style.transform = `translate(${rx - half}px, ${ry - half}px)`;
                requestAnimationFrame(animateRing);
            };
            animateRing();
        } else {
            window.addEventListener('mousemove', (e) => {
                cursorRing.style.transform = `translate(${e.clientX - 19}px, ${e.clientY - 19}px)`;
            }, { passive: true });
        }

        // Hover detection — expand ring, tint dot
        const hoverTargets = document.querySelectorAll(
            'a, button, [role=button], .tag-filter, .tag-pill, .btn, label, .project-card, .case-nav-link'
        );
        hoverTargets.forEach(el => {
            el.addEventListener('mouseenter', () => document.body.classList.add('cursor-hover'));
            el.addEventListener('mouseleave', () => document.body.classList.remove('cursor-hover'));
        });

        // Click ripple — two concentric rings, glow flash
        document.addEventListener('click', (e) => {
            const x = e.clientX;
            const y = e.clientY;

            // Inner ring — fast
            const r1 = document.createElement('div');
            r1.className = 'cursor-ripple cursor-ripple--inner';
            r1.style.left = `${x}px`;
            r1.style.top  = `${y}px`;
            document.body.appendChild(r1);
            r1.addEventListener('animationend', () => r1.remove());

            // Outer ring — slower, delayed
            const r2 = document.createElement('div');
            r2.className = 'cursor-ripple cursor-ripple--outer';
            r2.style.left = `${x}px`;
            r2.style.top  = `${y}px`;
            document.body.appendChild(r2);
            r2.addEventListener('animationend', () => r2.remove());

            // Glow flash on cursor glow element
            cursorGlow.style.transition = 'opacity 60ms ease';
            cursorGlow.style.opacity = '1';
            setTimeout(() => {
                cursorGlow.style.transition = 'opacity 400ms ease';
                cursorGlow.style.opacity = visible ? '0.9' : '0';
            }, 120);
        });
    }

    // Mobile nav burger toggle
    const burger = document.getElementById('nav-burger');
    const siteNav = document.getElementById('site-nav');
    if (burger && siteNav) {
        burger.addEventListener('click', () => {
            const isOpen = siteNav.classList.toggle('nav--open');
            burger.classList.toggle('nav-burger--open', isOpen);
            burger.setAttribute('aria-expanded', String(isOpen));
        });
        // Close on link click (single-page sections)
        siteNav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                siteNav.classList.remove('nav--open');
                burger.classList.remove('nav-burger--open');
                burger.setAttribute('aria-expanded', 'false');
            });
        });
    }

    // Smooth anchor scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (!target) return;
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth' });
        });
    });

    // Scroll reveal
    const elements = document.querySelectorAll('.reveal');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('reveal-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });

    elements.forEach((element) => {
        observer.observe(element);
    });

    // Active nav link highlighting
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav a');

    window.addEventListener('scroll', () => {
        let current = '';
        sections.forEach(section => {
            if(pageYOffset >= section.offsetTop - 200) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if(link.getAttribute('href') === `#${current}`) {
                link.classList.add('active');
            }
        });
    });

    // Auto-apply tag from query param
    const param = new URLSearchParams(window.location.search).get('tag');
    if (param) {
        const btn = document.querySelector(`.tag-filter[data-tag="${CSS.escape(param)}"]`);
        if (btn) {
            btn.click();
            btn.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
});
