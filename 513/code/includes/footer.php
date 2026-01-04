<?php
// includes/footer.php
?>
    </main>
    
    <!-- Footer Section -->
    <footer class="container" style="margin-top: 6rem; padding: 4rem 8%; border-top: 1px solid rgba(0,0,0,0.05); background-color: var(--text-main); color: rgba(255,255,255,0.7);">
        <div class="grid" style="text-align: left; gap: 3rem;">
            
            <!-- Column 1: Brand -->
            <div>
                <h5 style="color: white;">The Conscious Home Box</h5>
                <p style="font-size: 0.9rem;">Sustainable living, delivered to your door. Join us in making the world a little greener, one box at a time.</p>
            </div>

            <!-- Column 2: Quick Links -->
            <div>
                <h6 style="color: white;">Quick Links</h6>
                <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.9rem;">
                    <li><a href="<?php echo BASE_URL; ?>/about.php" style="color: inherit; text-decoration: none;">About Us</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/shop.php" style="color: inherit; text-decoration: none;">Shop</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/forum.php" style="color: inherit; text-decoration: none;">Community Forum</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/subscribe.php" style="color: inherit; text-decoration: none;">Subscribe</a></li>
                </ul>
            </div>

            <!-- Column 3: Support -->
            <div>
                <h6 style="color: white;">Support</h6>
                <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.9rem;">
                    <li><a href="<?php echo BASE_URL; ?>/support.php" style="color: inherit; text-decoration: none;">Contact Support</a></li>
                    <li><a href="#" style="color: inherit; text-decoration: none;">Shipping Policy</a></li>
                    <li><a href="#" style="color: inherit; text-decoration: none;">Returns</a></li>
                </ul>
            </div>
            
        </div>

        <div style="text-align: center; margin-top: 3rem; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1); font-size: 0.8rem;">
            <!-- UPDATED: Added Name 'Stephen' per teacher feedback -->
            <small>&copy; <?php echo date("Y"); ?> The Conscious Home Box. Created by Stephen. All Rights Reserved. Hosted on InfinityFree.</small>
        </div>
    </footer>

    <!-- AOS Animation JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
      AOS.init({
        duration: 800, 
        offset: 100,
        once: true, 
        easing: 'ease-out-cubic'
      });
    </script>

    <!-- Local Main JS file -->
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>