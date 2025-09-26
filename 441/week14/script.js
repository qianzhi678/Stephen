/* --- START OF FINAL, VERIFIED SCRIPT (WITH CART FIX) --- */
// This script handles all client-side functionality for the RTO training website.
// Student ID Suffix for all functions: 123

// Global variables to store data
let courses = [];
let resources = [];
let cart = [];
let currentUser = null;
let tasks = [];

// 1. Core Initializer: Runs when the page is fully loaded.
document.addEventListener('DOMContentLoaded', function() {
    initializeStorage123();
    setupEventListeners123();
    checkLoginStatus123();
    loadPageSpecificContent123();
    updateNavigation123();
});

// ===================================
// INITIALIZATION & STORAGE
// ===================================

function initializeStorage123() {
    // 2. Local Storage Check: Ensures necessary data structures exist in localStorage.
    if (!localStorage.getItem('users')) localStorage.setItem('users', JSON.stringify({}));
    if (!localStorage.getItem('cart')) localStorage.setItem('cart', JSON.stringify([]));
    if (!localStorage.getItem('orders')) localStorage.setItem('orders', JSON.stringify([]));
    if (!localStorage.getItem('tasks')) localStorage.setItem('tasks', JSON.stringify([]));
}

// ======================
// EVENT HANDLERS
// ======================

function setupEventListeners123() {
    const registerForm = document.getElementById('registerForm');
    if (registerForm) registerForm.addEventListener('submit', handleRegistration123);

    const loginForm = document.getElementById('loginForm');
    if (loginForm) loginForm.addEventListener('submit', handleLogin123);

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-to-cart')) addToCart123(e);
        if (e.target.classList.contains('remove-item')) removeFromCart123(e);
    });

    const downloadJsonBtn = document.getElementById('download-json');
    if (downloadJsonBtn) downloadJsonBtn.addEventListener('click', downloadOrderAsJson123);
    
    const clearDataBtn = document.getElementById('clear-data');
    if (clearDataBtn) clearDataBtn.addEventListener('click', clearAllData123);

    const logoutNowBtn = document.getElementById('logout-now');
    if (logoutNowBtn) logoutNowBtn.addEventListener('click', logoutUser123);

    const addTaskBtn = document.getElementById('add-task-btn');
    if (addTaskBtn) addTaskBtn.addEventListener('click', addTask123);

    const taskList = document.getElementById('task-list');
    if (taskList) {
        taskList.addEventListener('click', (e) => {
            if (e.target.classList.contains('delete-task-btn')) {
                const taskText = e.target.parentElement.querySelector('span').textContent;
                deleteTask123(taskText);
            } else if (e.target.closest('li')) {
                const taskText = e.target.closest('li').querySelector('span').textContent;
                toggleTaskComplete123(taskText);
            }
        });
    }
}


// ======================
// AUTHENTICATION (CORRECTED LOGIC)
// ======================

function handleRegistration123(event) {
    event.preventDefault();
    const username = document.getElementById('regUsername').value.trim();
    const email = document.getElementById('regEmail').value.trim();
    const phone = document.getElementById('regPhone').value.trim();
    const password = document.getElementById('regPassword').value;
    const confirmPassword = document.getElementById('regConfirmPassword').value;

    if (password !== confirmPassword) {
        showMessage123('Passwords do not match.', 'error');
        return;
    }
    
    const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/;
    if (!passwordRegex.test(password)) {
        showMessage123('Password must be at least 6 characters and contain one letter and one number.', 'error');
        return;
    }

    const users = JSON.parse(localStorage.getItem('users'));
    if (users[username]) {
        showMessage123('Username already exists.', 'error');
    } else {
        users[username] = { username, password, email, phone }; 
        localStorage.setItem('users', JSON.stringify(users));
        showMessage123('Registration successful! Please log in.', 'success');
        event.target.reset();
    }
}

function handleLogin123(event) {
    event.preventDefault();
    const username = document.getElementById('loginUsername').value.trim();
    const password = document.getElementById('loginPassword').value;

    const users = JSON.parse(localStorage.getItem('users'));
    const user = users[username]; 

    if (user && user.password === password) {
        sessionStorage.setItem('currentUser', JSON.stringify(user));
        showMessage123('Login successful! Redirecting...', 'success');
        setTimeout(() => { window.location.href = 'browse.html'; }, 1500);
    } else {
        showMessage123('Invalid username or password.', 'error');
    }
}

function checkLoginStatus123() {
    const userJson = sessionStorage.getItem('currentUser');
    if (userJson) {
        currentUser = JSON.parse(userJson);
        updateUserGreeting123();
    }
}

function updateUserGreeting123() {
    const greetingElement = document.getElementById('user-greeting');
    if (greetingElement && currentUser) {
        greetingElement.textContent = `Welcome, ${currentUser.username}`;
    }
}

function logoutUser123() {
    sessionStorage.removeItem('currentUser');
    currentUser = null;
    showMessage123('You have been logged out.', 'success');
    setTimeout(() => { window.location.href = 'index.html'; }, 1500);
}


// ======================
// DATA LOADING & DISPLAY
// ======================

async function loadRtoData123() {
    try {
        const [coursesRes, resourcesRes] = await Promise.all([
            fetch('courses.json'),
            fetch('resources.json')
        ]);
        courses = await coursesRes.json();
        resources = await resourcesRes.json();
        
        if (document.getElementById('course-list')) displayCourses123();
        if (document.getElementById('product-list')) displayResourceKits123();
    } catch (error) {
        console.error('Failed to load RTO data:', error);
        showMessage123('Could not load training data. Please try again later.', 'error');
    }
}

function displayCourses123() {
    const courseList = document.getElementById('course-list');
    if (!courseList) return;
    courseList.innerHTML = '';
    courses.forEach(course => {
        const courseCard = document.createElement('div');
        courseCard.className = 'product-card';
        courseCard.innerHTML = `
            <div style="padding: 1.5rem;">
                <h3>${course.courseName} (${course.courseCode})</h3>
                <p class="description">${course.description}</p>
                <p><strong>Assessments:</strong> ${course.assessmentRequirements}</p>
            </div>
        `;
        courseList.appendChild(courseCard);
    });
}

function displayResourceKits123() {
    const productList = document.getElementById('product-list');
    if (!productList) return;
    productList.innerHTML = '';
    resources.forEach(resource => {
        const productCard = document.createElement('div');
        productCard.className = 'product-card';
        productCard.innerHTML = `
            <img src="${resource.image}" alt="${resource.name}">
            <div style="padding: 1.5rem; flex-grow: 1; display: flex; flex-direction: column;">
                <h3>${resource.name}</h3>
                <p class="description">${resource.description}</p>
                <p class="price" style="margin-top: auto;">$${resource.price.toFixed(2)}</p>
                <button class="add-to-cart btn" data-id="${resource.id}">Add to Cart</button>
            </div>
        `;
        productList.appendChild(productCard);
    });
}


// ======================
// SHOPPING CART (WITH FIX)
// ======================

function loadCart123() {
    cart = JSON.parse(localStorage.getItem('cart')) || [];
    displayCart123();
}

function saveCart123() {
    localStorage.setItem('cart', JSON.stringify(cart));
}

// THIS IS THE CORRECTED FUNCTION
function addToCart123(event) {
    if (!currentUser) {
        showMessage123('Please log in to add items to your cart.', 'error');
        return;
    }

    // 1. ALWAYS read the most up-to-date cart from localStorage at the start.
    let currentCart = JSON.parse(localStorage.getItem('cart')) || [];

    const resourceId = event.target.dataset.id;
    const resource = resources.find(r => r.id === resourceId);
    
    if (resource) {
        // 2. Perform all logic on this fresh `currentCart` variable.
        const existingItem = currentCart.find(item => item.id === resourceId);
        if (existingItem) {
            existingItem.quantity++; // If item exists, increase quantity
        } else {
            currentCart.push({ ...resource, quantity: 1 }); // If new, add it to the array
        }
        
        // 3. Save the MODIFIED cart back to localStorage.
        localStorage.setItem('cart', JSON.stringify(currentCart));
        
        // 4. Also update the global variable so the rest of the script is in sync.
        cart = currentCart;

        showMessage123(`Added '${resource.name}' to cart.`, 'success');

        // This line is for instantly refreshing the cart page if you add an item while viewing the cart.
        if(window.location.pathname.includes('shopping_cart.html')) {
            displayCart123();
        }
    }
}

function removeFromCart123(event) {
    const resourceId = event.target.dataset.id;
    cart = cart.filter(item => item.id !== resourceId);
    saveCart123();
    displayCart123();
    showMessage123('Item removed from cart.', 'success');
}

function displayCart123() {
    const cartItemsEl = document.getElementById('cart-items');
    if (!cartItemsEl) return;
    cartItemsEl.innerHTML = '';

    if (cart.length === 0) {
        cartItemsEl.innerHTML = '<p class="empty-cart">Your shopping cart is empty.</p>';
        return;
    }

    let total = 0;
    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        const cartItemDiv = document.createElement('div');
        cartItemDiv.className = 'cart-item';
        cartItemDiv.innerHTML = `
            <div class="cart-item-info"><h3>${item.name} (Qty: ${item.quantity})</h3></div>
            <div class="cart-item-price">$${itemTotal.toFixed(2)}</div>
            <button class="remove-item btn danger" data-id="${item.id}">Remove</button>
        `;
        cartItemsEl.appendChild(cartItemDiv);
    });

    const totalEl = document.createElement('div');
    totalEl.className = 'cart-total';
    totalEl.innerHTML = `<strong>Total: $${total.toFixed(2)}</strong>`;
    cartItemsEl.appendChild(totalEl);

    const checkoutBtn = document.createElement('a');
    checkoutBtn.className = 'checkout-btn btn';
    checkoutBtn.textContent = 'Proceed to Checkout';
    checkoutBtn.href = 'order_confirmation.html';
    cartItemsEl.appendChild(checkoutBtn);
}


// ======================
// ORDER MANAGEMENT
// ======================

function displayOrderSummary123() {
    const orderItemsList = document.getElementById('order-items-list');
    const orderTotalDisplay = document.getElementById('order-total-display');
    const cartData = JSON.parse(localStorage.getItem('cart')) || [];

    if (!orderItemsList) return;
    orderItemsList.innerHTML = '';
    let total = 0;
    cartData.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        const listItem = document.createElement('li');
        listItem.className = 'order-item';
        listItem.innerHTML = `<span>${item.name} (Qty: ${item.quantity})</span><span>$${itemTotal.toFixed(2)}</span>`;
        orderItemsList.appendChild(listItem);
    });
    orderTotalDisplay.textContent = `Total: $${total.toFixed(2)}`;

    const confirmBtn = document.getElementById('confirm-order');
    if (confirmBtn) {
        confirmBtn.onclick = () => {
            const orders = JSON.parse(localStorage.getItem('orders')) || [];
            orders.push({ user: currentUser.username, date: new Date().toISOString(), items: cartData, total: total });
            localStorage.setItem('orders', JSON.stringify(orders));
            localStorage.removeItem('cart');
            
            showMessage123('Order confirmed! Redirecting...', 'success');
            setTimeout(() => { window.location.href = 'order_management.html'; }, 2000);
        };
    }
}

function downloadOrderAsJson123() {
    if (!currentUser) {
        showMessage123('You must be logged in to download your order history.', 'error');
        return;
    }
    const allOrders = JSON.parse(localStorage.getItem('orders')) || [];
    const userOrders = allOrders.filter(order => order.user === currentUser.username);

    if (userOrders.length === 0) {
        showMessage123('No order history found for your account.', 'error');
        return;
    }

    const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(userOrders, null, 2));
    const downloadAnchor = document.createElement('a');
    downloadAnchor.setAttribute("href", dataStr);
    downloadAnchor.setAttribute("download", `order_history_${currentUser.username}.json`);
    document.body.appendChild(downloadAnchor);
    downloadAnchor.click();
    downloadAnchor.remove();
    showMessage123('Your complete order history has been downloaded.', 'success');
}

function clearAllData123() {
    if (confirm('Are you sure you want to clear your cart and order history?')) {
        localStorage.removeItem('cart');
        localStorage.removeItem('orders');
        showMessage123('Cart and order history cleared.', 'success');
        setTimeout(() => { location.reload(); }, 1500);
    }
}


// ======================
// INTERACTIVE FEATURE: TO-DO LIST
// ======================

function loadTasks123() {
    tasks = JSON.parse(localStorage.getItem('tasks')) || [];
    const taskList = document.getElementById('task-list');
    if (!taskList) return;

    taskList.innerHTML = '';
    tasks.forEach(task => {
        const li = document.createElement('li');
        li.style.cssText = 'display: flex; justify-content: space-between; align-items: center; padding: 10px; border-bottom: 1px solid #eee; cursor: pointer;';
        if (task.completed) {
            li.style.textDecoration = 'line-through';
            li.style.opacity = '0.6';
        }
        li.innerHTML = `<span>${task.text}</span><button class="delete-task-btn btn danger" style="padding: 5px 10px;">Delete</button>`;
        taskList.appendChild(li);
    });
}

function addTask123() {
    const taskInput = document.getElementById('task-input');
    const taskText = taskInput.value.trim();
    if (taskText === '') return;
    tasks.push({ text: taskText, completed: false });
    localStorage.setItem('tasks', JSON.stringify(tasks));
    loadTasks123();
    taskInput.value = '';
}

function deleteTask123(taskText) {
    tasks = tasks.filter(task => task.text !== taskText);
    localStorage.setItem('tasks', JSON.stringify(tasks));
    loadTasks123();
}

function toggleTaskComplete123(taskText) {
    const task = tasks.find(t => t.text === taskText);
    if (task) task.completed = !task.completed;
    localStorage.setItem('tasks', JSON.stringify(tasks));
    loadTasks123();
}


// ======================
// UTILITY & PAGE ROUTING
// ======================

function showMessage123(message, type) {
    const existing = document.querySelector('.message');
    if (existing) existing.remove();
    const msgDiv = document.createElement('div');
    msgDiv.className = `message ${type}`;
    msgDiv.textContent = message;
    document.body.appendChild(msgDiv);
    setTimeout(() => { msgDiv.remove(); }, 3000);
}

function updateNavigation123() {
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
        }
    });
}

function loadPageSpecificContent123() {
    const path = window.location.pathname.split('/').pop();

    if (['', 'index.html', 'browse.html'].includes(path)) {
        loadRtoData123();
    } else if (path === 'shopping_cart.html') {
        if (!currentUser) {
            showMessage123('Please log in to view your cart.', 'error');
            setTimeout(() => { window.location.href = 'login.html'; }, 1500);
        } else {
            loadCart123();
        }
    } else if (path === 'order_confirmation.html') {
        if (!currentUser) {
            setTimeout(() => { window.location.href = 'login.html'; }, 50);
        } else {
            displayOrderSummary123();
        }
    } else if (path === 'order_management.html') {
        if (!currentUser) {
            setTimeout(() => { window.location.href = 'login.html'; }, 50);
        } else {
            loadTasks123();
        }
    }
}
/* --- END OF FINAL, VERIFIED SCRIPT (WITH CART FIX) --- */