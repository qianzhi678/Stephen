<?php
// about.php - About Us Page with Map Toggle
require_once __DIR__ . '/config/database.php';
require_once ROOT_PATH . '/includes/header.php';
?>

<!-- Hero Section -->
<section style="text-align: center; padding: 6rem 0; background-color: var(--bg-mint);">
    <h1 data-aos="fade-down" style="font-size: 3.5rem;">About The Conscious Home Box</h1>
    <p data-aos="fade-up" data-aos-delay="200" style="max-width: 700px; margin: 0 auto; font-size: 1.2rem;">
        We are on a mission to make sustainable living accessible, affordable, and beautiful for everyone.
    </p>
</section>

<!-- Story Section (Text Only - Centered) -->
<section class="container" style="margin-top: 4rem; max-width: 800px; text-align: center;">
    <div data-aos="fade-up">
        <h2 style="color: var(--primary); margin-bottom: 2rem; font-size: 2.5rem;">Our Story</h2>
        <p style="font-size: 1.1rem; line-height: 1.8;">
            Founded in 2024, The Conscious Home Box started with a simple idea: reducing household waste shouldn't be a chore. It should be a discovery.
        </p>
        <p style="font-size: 1.1rem; line-height: 1.8; margin-bottom: 3rem;">
            We partner with ethical artisans and sustainable brands to curate boxes that help you transition your home to a zero-waste lifestyle, one room at a time.
        </p>
        
        <div style="background-color: var(--bg-warm); padding: 2rem; border-radius: var(--border-radius); text-align: left;">
            <h4 style="color: var(--primary); text-align: center; margin-bottom: 1.5rem;">Our Core Values</h4>
            <div class="grid">
                <div style="text-align: center;">
                    <span style="font-size: 2rem;">🌱</span>
                    <p><strong>Sustainability First</strong><br>Plastic-free packaging always.</p>
                </div>
                <div style="text-align: center;">
                    <span style="font-size: 2rem;">🤝</span>
                    <p><strong>Ethical Sourcing</strong><br>Fair wages for all makers.</p>
                </div>
                <div style="text-align: center;">
                    <span style="font-size: 2rem;">♻️</span>
                    <p><strong>Circular Economy</strong><br>Designed to biodegrade.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section (Full Width & Switchable) -->
<section style="margin-top: 6rem; padding-bottom: 4rem;">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: end; margin-bottom: 1rem;">
            <div>
                <h2 style="color: var(--primary); margin-bottom: 0;">Visit Our HQ</h2>
                <p>Come say hi! We have a small showroom in Melbourne.</p>
            </div>
            
            <!-- Map Switcher Button -->
            <div>
                <button onclick="toggleMap()" class="outline" style="border-radius: 50px; border-color: var(--text-main); color: var(--text-main);">
                    <span id="btn-icon">🌏</span> 
                    <span id="btn-text">Can't see the map? Switch View</span>
                </button>
            </div>
        </div>

        <!-- Map Container -->
        <div style="height: 500px; width: 100%; position: relative; border-radius: var(--border-radius); overflow: hidden; box-shadow: var(--shadow-md);" data-aos="zoom-in">
            
            <!-- Map 1: Google Maps (Default) -->
            <div id="google-map-container" style="width: 100%; height: 100%;">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.835434509374!2d144.9537353153169!3d-37.817323442021134!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad65d4c2b349649%3A0xb6899234e561db11!2sEnvato!5e0!3m2!1sen!2sau!4v1623123456789!5m2!1sen!2sau" 
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
            </div>

            <!-- Map 2: OpenStreetMap (China Accessible Backup) -->
            <div id="china-map-container" style="width: 100%; height: 100%; display: none;">
                <!-- OpenStreetMap Embed -->
                <iframe 
                    width="100%" 
                    height="100%" 
                    src="https://www.openstreetmap.org/export/embed.html?bbox=144.9500%2C-37.8200%2C144.9600%2C-37.8100&amp;layer=mapnik&amp;marker=-37.8173%2C144.9537" 
                    style="border: 0;">
                </iframe>
                <small style="position: absolute; bottom: 10px; right: 10px; background: white; padding: 2px 5px; border-radius: 4px;">
                    <a href="https://www.openstreetmap.org/?mlat=-37.8173&amp;mlon=144.9537#map=17/-37.8173/144.9537" target="_blank">View Larger Map</a>
                </small>
            </div>

        </div>
    </div>
</section>

<!-- Toggle Script -->
<script>
    function toggleMap() {
        const googleMap = document.getElementById('google-map-container');
        const chinaMap = document.getElementById('china-map-container');
        const btnText = document.getElementById('btn-text');
        
        if (googleMap.style.display === 'none') {
            // Switch back to Google
            googleMap.style.display = 'block';
            chinaMap.style.display = 'none';
            btnText.innerText = "Can't see the map? Switch View";
        } else {
            // Switch to OpenStreetMap (China Friendly)
            googleMap.style.display = 'none';
            chinaMap.style.display = 'block';
            btnText.innerText = "Switch back to Google Maps";
        }
    }
</script>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>