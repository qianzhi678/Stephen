<?php
// shop.php - The Eco Shop with Quick View Modal
require_once __DIR__ . '/config/database.php';
require_once ROOT_PATH . '/includes/functions.php';
require_once ROOT_PATH . '/includes/header.php';

// Fetch all products from JSON
$products = get_all_products_from_json();
?>

<!-- Shop Hero -->
<section style="text-align: center; padding: 4rem 0; background-color: var(--bg-mint);">
    <h1>The Eco Shop</h1>
    <p>Browse our collection of 20 sustainable essentials.</p>
</section>

<!-- Shop Grid -->
<section class="container" style="margin-top: 3rem;">
    <?php if (empty($products)): ?>
        <p style="text-align: center;">No products available.</p>
    <?php else: ?>
        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem;">
            <?php foreach ($products as $product): ?>
                <?php if (isset($product['price']) && $product['price'] > 0): ?>
                
                <article class="shop-item" style="position: relative; display: flex; flex-direction: column; height: 100%; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                    
                    <!-- Image Container with Overlay Button -->
                    <figure style="margin: 0; overflow: hidden; border-radius: var(--border-radius); position: relative; group">
                        
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                             style="width: 100%; height: 280px; object-fit: cover;">
                        
                        <!-- THE "TRANSPARENT" BUTTON (Overlay) -->
                        <!-- This covers the image and triggers the modal on click -->
                        <div class="quick-view-overlay"
                             onclick="openModal(this)"
                             data-id="<?php echo $product['id']; ?>"
                             data-name="<?php echo htmlspecialchars($product['name']); ?>"
                             data-desc="<?php echo htmlspecialchars($product['description']); ?>"
                             data-extra="<?php echo htmlspecialchars($product['extra_details'] ?? 'No extra details available.'); ?>"
                             data-price="<?php echo number_format($product['price'], 2); ?>"
                             data-image="<?php echo htmlspecialchars($product['image_url']); ?>">
                             <span>Quick View</span>
                        </div>

                    </figure>

                    <!-- Product Info -->
                    <div style="padding: 1.5rem 0.5rem 0.5rem; flex-grow: 1;">
                        <h5 style="margin-bottom: 0.5rem; font-size: 1.1rem;"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p style="font-size: 0.9rem; color: var(--text-light); margin-bottom: 1rem;">
                            <?php echo htmlspecialchars($product['description']); ?>
                        </p>
                    </div>

                    <!-- Price & Add to Cart -->
                    <div style="margin-top: auto; padding-top: 1rem; border-top: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 1.25rem; font-weight: bold; color: var(--primary);">
                            $<?php echo number_format($product['price'], 2); ?>
                        </span>
                        
                        <form action="<?php echo BASE_URL; ?>/cart-action.php" method="post" style="margin:0;">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" style="padding: 8px 20px; font-size: 0.9rem; border-radius: 50px;">Add</button>
                        </form>
                    </div>

                </article>

                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- --- MODAL STRUCTURE (Hidden by default) --- -->
<!-- --- MODAL STRUCTURE (Hidden by default) --- -->
<dialog id="product_modal">
    <article style="width: 100%; max-width: 800px; padding: 0;">
        <header style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 2rem;">
            <h5 id="modal_title" style="margin: 0;">Product Name</h5>
            <!-- Top Right 'X' Close Link -->
            <a href="#close" aria-label="Close" class="close" onclick="closeModal()"></a>
        </header>
        
        <div class="grid" style="gap: 0;">
            <!-- Modal Image -->
            <div style="width: 100%; height: 300px; background-color: #f0f0f0;">
                <img id="modal_image" src="" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            
            <!-- Modal Content -->
            <div style="padding: 2rem;">
                <h4 id="modal_price" style="color: var(--primary); margin-bottom: 1rem;">$0.00</h4>
                <p id="modal_desc" style="font-size: 1.1rem;">Description goes here.</p>
                
                <hr>
                
                <p><strong>More Details:</strong></p>
                <p id="modal_extra" style="color: var(--text-light); font-size: 0.95rem;">Extra details...</p>
                
                <!-- Add to Cart Form -->
                <form action="<?php echo BASE_URL; ?>/cart-action.php" method="post" style="margin-top: 2rem; margin-bottom: 1rem;">
                    <input type="hidden" id="modal_product_id" name="product_id" value="">
                    <button type="submit" style="width: 100%;">Add to Cart</button>
                </form>

                <!-- NEW: Explicit Close Button -->
                <button type="button" class="secondary outline" onclick="closeModal()" style="width: 100%;">Close Window</button>
            </div>
        </div>
    </article>
</dialog>

<!-- --- CSS FOR HOVER EFFECT --- -->
<style>
    /* Style for the transparent overlay button */
    .quick-view-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(44, 94, 67, 0.7); /* Green tint */
        opacity: 0; /* Transparent by default */
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        transition: opacity 0.3s ease;
    }
    
    /* Show on hover */
    .shop-item figure:hover .quick-view-overlay {
        opacity: 1;
    }
    
    .quick-view-overlay span {
        color: white;
        font-weight: bold;
        border: 2px solid white;
        padding: 10px 20px;
        border-radius: 50px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Modal Tweaks */
    dialog article {
        overflow: hidden; /* For image corners */
    }
</style>

<!-- --- JS FOR MODAL LOGIC --- -->
<script>
    const modal = document.getElementById('product_modal');
    const html = document.querySelector('html');

    function openModal(element) {
        // 1. Get data from the clicked element's data attributes
        const id = element.getAttribute('data-id');
        const name = element.getAttribute('data-name');
        const desc = element.getAttribute('data-desc');
        const extra = element.getAttribute('data-extra');
        const price = element.getAttribute('data-price');
        const image = element.getAttribute('data-image');

        // 2. Populate the modal elements
        document.getElementById('modal_title').innerText = name;
        document.getElementById('modal_desc').innerText = desc;
        document.getElementById('modal_extra').innerText = extra; // Show extra content
        document.getElementById('modal_price').innerText = '$' + price;
        document.getElementById('modal_image').src = image;
        document.getElementById('modal_product_id').value = id;

        // 3. Show the modal
        modal.setAttribute('open', '');
        html.classList.add('modal-is-open');
    }

    function closeModal() {
        modal.removeAttribute('open');
        html.classList.remove('modal-is-open');
    }

    // Close modal if clicked outside
    document.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });
</script>

<?php
require_once ROOT_PATH . '/includes/footer.php';
?>