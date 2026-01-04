<?php
// index.php (FINAL VERSION with Eco-Calculator)
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config/database.php';
require_once ROOT_PATH . '/includes/functions.php';
require_once ROOT_PATH . '/includes/header.php';

// --- Call the Pexels API with RANDOMIZED query ---
$search_terms = ['sustainable lifestyle', 'zero waste home', 'eco friendly products', 'natural home'];
$random_query = $search_terms[array_rand($search_terms)];
$gallery_images = get_pexels_images($random_query, 3);
?>

<!-- Main content of the homepage -->
<section id="hero" style="text-align: center; padding: 4rem 0;" data-aos="fade-in">
    <h1>Sustainable Living, Delivered.</h1>
    <p>Discover curated, eco-friendly products for your home, delivered quarterly.</p>
    <a href="http://121.196.229.71/register/" role="button" class="contrast">Get Started Today</a>
</section>

<section id="how-it-works">
    <h2 data-aos="fade-up">How It Works</h2>
    <div class="grid">
        <article data-aos="fade-up" data-aos-delay="0">
            <h5>1. Choose Your Plan</h5>
            <p>Pick a subscription that fits your lifestyle. Cancel anytime.</p>
        </article>
        <article data-aos="fade-up" data-aos-delay="200">
            <h5>2. Receive Your Themed Box</h5>
            <p>Get a curated box for a new room each quarter, filled with amazing products.</p>
        </article>
        <article data-aos="fade-up" data-aos-delay="400">
            <h5>3. Enjoy Your Conscious Home</h5>
            <p>Easily build sustainable habits with products you'll love.</p>
        </article>
    </div>
</section>

<!-- --- NEW: Eco-Impact Calculator Section --- -->
<section id="impact-calculator" style="background-color: #f9f9f9; padding: 3rem; border-radius: 10px; margin-top: 3rem; margin-bottom: 3rem;" data-aos="fade-up">
    <div class="grid">
        <div>
            <h2 style="color: #5c7c65;">🌱 See Your Impact</h2>
            <p>Curious how much plastic you could save by switching to The Conscious Home Box? Enter the number of disposable plastic bottles (shampoo, soap, detergent) you use per month.</p>
        </div>
        <div>
            <label for="bottleInput">Plastic Items per Month:</label>
            <div class="grid">
                <input type="number" id="bottleInput" placeholder="e.g. 3" min="0">
                <button onclick="calculateImpact()" class="contrast">Calculate Savings</button>
            </div>
            
            <div id="resultArea" style="display:none; margin-top: 1rem; padding: 1rem; background-color: #e6fffa; border: 1px solid #5c7c65; border-radius: 5px;">
                <h4 style="margin-bottom: 0.5rem;">You could save <span id="savedBottles" style="font-weight:bold; font-size: 1.5rem; color: #5c7c65;">0</span> items per year!</h4>
                <p style="margin-bottom: 0;">That's approximately <span id="savedKg" style="font-weight:bold;">0</span> kg of plastic waste kept out of the ocean.</p>
            </div>
        </div>
    </div>
</section>

<!-- --- Inspiration Gallery Section --- -->
<?php if (!empty($gallery_images)): ?>
<section id="inspiration-gallery">
    <h2 style="text-align: center; margin-bottom: 2rem;" data-aos="fade-up">Inspiration Gallery</h2>
    <div class="grid">
        <?php foreach ($gallery_images as $image_url): ?>
            <figure data-aos="zoom-in" data-aos-duration="1000">
                <img src="<?php echo htmlspecialchars($image_url); ?>" alt="Sustainable lifestyle inspiration" style="width:100%; height: 350px; object-fit: cover; border-radius: 0.25rem;">
            </figure>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Script for the Calculator -->
<script>
function calculateImpact() {
    const input = document.getElementById('bottleInput').value;
    const resultArea = document.getElementById('resultArea');
    const savedBottlesSpan = document.getElementById('savedBottles');
    const savedKgSpan = document.getElementById('savedKg');

    if (input && input > 0) {
        // Calculation Logic: 
        // Yearly Savings = Monthly Input * 12
        // Weight Savings = Yearly Savings * 0.05 kg (approx weight of a plastic bottle)
        const yearlySavings = input * 12;
        const kgSavings = (yearlySavings * 0.05).toFixed(1);

        // Update the display
        savedBottlesSpan.innerText = yearlySavings;
        savedKgSpan.innerText = kgSavings;
        
        // Show the result box
        resultArea.style.display = 'block';
    } else {
        alert("Please enter a valid number!");
    }
}
</script>

<?php
require_once ROOT_PATH . '/includes/footer.php';
?>