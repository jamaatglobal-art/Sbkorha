/**
 * মেইন জাভাস্ক্রিপ্ট ফাইল
 * সবারকথা নিউজ পোর্টাল
 */

// ডাম করা বিষয়বস্তু লগ করার জন্য পরিবেশ চেক করুন
const isDevelopment = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';

/**
 * ডেবাগ লগ ফাংশন
 */
function debugLog(message, data = null) {
    if (isDevelopment) {
        if (data) {
            console.log(`[Sbkorha Debug] ${message}`, data);
        } else {
            console.log(`[Sbkorha Debug] ${message}`);
        }
    }
}

/**
 * মোবাইল মেনু টগল
 */
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const navMenu = document.getElementById('nav-menu');
    
    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            debugLog('Mobile menu toggled');
        });
        
        // মেনু আইটেম ক্লিক করলে মেনু বন্ধ করুন
        const navLinks = navMenu.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                navMenu.classList.remove('active');
            });
        });
    }
});

/**
 * স্মুথ স্ক্রোল আচরণ
 */
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        
        // শুধুমাত্র বৈধ অ্যাঙ্করের জন্য ডিফল্ট আচরণ প্রতিরোধ করুন
        if (href !== '#' && document.querySelector(href)) {
            e.preventDefault();
            const target = document.querySelector(href);
            
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
            
            debugLog(`Smooth scrolled to ${href}`);
        }
    });
});

/**
 * লেজি লোডিং ইমেজের জন্য IntersectionObserver
 */
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                
                // ডেটা-src থেকে src সেট করুন
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    debugLog(`Image loaded: ${img.dataset.src}`);
                }
                
                // পর্যবেক্ষক থেকে এটি সরান
                imageObserver.unobserve(img);
            }
        });
    }, {
        rootMargin: '50px'
    });
    
    // সমস্ত ছবি পর্যবেক্ষণ করুন
    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

/**
 * সার্চ ফাংশনালিটি
 */
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.querySelector('.search-form');
    
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const searchInput = this.querySelector('.search-input');
            
            if (!searchInput.value.trim()) {
                e.preventDefault();
                searchInput.focus();
                debugLog('Search input is empty');
            }
        });
    }
});

/**
 * AJAX পেজিনেশন লোডার
 */
function loadMorePosts(categoryId = null, page = 2) {
    const url = new URL(window.location.origin + '/api/get-posts.php');
    
    if (categoryId) {
        url.searchParams.append('category_id', categoryId);
    }
    url.searchParams.append('page', page);
    
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const newsGrid = document.querySelector('.news-grid');
                
                if (newsGrid) {
                    newsGrid.insertAdjacentHTML('beforeend', data.html);
                    debugLog(`Loaded ${data.count} more posts`);
                }
            } else {
                console.error('Error loading posts:', data.message);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
}

/**
 * নিউজলেটার সাবস্ক্রিপশন হ্যান্ডলার
 * (এটি footer.php-তেও আছে, তবে অপ্টিমাইজেশনের জন্য)
 */
function handleNewsletterSubscription() {
    const form = document.getElementById('newsletter-form');
    
    if (!form) return;
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const email = this.querySelector('.newsletter-input');
        const messageDiv = document.getElementById('newsletter-message');
        
        if (!email || !messageDiv) return;
        
        try {
            const response = await fetch('/api/subscribe-newsletter.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email: email.value })
            });
            
            const data = await response.json();
            
            messageDiv.textContent = data.message;
            messageDiv.className = 'newsletter-message ' + (data.success ? 'success' : 'error');
            
            if (data.success) {
                this.reset();
                debugLog('Newsletter subscription successful:', data.message);
            }
        } catch (error) {
            messageDiv.textContent = 'একটি সমস্যা হয়েছে। পরে আবার চেষ্টা করুন।';
            messageDiv.className = 'newsletter-message error';
            console.error('Newsletter subscription error:', error);
        }
    });
}

// পৃষ্ঠা লোড হওয়ার পরে নিউজলেটার হ্যান্ডলার চালু করুন
document.addEventListener('DOMContentLoaded', handleNewsletterSubscription);

/**
 * স্টোরেজ রিলেটেড ফাংশন
 */
const StorageManager = {
    /**
     * লোকাল স্টোরেজে ডেটা সেট করুন
     */
    set: (key, value, expiryMinutes = null) => {
        const item = {
            value: value,
            timestamp: Date.now()
        };
        
        if (expiryMinutes) {
            item.expiry = Date.now() + (expiryMinutes * 60 * 1000);
        }
        
        localStorage.setItem(key, JSON.stringify(item));
        debugLog(`Storage set: ${key}`);
    },
    
    /**
     * লোকাল স্টোরেজ থেকে ডেটা পান
     */
    get: (key) => {
        const item = localStorage.getItem(key);
        
        if (!item) return null;
        
        const parsed = JSON.parse(item);
        
        // এক্সপায়ারি চেক করুন
        if (parsed.expiry && parsed.expiry < Date.now()) {
            StorageManager.remove(key);
            return null;
        }
        
        return parsed.value;
    },
    
    /**
     * লোকাল স্টোরেজ থেকে ডেটা সরান
     */
    remove: (key) => {
        localStorage.removeItem(key);
        debugLog(`Storage removed: ${key}`);
    },
    
    /**
     * সমস্ত লোকাল স্টোরেজ ক্লিয়ার করুন
     */
    clear: () => {
        localStorage.clear();
        debugLog('Storage cleared');
    }
};

/**
 * স্ক্রল ইভেন্ট লিসেনার - ফ্লোটিং বাটন ইত্যাদির জন্য
 */
const ScrollManager = {
    lastScrollY: 0,
    
    init: () => {
        window.addEventListener('scroll', ScrollManager.onScroll, { passive: true });
    },
    
    onScroll: () => {
        ScrollManager.lastScrollY = window.scrollY;
        
        // "টপে যান" বাটন দৃশ্যমানতা
        const backToTopBtn = document.getElementById('back-to-top');
        if (backToTopBtn) {
            if (ScrollManager.lastScrollY > 300) {
                backToTopBtn.style.display = 'block';
            } else {
                backToTopBtn.style.display = 'none';
            }
        }
    }
};

/**
 * পৃষ্ঠা লোড হওয়ার পরে সমস্ত ফাংশন ইনিশিয়ালাইজ করুন
 */
document.addEventListener('DOMContentLoaded', function() {
    debugLog('Document ready - initializing app');
    
    // স্ক্রল ম্যানেজার শুরু করুন
    ScrollManager.init();
    
    // অন্যান্য ইনিশিয়ালাইজেশন এখানে যোগ করুন
    debugLog('App initialization complete');
});

/**
 * উইন্ডো আনলোড হলে ক্লিনআপ
 */
window.addEventListener('beforeunload', function() {
    // যদি প্রয়োজন হয় তবে সংরক্ষণ করা ডেটা ক্লিয়ার করুন
    debugLog('Page unloading');
});

/**
 * নেটওয়ার্ক স্ট্যাটাস চেক
 */
window.addEventListener('online', () => {
    console.log('Connection restored');
    debugLog('Online status: connected');
});

window.addEventListener('offline', () => {
    console.log('Connection lost');
    debugLog('Online status: disconnected');
});

/**
 * কনসোল টেস্ট মেসেজ
 */
console.log('%c🔴 সবারকথা নিউজ পোর্টাল', 'color: #E8504B; font-size: 16px; font-weight: bold;');
console.log('%cডেভেলপড বাই: সবারকথা টেকনিক্যাল টিম', 'color: #666; font-size: 12px;');
