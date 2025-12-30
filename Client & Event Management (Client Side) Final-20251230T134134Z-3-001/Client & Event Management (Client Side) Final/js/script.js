// --- GLOBAL STATE ---
let isUserLoggedIn = false;

document.addEventListener('DOMContentLoaded', () => {
    checkSession();
    createToastContainer(); // Init Toast System
    setupBookingRestrictions();
    handlePageToasts(); // NEW: Checks if we need to show a toast on load
});

// --- 1. TOAST NOTIFICATION LOGIC (Global) ---
function createToastContainer() {
    if (!document.getElementById('toast-container')) {
        const container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }
}

function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    if(!container) return;

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    let iconClass = 'fa-info-circle';
    if(type === 'success') iconClass = 'fa-check-circle';
    if(type === 'error') iconClass = 'fa-times-circle';
    if(type === 'warning') iconClass = 'fa-exclamation-triangle';

    toast.innerHTML = `
        <div class="toast-content">
            <i class="fas ${iconClass}"></i>
            <span>${message}</span>
        </div>
        <div class="toast-progress"></div>
    `;

    container.appendChild(toast);
    requestAnimationFrame(() => toast.classList.add('show'));

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            if(container.contains(toast)) container.removeChild(toast);
        }, 300);
    }, 3000);
}

// --- 2. HANDLE REDIRECT TOASTS (NEW) ---
function handlePageToasts() {
    const params = new URLSearchParams(window.location.search);
    
    // Check for 'toast' parameter in URL
    if (params.has('toast')) {
        const msgType = params.get('toast');
        
        if (msgType === 'login_required') {
            showToast("Please sign in to book an event.", "warning");
        } else if (msgType === 'registered') {
            showToast("Account created! Please sign in.", "success");
        } else if (msgType === 'logout') {
            showToast("You have successfully logged out.", "info");
        }

        // Clean up URL (remove the ?toast=... part without refreshing)
        window.history.replaceState({}, document.title, window.location.pathname);
    }
}

// --- 3. SESSION CHECKER ---
async function checkSession() {
    try {
        const response = await fetch('php/api_session.php');
        const data = await response.json();

        isUserLoggedIn = data.logged_in;
        const authContainer = document.getElementById('desktopAuthContainer');
        const userContainer = document.getElementById('userDropdownContainer');
        const mobileAuth = document.querySelector('.mobile-auth');

        if (data.logged_in) {
            if(authContainer) authContainer.style.display = 'none';
            if(mobileAuth) mobileAuth.style.display = 'none';
            if(userContainer) {
                userContainer.style.display = 'block';
                if(data.user && data.user.full_name) {
                    const nameEl = document.getElementById('userNameDisplay');
                    const avatarEl = document.getElementById('userAvatar');
                    if(nameEl) nameEl.textContent = data.user.full_name;
                    if(avatarEl) avatarEl.textContent = data.user.full_name.charAt(0).toUpperCase();
                }
            }
        } else {
            if(authContainer) authContainer.style.display = 'flex';
            if(userContainer) userContainer.style.display = 'none';
        }
    } catch (error) {
        console.error("Session check failed:", error);
    }
}

// --- 4. RESTRICT BOOKING (Immediate Redirect) ---
function setupBookingRestrictions() {
    const bookingLinks = document.querySelectorAll('a[href="book_now.html"]');
    
    bookingLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            if (!isUserLoggedIn) {
                e.preventDefault();
                // Redirect IMMEDIATELY with a flag
                window.location.href = 'login.html?toast=login_required';
            }
        });
    });
}

// --- 5. SLIDER & SCROLL LOGIC (Safe Checks added) ---
const slides = document.querySelectorAll('.slide');
const dots = document.querySelectorAll('.dot');
let currentSlide = 0;
const slideInterval = 5000;

function showSlide(index) {
    if(slides.length === 0) return;
    slides.forEach((slide, i) => {
        slide.classList.remove('active');
        if(dots[i]) dots[i].classList.remove('active');
    });
    slides[index].classList.add('active');
    if(dots[index]) dots[index].classList.add('active');
}

function nextSlide() { currentSlide = (currentSlide + 1) % slides.length; showSlide(currentSlide); }
function prevSlide() { currentSlide = (currentSlide - 1 + slides.length) % slides.length; showSlide(currentSlide); }

if(slides.length > 0) setInterval(nextSlide, slideInterval);

// Scroll Reveal
const revealElements = document.querySelectorAll('.reveal-up');
const revealOnScroll = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('active');
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.1 });
revealElements.forEach(el => revealOnScroll.observe(el));

// Mobile Menu
const hamburger = document.querySelector('.hamburger');
const navLinksContainer = document.querySelector('.nav-links');
const navItems = document.querySelectorAll(".nav-item");

if (hamburger) {
    hamburger.addEventListener('click', () => {
        navLinksContainer.classList.toggle('nav-active');
        hamburger.classList.toggle('toggle');
    });
}
navItems.forEach(link => {
    link.addEventListener('click', () => {
        navLinksContainer.classList.remove('nav-active');
        if(hamburger) hamburger.classList.remove('toggle');
    });
});

// Dropdown
const userDropdown = document.querySelector('.user-dropdown');
if (userDropdown) {
    userDropdown.addEventListener('click', (e) => {
        userDropdown.classList.toggle('active');
        e.stopPropagation();
    });
    document.addEventListener('click', (e) => {
        if (!userDropdown.contains(e.target)) userDropdown.classList.remove('active');
    });
}