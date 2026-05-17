(function(){
'use strict';

// ── THEME TOGGLE ──
var toggle = document.getElementById('themeToggle');
if (toggle) {
  toggle.addEventListener('click', function(){
    document.body.classList.toggle('light');
    localStorage.setItem('ez-theme', document.body.classList.contains('light') ? 'light' : 'dark');
  });
}

// ── MOBILE MENU ──
var hamburger = document.getElementById('hamburger');
var mobileMenu = document.getElementById('mobileMenu');
if (hamburger && mobileMenu) {
  hamburger.addEventListener('click', function(){
    hamburger.classList.toggle('open');
    mobileMenu.classList.toggle('open');
    document.body.style.overflow = mobileMenu.classList.contains('open') ? 'hidden' : '';
  });
  var mmClose = document.getElementById('mmClose');
  if (mmClose) {
    mmClose.addEventListener('click', function(){
      hamburger.classList.remove('open');
      mobileMenu.classList.remove('open');
      document.body.style.overflow = '';
    });
  }
  document.querySelectorAll('.mm-link').forEach(function(link){
    link.addEventListener('click', function(){
      hamburger.classList.remove('open');
      mobileMenu.classList.remove('open');
      document.body.style.overflow = '';
    });
  });
}

// ── LOADER ──
var loaderLogo = document.getElementById('loaderLogo');
if (loaderLogo) {
  var brand = 'LUXURY COPRO';
  brand.split('').forEach(function(c, i){
    var s = document.createElement('span');
    s.textContent = c === ' ' ? ' ' : c;
    s.style.animationDelay = i * 0.055 + 's';
    loaderLogo.appendChild(s);
  });
  window.addEventListener('load', function(){
    setTimeout(function(){
      var loader = document.getElementById('loader');
      if (loader) loader.classList.add('done');
    }, 2200);
  });
}

// ── CURSOR ──
var dot = document.getElementById('cDot');
var ring = document.getElementById('cRing');
if (dot && ring) {
  var mx = 0, my = 0, rx = 0, ry = 0;
  document.addEventListener('mousemove', function(e){ mx = e.clientX; my = e.clientY; });
  (function loop(){
    rx += (mx - rx) * 0.12;
    ry += (my - ry) * 0.12;
    dot.style.transform = 'translate(' + (mx - 3) + 'px,' + (my - 3) + 'px)';
    ring.style.transform = 'translate(' + (rx - 22) + 'px,' + (ry - 22) + 'px)';
    requestAnimationFrame(loop);
  })();
}

// ── NAV SCROLL ──
var nav = document.getElementById('nav');
function updateNavHeight() {
  if (nav) document.documentElement.style.setProperty('--nav-h', nav.offsetHeight + 'px');
}
if (nav) {
  updateNavHeight();
  window.addEventListener('resize', updateNavHeight);
  window.addEventListener('scroll', function(){
    nav.classList.toggle('compact', window.scrollY > 60);
    updateNavHeight();
  });
}

// ── GSAP SCROLL ANIMATIONS ──
if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
  var isMobile = window.innerWidth <= 768;

  // ── Hero parallax layers (desktop only) ──
  if (!isMobile) {
    var heroGlow1 = document.querySelector('.hero-glow-1');
    var heroGlow2 = document.querySelector('.hero-glow-2');
    var heroGrid = document.querySelector('.hero-grid');
    if (heroGlow1) gsap.to(heroGlow1, { scrollTrigger: { trigger: '.hero', start: 'top top', end: 'bottom top', scrub: true }, y: 120, scale: 1.3, ease: 'none' });
    if (heroGlow2) gsap.to(heroGlow2, { scrollTrigger: { trigger: '.hero', start: 'top top', end: 'bottom top', scrub: true }, y: 80, scale: 0.8, ease: 'none' });
    if (heroGrid) gsap.to(heroGrid, { scrollTrigger: { trigger: '.hero', start: 'top top', end: 'bottom top', scrub: true }, y: 60, opacity: 0, ease: 'none' });
  }

  // ── About — text slides in from left with 3D rotation ──
  var about = document.querySelector('.about-intro');
  if (about) {
    gsap.fromTo(about.querySelector('.sec-label'), { opacity: 0, x: isMobile ? -30 : -80, rotateY: isMobile ? 0 : 15 }, { scrollTrigger: { trigger: about, start: 'top 85%', once: true }, opacity: 1, x: 0, rotateY: 0, duration: 0.8, ease: 'power3.out' });
    gsap.fromTo(about.querySelector('.sec-title'), { opacity: 0, x: isMobile ? -30 : -100, rotateY: isMobile ? 0 : 20 }, { scrollTrigger: { trigger: about, start: 'top 85%', once: true }, opacity: 1, x: 0, rotateY: 0, duration: 0.9, delay: 0.15, ease: 'power3.out' });
    var aboutPs = about.querySelectorAll('.about-inner p');
    gsap.fromTo(aboutPs, { opacity: 0, y: isMobile ? 30 : 60, rotateX: isMobile ? 0 : 8 }, { scrollTrigger: { trigger: about, start: 'top 80%', once: true }, opacity: 1, y: 0, rotateX: 0, duration: 0.8, stagger: 0.2, ease: 'power2.out' });
  }

  // ── References — cards flip in with 3D perspective ──
  var refsGrid = document.querySelector('.refs-grid');
  var refsSection = document.querySelector('.refs');
  if (refsSection) {
    gsap.fromTo(refsSection.querySelector('.sec-label'), { opacity: 0, scale: 0.5 }, { scrollTrigger: { trigger: refsSection, start: 'top 85%', once: true }, opacity: 1, scale: 1, duration: 0.6, ease: 'back.out(2)' });
    gsap.fromTo(refsSection.querySelector('.sec-title'), { opacity: 0, y: 30 }, { scrollTrigger: { trigger: refsSection, start: 'top 85%', once: true }, opacity: 1, y: 0, duration: 0.7, delay: 0.1, ease: 'power2.out' });
    var refsSub = refsSection.querySelector('.sec-sub');
    if (refsSub) gsap.fromTo(refsSub, { opacity: 0, y: 20 }, { scrollTrigger: { trigger: refsSection, start: 'top 85%', once: true }, opacity: 1, y: 0, duration: 0.7, delay: 0.2, ease: 'power2.out' });
  }
  if (refsGrid) {
    var refCards = refsGrid.querySelectorAll('.ref-card');
    refCards.forEach(function(card, i){
      if (isMobile) {
        gsap.fromTo(card, { opacity: 0, y: 40, scale: 0.92 }, { scrollTrigger: { trigger: card, start: 'top 90%', once: true }, opacity: 1, y: 0, scale: 1, duration: 0.6, ease: 'power2.out' });
      } else {
        var rotDir = i % 2 === 0 ? -15 : 15;
        gsap.fromTo(card, { opacity: 0, rotateY: rotDir, scale: 0.85, transformPerspective: 800 }, { scrollTrigger: { trigger: refsGrid, start: 'top 82%', once: true }, opacity: 1, rotateY: 0, scale: 1, duration: 0.8, delay: i * 0.15, ease: 'power3.out' });
      }
    });
  }

  // ── Properties — cards rise with 3D tilt and scale ──
  var propGrid = document.querySelector('.prop-grid');
  var propSection = document.querySelector('.properties');
  if (propSection) {
    gsap.fromTo(propSection.querySelectorAll('.sec-label, .sec-title, .sec-sub'), { opacity: 0, y: 40 }, { scrollTrigger: { trigger: propSection, start: 'top 85%', once: true }, opacity: 1, y: 0, duration: 0.7, stagger: 0.1, ease: 'power2.out' });
    var filterBtns = propSection.querySelectorAll('.prop-filters button');
    if (filterBtns.length) gsap.fromTo(filterBtns, { opacity: 0, scale: 0.7 }, { scrollTrigger: { trigger: propSection, start: 'top 85%', once: true }, opacity: 1, scale: 1, duration: 0.5, stagger: 0.08, delay: 0.3, ease: 'back.out(2)' });
  }
  if (propGrid) {
    var propCards = propGrid.querySelectorAll('.prop-card');
    propCards.forEach(function(card, i){
      if (isMobile) {
        gsap.fromTo(card, { opacity: 0, y: 40, scale: 0.95 }, { scrollTrigger: { trigger: card, start: 'top 92%', once: true }, opacity: 1, y: 0, scale: 1, duration: 0.6, ease: 'power2.out' });
      } else {
        gsap.fromTo(card, { opacity: 0, y: 80, rotateX: 12, scale: 0.9, transformPerspective: 1000 }, { scrollTrigger: { trigger: propGrid, start: 'top 85%', once: true }, opacity: 1, y: 0, rotateX: 0, scale: 1, duration: 0.8, delay: i * 0.12, ease: 'power3.out' });
      }
    });
  }

  // ── Services — items slide from alternating sides with rotateY ──
  var srvGrid = document.querySelector('.srv-grid');
  var srvSection = document.querySelector('.services-row');
  if (srvSection) {
    gsap.fromTo(srvSection.querySelectorAll('.sec-label, .sec-title'), { opacity: 0, y: 40 }, { scrollTrigger: { trigger: srvSection, start: 'top 85%', once: true }, opacity: 1, y: 0, duration: 0.7, stagger: 0.12, ease: 'power2.out' });
  }
  if (srvGrid) {
    var srvItems = srvGrid.querySelectorAll('.srv-item');
    srvItems.forEach(function(item, i){
      if (isMobile) {
        gsap.fromTo(item, { opacity: 0, y: 40 }, { scrollTrigger: { trigger: item, start: 'top 92%', once: true }, opacity: 1, y: 0, duration: 0.6, ease: 'power2.out' });
      } else {
        var fromX = i % 2 === 0 ? -60 : 60;
        gsap.fromTo(item, { opacity: 0, x: fromX, rotateY: i % 2 === 0 ? -10 : 10, transformPerspective: 600 }, { scrollTrigger: { trigger: srvGrid, start: 'top 82%', once: true }, opacity: 1, x: 0, rotateY: 0, duration: 0.8, delay: i * 0.15, ease: 'power3.out' });
      }
    });
  }

  // ── Service Detail blocks — unfold from top with rotateX ──
  var srvDetail = document.querySelector('.srv-detail-grid');
  if (srvDetail) {
    var srvBlocks = srvDetail.querySelectorAll('.srv-block');
    srvBlocks.forEach(function(block, i){
      if (isMobile) {
        gsap.fromTo(block, { opacity: 0, y: 30 }, { scrollTrigger: { trigger: block, start: 'top 92%', once: true }, opacity: 1, y: 0, duration: 0.6, ease: 'power2.out' });
      } else {
        gsap.fromTo(block, { opacity: 0, rotateX: -20, y: 50, transformOrigin: 'top center', transformPerspective: 800 }, { scrollTrigger: { trigger: srvDetail, start: 'top 85%', once: true }, opacity: 1, rotateX: 0, y: 0, duration: 0.9, delay: i * 0.15, ease: 'power3.out' });
      }
    });
  }

  // ── Engagements — cards spiral in (desktop) / fade up (mobile) ──
  var engageGrid = document.querySelector('.engage-grid');
  var engageSection = document.querySelector('.why');
  if (engageSection) {
    gsap.fromTo(engageSection.querySelectorAll('.sec-label, .sec-title'), { opacity: 0, y: 30 }, { scrollTrigger: { trigger: engageSection, start: 'top 85%', once: true }, opacity: 1, y: 0, duration: 0.8, stagger: 0.12, ease: 'power2.out' });
  }
  if (engageGrid) {
    var engCards = engageGrid.querySelectorAll('.engage-card');
    if (isMobile) {
      engCards.forEach(function(card){
        gsap.fromTo(card, { opacity: 0, y: 30, scale: 0.95 }, { scrollTrigger: { trigger: card, start: 'top 92%', once: true }, opacity: 1, y: 0, scale: 1, duration: 0.6, ease: 'power2.out' });
      });
    } else {
      var angles = [-12, 8, -8, 12, -6];
      engCards.forEach(function(card, i){
        gsap.fromTo(card, { opacity: 0, scale: 0.7, rotation: angles[i] || 0, y: 60, transformPerspective: 800 }, { scrollTrigger: { trigger: engageGrid, start: 'top 85%', once: true }, opacity: 1, scale: 1, rotation: 0, y: 0, duration: 0.8, delay: i * 0.12, ease: 'elastic.out(1, 0.75)' });
      });
    }
  }
  var ambitionBox = document.querySelector('.ambition-box');
  if (ambitionBox) {
    gsap.fromTo(ambitionBox, { opacity: 0, y: isMobile ? 30 : 0, scaleX: isMobile ? 1 : 0.8, rotateY: isMobile ? 0 : -8, transformPerspective: 800 }, { scrollTrigger: { trigger: ambitionBox, start: 'top 90%', once: true }, opacity: 1, y: 0, scaleX: 1, rotateY: 0, duration: 1, ease: 'power3.out' });
  }

  // ── Stats — items pop in with bounce ──
  var statsGrid = document.querySelector('.stats-grid');
  if (statsGrid) {
    var statItems = statsGrid.querySelectorAll('.st');
    statItems.forEach(function(st, i){
      gsap.fromTo(st, { opacity: 0, scale: isMobile ? 0.8 : 0.4, y: 30 }, { scrollTrigger: { trigger: isMobile ? st : statsGrid, start: 'top 90%', once: true }, opacity: 1, scale: 1, y: 0, duration: 0.7, delay: isMobile ? 0 : i * 0.12, ease: 'back.out(1.7)' });
    });
  }

  // ── CTA — zoom in with 3D depth ──
  var ctaSection = document.querySelector('.cta');
  if (ctaSection) {
    gsap.fromTo(ctaSection.children, { opacity: 0, scale: isMobile ? 0.95 : 0.85, y: 30, rotateX: isMobile ? 0 : 6, transformPerspective: 600 }, { scrollTrigger: { trigger: ctaSection, start: 'top 88%', once: true }, opacity: 1, scale: 1, y: 0, rotateX: 0, duration: 0.8, stagger: 0.15, ease: 'power3.out' });
  }

  // ── Contact — cards slide in from sides (desktop) / fade up (mobile) ──
  var contactSection = document.querySelector('.contact');
  if (contactSection) {
    gsap.fromTo(contactSection.querySelectorAll('.sec-label, .sec-title'), { opacity: 0, y: 30 }, { scrollTrigger: { trigger: contactSection, start: 'top 88%', once: true }, opacity: 1, y: 0, duration: 0.7, stagger: 0.1, ease: 'power2.out' });
    var ctLocCard = contactSection.querySelector('.ct-location-card');
    var ctFormCard = contactSection.querySelector('.ct-form-card');
    if (isMobile) {
      if (ctLocCard) gsap.fromTo(ctLocCard, { opacity: 0, y: 40 }, { scrollTrigger: { trigger: ctLocCard, start: 'top 92%', once: true }, opacity: 1, y: 0, duration: 0.7, ease: 'power2.out' });
      if (ctFormCard) gsap.fromTo(ctFormCard, { opacity: 0, y: 40 }, { scrollTrigger: { trigger: ctFormCard, start: 'top 92%', once: true }, opacity: 1, y: 0, duration: 0.7, ease: 'power2.out' });
    } else {
      if (ctLocCard) gsap.fromTo(ctLocCard, { opacity: 0, x: -60, rotateY: 10, transformPerspective: 800 }, { scrollTrigger: { trigger: ctLocCard, start: 'top 88%', once: true }, opacity: 1, x: 0, rotateY: 0, duration: 0.9, ease: 'power3.out' });
      if (ctFormCard) gsap.fromTo(ctFormCard, { opacity: 0, x: 60, rotateY: -10, transformPerspective: 800 }, { scrollTrigger: { trigger: ctFormCard, start: 'top 88%', once: true }, opacity: 1, x: 0, rotateY: 0, duration: 0.9, delay: 0.15, ease: 'power3.out' });
    }
  }

  // ── Counter animations ──
  function animateCounter(el, target, suffix) {
    var obj = { val: 0 };
    gsap.to(obj, {
      val: target, duration: 2, ease: 'power1.out',
      scrollTrigger: { trigger: el, start: 'top 90%', once: true },
      onUpdate: function(){ el.textContent = Math.round(obj.val) + suffix; }
    });
  }
  document.querySelectorAll('.hero-stat-num').forEach(function(el){
    var text = el.textContent.trim();
    var num = parseInt(text);
    if (isNaN(num)) return;
    var suffix = text.replace(/[0-9]/g, '');
    el.textContent = '0' + suffix;
    animateCounter(el, num, suffix);
  });
  document.querySelectorAll('.st-num').forEach(function(el){
    var text = el.textContent.trim();
    var num = parseInt(text);
    if (isNaN(num)) return;
    var suffix = text.replace(/[0-9]/g, '');
    el.textContent = '0' + suffix;
    animateCounter(el, num, suffix);
  });

} else {
  document.body.classList.add('no-gsap');
  var obs = new IntersectionObserver(function(entries){
    entries.forEach(function(e){
      if (e.isIntersecting) e.target.classList.add('show');
    });
  }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
  document.querySelectorAll('.rv').forEach(function(el){ obs.observe(el); });
}

// ── 3D TILT ──
document.querySelectorAll('.tilt').forEach(function(card){
  card.addEventListener('mousemove', function(e){
    var r = card.getBoundingClientRect();
    var x = (e.clientX - r.left) / r.width - 0.5;
    var y = (e.clientY - r.top) / r.height - 0.5;
    card.style.transform = 'perspective(800px) rotateX(' + (y * -10) + 'deg) rotateY(' + (x * 10) + 'deg) scale(1.02)';
  });
  card.addEventListener('mouseleave', function(){
    card.style.transform = 'perspective(800px) rotateX(0) rotateY(0) scale(1)';
  });
});

// ── PARALLAX FLOATING CARDS ──
var floatBaseTransforms = [
  'rotateY(-12deg) rotateX(4deg)',
  'rotateY(-8deg) rotateX(-3deg)',
  'rotateY(-15deg) rotateX(6deg)'
];
document.addEventListener('mousemove', function(e){
  var x = (e.clientX / innerWidth - 0.5) * 2;
  var y = (e.clientY / innerHeight - 0.5) * 2;
  document.querySelectorAll('.float-card').forEach(function(c, i){
    var sp = (i + 1) * 12;
    c.style.transform = (floatBaseTransforms[i] || '') + ' translate(' + (x * sp) + 'px,' + (y * sp) + 'px)';
  });
});

// ── MARQUEE GSAP ──
var marqueeSection = document.getElementById('marqueeSection');
if (marqueeSection && typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
  gsap.from(marqueeSection, {
    scrollTrigger: { trigger: marqueeSection, start: 'top 85%', once: true },
    opacity: 0, y: 40, duration: 0.9, ease: 'power2.out'
  });
  var bgText = marqueeSection.querySelector('.marquee-bg-text');
  if (bgText) {
    gsap.to(bgText, {
      scrollTrigger: { trigger: marqueeSection, start: 'top bottom', end: 'bottom top', scrub: true },
      x: 80, ease: 'none'
    });
  }
}

// ── FILTER BUTTONS ──
document.querySelectorAll('.prop-filters button').forEach(function(btn){
  btn.addEventListener('click', function(){
    document.querySelectorAll('.prop-filters button').forEach(function(b){ b.classList.remove('active'); });
    btn.classList.add('active');
    var filter = btn.getAttribute('data-filter');
    document.querySelectorAll('.prop-card').forEach(function(card){
      if (filter === 'all' || card.getAttribute('data-type') === filter) {
        card.style.display = '';
      } else {
        card.style.display = 'none';
      }
    });
  });
});

// ── SMOOTH SCROLL ──
document.querySelectorAll('a[href^="#"]').forEach(function(a){
  a.addEventListener('click', function(e){
    e.preventDefault();
    var t = document.querySelector(a.getAttribute('href'));
    if (t) t.scrollIntoView({ behavior: 'smooth' });
  });
});

// ── PROPERTY MODAL ──
var overlay = document.getElementById('modalOverlay');
var propData = (typeof lcProperties !== 'undefined') ? lcProperties : [];
var waNum = (typeof lcData !== 'undefined') ? lcData.whatsapp : '212700727165';
var emailAddr = (typeof lcData !== 'undefined') ? lcData.email : 'ezzine.surgar@gmail.com';

document.querySelectorAll('.prop-card').forEach(function(card, i){
  card.addEventListener('click', function(){
    var d = propData[i];
    if (!d) return;
    document.getElementById('modalIcon').textContent = d.icon;
    var badge = document.getElementById('modalBadge');
    badge.textContent = d.badge;
    badge.className = 'm-badge ' + d.badgeClass;
    document.getElementById('modalPrice').textContent = d.price;
    document.getElementById('modalTitle').textContent = d.title;
    document.getElementById('modalLoc').textContent = d.loc;
    document.getElementById('modalRef').textContent = d.ref;
    var specs = document.getElementById('modalSpecs');
    specs.innerHTML = '';
    for (var j = 0; j < d.specs.length; j += 2) {
      specs.innerHTML += '<div class="ms"><div class="ms-val">' + d.specs[j] + '</div><div class="ms-lbl">' + d.specs[j+1] + '</div></div>';
    }
    document.getElementById('modalDesc').textContent = d.desc;
    var feats = document.getElementById('modalFeatures');
    feats.innerHTML = d.features.map(function(f){ return '<div class="mf">' + f + '</div>'; }).join('');
    var waBtn = document.getElementById('modalWaBtn');
    if (waBtn) waBtn.href = 'https://wa.me/' + waNum + '?text=' + encodeURIComponent('Bonjour, je suis intéressé par le bien ' + d.ref + ' — ' + d.title);
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
  });
});

if (overlay) {
  var modalClose = document.getElementById('modalClose');
  if (modalClose) {
    modalClose.addEventListener('click', function(){
      overlay.classList.remove('open');
      document.body.style.overflow = '';
    });
  }
  overlay.addEventListener('click', function(e){
    if (e.target === overlay) {
      overlay.classList.remove('open');
      document.body.style.overflow = '';
    }
  });
  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape' && overlay.classList.contains('open')) {
      overlay.classList.remove('open');
      document.body.style.overflow = '';
    }
  });
}

// ── CONTACT FORM ──
function getFormData(){
  return {
    name:   document.getElementById('cfName').value.trim(),
    phone:  document.getElementById('cfPhone').value.trim(),
    type:   document.getElementById('cfType').value,
    budget: document.getElementById('cfBudget').value,
    msg:    document.getElementById('cfMsg').value.trim()
  };
}
function formatMsg(d){
  return 'Bonjour Luxury Copro,\n\nNom: ' + d.name + '\nTéléphone: ' + d.phone + '\nProjet: ' + d.type + '\nBudget: ' + d.budget + (d.msg ? '\nMessage: ' + d.msg : '');
}
function showSuccess(btn, original){
  btn.textContent = '✓ Message envoyé !';
  btn.style.background = '#25D366';
  btn.style.color = 'white';
  btn.style.borderColor = '#25D366';
  setTimeout(function(){
    btn.textContent = original;
    btn.style.background = '';
    btn.style.color = '';
    btn.style.borderColor = '';
  }, 3000);
}

var contactForm = document.getElementById('contactForm');
if (contactForm) {
  contactForm.addEventListener('submit', function(e){
    e.preventDefault();
    var d = getFormData();
    if (!d.name || !d.phone) return;
    var text = encodeURIComponent(formatMsg(d));
    window.open('https://wa.me/' + waNum + '?text=' + text, '_blank');
    showSuccess(e.target.querySelector('[type="submit"]'), 'Envoyer via WhatsApp');
    e.target.reset();
  });
}

var emailBtn = document.getElementById('cfEmailBtn');
if (emailBtn) {
  emailBtn.addEventListener('click', function(){
    var d = getFormData();
    if (!d.name || !d.phone) {
      document.getElementById('cfName').reportValidity();
      return;
    }
    var subject = encodeURIComponent('Nouveau contact — ' + d.type + ' — ' + d.name);
    var body = encodeURIComponent(formatMsg(d));
    window.location.href = 'mailto:' + emailAddr + '?subject=' + subject + '&body=' + body;
    showSuccess(emailBtn, 'Envoyer par E-mail');
    contactForm.reset();
  });
}

// ── WHATSAPP WIDGET ──
var waWidget = document.getElementById('waWidget');
var waFab = document.getElementById('waFab');
if (waFab && waWidget) {
  waFab.addEventListener('click', function(){ waWidget.classList.toggle('open'); });
  var waSend = document.getElementById('waSend');
  var waInput = document.getElementById('waInput');
  if (waSend && waInput) {
    waSend.addEventListener('click', function(){
      var msg = waInput.value.trim();
      if (!msg) return;
      window.open('https://wa.me/' + waNum + '?text=' + encodeURIComponent(msg), '_blank');
      waInput.value = '';
      waWidget.classList.remove('open');
    });
    waInput.addEventListener('keydown', function(e){
      if (e.key === 'Enter') waSend.click();
    });
  }
}

})();
