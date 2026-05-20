<?php
/**
 * ফুটার কম্পোনেন্ট
 * সবারকথা নিউজ পোর্টাল
 */
?>

    </main>
    <!-- মূল কন্টেন্ট শেষ -->

    <!-- ফুটার শুরু -->
    <footer class="footer">
        <!-- নিউজলেটার সাবস্ক্রিপশন সেকশন -->
        <section class="newsletter-section">
            <div class="container">
                <div class="newsletter-content">
                    <div class="newsletter-text">
                        <h3>নিউজলেটার সাবস্ক্রাইব করুন</h3>
                        <p>সর্বশেষ খবর সরাসরি আপনার ইমেইলে পান</p>
                    </div>
                    <form id="newsletter-form" class="newsletter-form">
                        <div class="newsletter-input-wrapper">
                            <input 
                                type="email" 
                                class="newsletter-input" 
                                placeholder="আপনার ইমেইল দিন..." 
                                required
                                aria-label="নিউজলেটার ইমেইল"
                            >
                            <button type="submit" class="newsletter-btn" aria-label="সাবস্ক্রাইব করুন">
                                সাবস্ক্রাইব করুন
                            </button>
                        </div>
                        <div id="newsletter-message" class="newsletter-message"></div>
                    </form>
                </div>
            </div>
        </section>

        <!-- মেইন ফুটার -->
        <section class="footer-main">
            <div class="container">
                <div class="footer-grid">
                    <!-- সম্পর্কে -->
                    <div class="footer-column">
                        <h4>আমাদের সম্পর্কে</h4>
                        <p>
                            <?php echo escapeOutput(SITE_NAME); ?> হল একটি নির্ভরযোগ্য অনলাইন নিউজ মিডিয়া যা 
                            সর্বশেষ এবং সবচেয়ে গুরুত্বপূর্ণ খবর আপনার কাছে নিয়ে আসে।
                        </p>
                        <div class="footer-social">
                            <a href="<?php echo escapeOutput(FACEBOOK_URL); ?>" target="_blank" title="ফেসবুক" aria-label="ফেসবুক">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="<?php echo escapeOutput(TWITTER_URL); ?>" target="_blank" title="টুইটার" aria-label="টুইটার">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="<?php echo escapeOutput(YOUTUBE_URL); ?>" target="_blank" title="ইউটিউব" aria-label="ইউটিউব">
                                <i class="fab fa-youtube"></i>
                            </a>
                            <a href="<?php echo escapeOutput(INSTAGRAM_URL); ?>" target="_blank" title="ইনস্টাগ্রাম" aria-label="ইনস্টাগ্রাম">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </div>
                    </div>

                    <!-- দ্রুত লিংক -->
                    <div class="footer-column">
                        <h4>দ্রুত লিংক</h4>
                        <ul class="footer-links">
                            <li><a href="/">হোম</a></li>
                            <li><a href="/about.php">আমাদের সম্পর্কে</a></li>
                            <li><a href="/contact.php">যোগাযোগ</a></li>
                            <li><a href="/privacy.php">গোপনীয়তা নীতি</a></li>
                            <li><a href="/terms.php">শর্তাবলী</a></li>
                        </ul>
                    </div>

                    <!-- ক্যাটাগরি -->
                    <div class="footer-column">
                        <h4>ক্যাটাগরি</h4>
                        <ul class="footer-links">
                            <?php 
                            // ফুটারে ক্যাটাগরি লিংক দেখান
                            if (!empty($categories)) {
                                foreach (array_slice($categories, 0, 5) as $cat) {
                                    echo '<li><a href="/category/' . escapeOutput($cat['slug']) . '">' . escapeOutput($cat['name']) . '</a></li>';
                                }
                            }
                            ?>
                        </ul>
                    </div>

                    <!-- যোগাযোগ তথ্য -->
                    <div class="footer-column">
                        <h4>যোগাযোগ করুন</h4>
                        <div class="contact-info">
                            <p>
                                <i class="fas fa-envelope"></i>
                                <a href="mailto:<?php echo escapeOutput(SITE_EMAIL); ?>">
                                    <?php echo escapeOutput(SITE_EMAIL); ?>
                                </a>
                            </p>
                            <p>
                                <i class="fas fa-phone"></i>
                                <a href="tel:+8801700000000">+880 1700 000 000</a>
                            </p>
                            <p>
                                <i class="fas fa-map-marker-alt"></i>
                                ঢাকা, বাংলাদেশ
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ফুটার বটম -->
        <div class="footer-bottom">
            <div class="container">
                <div class="footer-bottom-content">
                    <p>&copy; <?php echo date('Y'); ?> 
                        <strong><?php echo escapeOutput(SITE_NAME); ?></strong>. 
                        সর্বস্বত্ব সংরক্ষিত।
                    </p>
                    <p class="footer-credits">
                        ডিজাইন ও ডেভেলপমেন্ট: 
                        <strong><?php echo escapeOutput(SITE_NAME); ?> টেকনিক্যাল টিম</strong>
                    </p>
                </div>
            </div>
        </div>
    </footer>
    <!-- ফুটার শেষ -->

    <!-- "উপরে যান" বাটন -->
    <button id="back-to-top" class="back-to-top" title="উপরে যান" aria-label="উপরে যান">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- জাভাস্ক্রিপ্ট ফাইল -->
    <script src="/js/main.js"></script>

    <!-- উপরে যান বাটন ফাংশনালিটি -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const backToTopBtn = document.getElementById('back-to-top');
            
            if (backToTopBtn) {
                backToTopBtn.addEventListener('click', function() {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }
        });
    </script>
</body>
</html>
