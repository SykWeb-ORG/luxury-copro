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
if (nav) {
  window.addEventListener('scroll', function(){
    nav.classList.toggle('compact', window.scrollY > 60);
  });
}

// ── REVEAL ──
var obs = new IntersectionObserver(function(entries){
  entries.forEach(function(e){
    if (e.isIntersecting) e.target.classList.add('show');
  });
}, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
document.querySelectorAll('.rv').forEach(function(el){ obs.observe(el); });

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
  gsap.registerPlugin(ScrollTrigger);
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
  setTimeout(function(){ waWidget.classList.add('open'); }, 4000);
}

})();
