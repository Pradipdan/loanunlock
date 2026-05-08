@extends('layouts.app')
@section('title', 'Smart Store – Premium Products at ₹299')
@section('content')

@push('styles')
<style>
    .store-shell {
        min-height: 100vh;
        background: #fff;
        padding-bottom: 80px;
    }

    /* Hero Banner */
    .store-hero {
        background: linear-gradient(135deg, #3B5BDB 0%, #6C63FF 50%, #8B5CF6 100%);
        padding: 32px 20px 40px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .store-hero::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 200px; height: 200px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
    }
    .store-hero::after {
        content: '';
        position: absolute;
        bottom: -40px; left: -30px;
        width: 140px; height: 140px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
    }
    .store-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        position: relative;
        z-index: 1;
    }
    .store-logo {
        font-size: 20px;
        font-weight: 800;
        letter-spacing: -0.5px;
    }
    .store-logo span { color: #FEC84B; }
    .store-cart {
        width: 40px; height: 40px;
        background: rgba(255,255,255,0.15);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
        position: relative;
        backdrop-filter: blur(10px);
    }
    .cart-badge {
        position: absolute;
        top: -4px; right: -4px;
        width: 18px; height: 18px;
        background: #F04438;
        border-radius: 50%;
        font-size: 10px;
        font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        color: #fff;
    }
    .hero-content { position: relative; z-index: 1; }
    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255,255,255,0.18);
        backdrop-filter: blur(10px);
        padding: 6px 14px;
        border-radius: 99px;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 14px;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(255,255,255,0.3); }
        50% { box-shadow: 0 0 0 8px rgba(255,255,255,0); }
    }
    .hero-title {
        font-size: 28px;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 8px;
    }
    .hero-subtitle {
        font-size: 14px;
        opacity: 0.85;
        margin-bottom: 20px;
    }
    .hero-price-tag {
        display: inline-flex;
        align-items: baseline;
        gap: 4px;
        background: #FEC84B;
        color: #1a1a2e;
        padding: 8px 20px;
        border-radius: 12px;
        font-weight: 800;
        font-size: 24px;
    }
    .hero-price-tag small {
        font-size: 13px;
        font-weight: 600;
        opacity: 0.7;
    }

    /* Search Bar */
    .store-search {
        padding: 0 20px;
        margin-top: -22px;
        position: relative;
        z-index: 2;
    }
    .search-box {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #fff;
        border: 1.5px solid var(--gray-200);
        border-radius: 14px;
        padding: 12px 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .search-box i { color: var(--gray-400); font-size: 18px; }
    .search-box input {
        flex: 1;
        border: none;
        outline: none;
        font-family: inherit;
        font-size: 14px;
        color: var(--gray-900);
        background: transparent;
    }

    /* Categories */
    .cat-section { padding: 24px 20px 0; }
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }
    .section-title {
        font-size: 18px;
        font-weight: 800;
        color: var(--gray-900);
    }
    .section-link {
        font-size: 13px;
        font-weight: 600;
        color: var(--blue);
        text-decoration: none;
    }
    .cat-scroll {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        padding-bottom: 4px;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    .cat-scroll::-webkit-scrollbar { display: none; }
    .cat-chip {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        min-width: 72px;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .cat-chip:hover { transform: translateY(-2px); }
    .cat-chip.active .cat-icon { border-color: var(--blue); background: var(--blue-light); }
    .cat-icon {
        width: 56px; height: 56px;
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 26px;
        background: var(--gray-50);
        border: 2px solid transparent;
        transition: all 0.2s;
    }
    .cat-name {
        font-size: 11px;
        font-weight: 600;
        color: var(--gray-600);
        text-align: center;
    }

    /* Flash Sale Banner */
    .flash-banner {
        margin: 20px 20px 0;
        background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
        border-radius: 16px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .flash-banner::after {
        content: '⚡';
        position: absolute;
        right: 16px; top: 50%;
        transform: translateY(-50%);
        font-size: 60px;
        opacity: 0.15;
    }
    .flash-info { position: relative; z-index: 1; }
    .flash-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.9;
        margin-bottom: 4px;
    }
    .flash-title {
        font-size: 18px;
        font-weight: 800;
    }
    .flash-timer {
        display: flex;
        gap: 6px;
        position: relative;
        z-index: 1;
    }
    .timer-box {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        border-radius: 8px;
        padding: 6px 8px;
        text-align: center;
        min-width: 38px;
    }
    .timer-num {
        font-size: 18px;
        font-weight: 800;
        display: block;
    }
    .timer-label {
        font-size: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        opacity: 0.8;
    }

    /* Products Grid */
    .products-section { padding: 24px 20px 0; }
    .products-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }
    .product-card {
        background: #fff;
        border: 1.5px solid var(--gray-100);
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.25s;
        position: relative;
    }
    .product-card:hover {
        border-color: var(--blue);
        box-shadow: 0 8px 30px rgba(59,91,219,0.12);
        transform: translateY(-3px);
    }
    .product-badge {
        position: absolute;
        top: 10px; left: 10px;
        background: #F04438;
        color: #fff;
        padding: 3px 10px;
        border-radius: 8px;
        font-size: 10px;
        font-weight: 700;
        z-index: 1;
    }
    .product-wish {
        position: absolute;
        top: 10px; right: 10px;
        width: 32px; height: 32px;
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(6px);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px;
        color: var(--gray-400);
        cursor: pointer;
        border: none;
        z-index: 1;
        transition: all 0.2s;
    }
    .product-wish:hover, .product-wish.active { color: #F04438; }
    .product-img {
        width: 100%;
        aspect-ratio: 1;
        object-fit: cover;
        background: var(--gray-50);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 52px;
    }
    .product-info { padding: 12px 14px 14px; }
    .product-brand {
        font-size: 10px;
        font-weight: 700;
        color: var(--blue);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .product-name {
        font-size: 13px;
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 6px;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .product-rating {
        display: flex;
        align-items: center;
        gap: 4px;
        margin-bottom: 8px;
    }
    .stars { color: #F59E0B; font-size: 11px; }
    .rating-count { font-size: 11px; color: var(--gray-400); }
    .product-price-row {
        display: flex;
        align-items: baseline;
        gap: 6px;
        margin-bottom: 10px;
    }
    .product-price {
        font-size: 18px;
        font-weight: 800;
        color: var(--gray-900);
    }
    .product-mrp {
        font-size: 12px;
        color: var(--gray-400);
        text-decoration: line-through;
    }
    .product-discount {
        font-size: 11px;
        font-weight: 700;
        color: #12B76A;
    }
    .btn-buy {
        width: 100%;
        padding: 9px;
        background: var(--blue);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-family: inherit;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-buy:hover { background: var(--blue-dark); }

    /* Trust Strip */
    .trust-strip {
        display: flex;
        justify-content: space-around;
        padding: 20px;
        margin: 24px 20px 0;
        background: var(--gray-50);
        border-radius: 16px;
    }
    .trust-item-sm {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        font-size: 10px;
        font-weight: 600;
        color: var(--gray-600);
        text-align: center;
    }
    .trust-item-sm i {
        font-size: 20px;
        color: var(--blue);
    }

    /* Bottom Nav */
    .bottom-nav {
        position: fixed;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100%;
        max-width: 430px;
        background: #fff;
        border-top: 1px solid var(--gray-100);
        display: flex;
        justify-content: space-around;
        padding: 8px 0 12px;
        z-index: 100;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.05);
    }
    .nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
        font-size: 10px;
        font-weight: 600;
        color: var(--gray-400);
        text-decoration: none;
        cursor: pointer;
        transition: color 0.2s;
    }
    .nav-item.active { color: var(--blue); }
    .nav-item i { font-size: 20px; }
</style>
@endpush

<div class="store-shell">

    <!-- Hero -->
    <div class="store-hero">
        <div class="store-nav">
            <div class="store-logo">Smart<span>Store</span></div>
            <div class="store-cart">
                <i class="bi bi-bag"></i>
                <div class="cart-badge">0</div>
            </div>
        </div>
        <div class="hero-content">
            <div class="hero-badge">🔥 MEGA SALE LIVE NOW</div>
            <div class="hero-title">Everything at<br>One Price!</div>
            <div class="hero-subtitle">Premium products, unbelievable price. Limited time only.</div>
            <div class="hero-price-tag">₹299 <small>only</small></div>
        </div>
    </div>

    <!-- Search -->
    <div class="store-search">
        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Search products..." id="searchInput">
        </div>
    </div>

    <!-- Categories -->
    <div class="cat-section">
        <div class="section-header">
            <div class="section-title">Categories</div>
            <a href="#" class="section-link">See All</a>
        </div>
        <div class="cat-scroll">
            <div class="cat-chip active" data-cat="all">
                <div class="cat-icon">🛍️</div>
                <div class="cat-name">All</div>
            </div>
            <div class="cat-chip" data-cat="electronics">
                <div class="cat-icon">📱</div>
                <div class="cat-name">Electronics</div>
            </div>
            <div class="cat-chip" data-cat="fashion">
                <div class="cat-icon">👕</div>
                <div class="cat-name">Fashion</div>
            </div>
            <div class="cat-chip" data-cat="beauty">
                <div class="cat-icon">💄</div>
                <div class="cat-name">Beauty</div>
            </div>
            <div class="cat-chip" data-cat="home">
                <div class="cat-icon">🏠</div>
                <div class="cat-name">Home</div>
            </div>
            <div class="cat-chip" data-cat="fitness">
                <div class="cat-icon">💪</div>
                <div class="cat-name">Fitness</div>
            </div>
        </div>
    </div>

    <!-- Flash Sale -->
    <div class="flash-banner">
        <div class="flash-info">
            <div class="flash-label">⚡ Flash Sale</div>
            <div class="flash-title">Ends In</div>
        </div>
        <div class="flash-timer">
            <div class="timer-box">
                <span class="timer-num" id="hrs">02</span>
                <span class="timer-label">Hrs</span>
            </div>
            <div class="timer-box">
                <span class="timer-num" id="mins">45</span>
                <span class="timer-label">Min</span>
            </div>
            <div class="timer-box">
                <span class="timer-num" id="secs">30</span>
                <span class="timer-label">Sec</span>
            </div>
        </div>
    </div>

    <!-- Products -->
    <div class="products-section">
        <div class="section-header">
            <div class="section-title">🔥 Trending Now</div>
            <a href="#" class="section-link">View All</a>
        </div>
        <div class="products-grid" id="productsGrid">
            <!-- Products injected by JS -->
        </div>
    </div>

    <!-- Trust Strip -->
    <div class="trust-strip">
        <div class="trust-item-sm"><i class="bi bi-truck"></i>Free<br>Delivery</div>
        <div class="trust-item-sm"><i class="bi bi-shield-check"></i>Genuine<br>Products</div>
        <div class="trust-item-sm"><i class="bi bi-arrow-counterclockwise"></i>Easy<br>Returns</div>
        <div class="trust-item-sm"><i class="bi bi-credit-card"></i>Secure<br>Payment</div>
    </div>

    <!-- Bottom Nav -->
    <div class="bottom-nav">
        <a class="nav-item active"><i class="bi bi-house-fill"></i>Home</a>
        <a class="nav-item"><i class="bi bi-grid"></i>Categories</a>
        <a class="nav-item" href="{{ route('otp.mobile') }}"><i class="bi bi-person"></i>Account</a>
        <a class="nav-item"><i class="bi bi-bag"></i>Cart</a>
    </div>
</div>

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
const products = [
    { name: "Wireless Bluetooth Earbuds Pro", brand: "SoundMax", cat: "electronics", emoji: "🎧", mrp: 1999, rating: 4.5, reviews: 2341, badge: "BESTSELLER" },
    { name: "Men's Premium Cotton T-Shirt", brand: "UrbanWear", cat: "fashion", emoji: "👕", mrp: 899, rating: 4.3, reviews: 1829, badge: "NEW" },
    { name: "Vitamin C Face Serum 30ml", brand: "GlowSkin", cat: "beauty", emoji: "✨", mrp: 1499, rating: 4.7, reviews: 3120, badge: "TOP RATED" },
    { name: "Smart LED Desk Lamp Touch", brand: "LumiTech", cat: "home", emoji: "💡", mrp: 1299, rating: 4.4, reviews: 982, badge: "" },
    { name: "Resistance Bands Set of 5", brand: "FitPro", cat: "fitness", emoji: "🏋️", mrp: 999, rating: 4.6, reviews: 1543, badge: "HOT" },
    { name: "USB-C Fast Charging Cable 2m", brand: "ChargePlus", cat: "electronics", emoji: "🔌", mrp: 699, rating: 4.2, reviews: 4502, badge: "" },
    { name: "Women's Oversized Hoodie", brand: "CozyFit", cat: "fashion", emoji: "🧥", mrp: 1599, rating: 4.5, reviews: 876, badge: "TRENDING" },
    { name: "Natural Lip Balm Set (3 Pack)", brand: "PureGlow", cat: "beauty", emoji: "💋", mrp: 599, rating: 4.8, reviews: 2190, badge: "BESTSELLER" },
    { name: "Portable Bluetooth Speaker", brand: "BassBox", cat: "electronics", emoji: "🔊", mrp: 2499, rating: 4.4, reviews: 1675, badge: "88% OFF" },
    { name: "Yoga Mat Anti-Slip 6mm", brand: "ZenFit", cat: "fitness", emoji: "🧘", mrp: 1199, rating: 4.6, reviews: 2034, badge: "" },
    { name: "Stainless Steel Water Bottle 1L", brand: "HydroLife", cat: "home", emoji: "💧", mrp: 799, rating: 4.3, reviews: 3456, badge: "ECO" },
    { name: "Anti-Blue Light Glasses", brand: "ClearVue", cat: "fashion", emoji: "👓", mrp: 1299, rating: 4.1, reviews: 1267, badge: "NEW" },
];

const grid = document.getElementById('productsGrid');

function renderProducts(filter = 'all', search = '') {
    const filtered = products.filter(p => {
        const matchCat = filter === 'all' || p.cat === filter;
        const matchSearch = !search || p.name.toLowerCase().includes(search.toLowerCase()) || p.brand.toLowerCase().includes(search.toLowerCase());
        return matchCat && matchSearch;
    });

    grid.innerHTML = filtered.map(p => {
        const discount = Math.round((1 - 299 / p.mrp) * 100);
        const stars = '★'.repeat(Math.floor(p.rating)) + (p.rating % 1 >= 0.5 ? '½' : '');
        const safeName = p.name.replace(/'/g, "\\'");
        return `
        <div class="product-card" data-cat="${p.cat}">
            ${p.badge ? `<div class="product-badge">${p.badge}</div>` : ''}
            <button class="product-wish" onclick="this.classList.toggle('active')"><i class="bi bi-heart"></i></button>
            <div class="product-img">${p.emoji}</div>
            <div class="product-info">
                <div class="product-brand">${p.brand}</div>
                <div class="product-name">${p.name}</div>
                <div class="product-rating">
                    <span class="stars">${stars}</span>
                    <span class="rating-count">(${p.reviews.toLocaleString()})</span>
                </div>
                <div class="product-price-row">
                    <span class="product-price">₹299</span>
                    <span class="product-mrp">₹${p.mrp.toLocaleString()}</span>
                    <span class="product-discount">${discount}% off</span>
                </div>
                <button class="btn-buy" onclick="buyNow('${safeName}', this)">Buy Now</button>
            </div>
        </div>`;
    }).join('');
}

renderProducts();

// Category filter
document.querySelectorAll('.cat-chip').forEach(chip => {
    chip.addEventListener('click', () => {
        document.querySelectorAll('.cat-chip').forEach(c => c.classList.remove('active'));
        chip.classList.add('active');
        renderProducts(chip.dataset.cat, document.getElementById('searchInput').value);
    });
});

// Search
document.getElementById('searchInput').addEventListener('input', function() {
    const activeCat = document.querySelector('.cat-chip.active')?.dataset.cat || 'all';
    renderProducts(activeCat, this.value);
});

// Buy Now → Create Razorpay order and open payment popup
function buyNow(productName, btn) {
    const originalText = btn.textContent;
    btn.textContent = 'Processing...';
    btn.disabled = true;

    fetch("{{ route('store.create.order') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ product_name: productName })
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
            resetBtn(btn, originalText);
            return;
        }

        const options = {
            key: data.key,
            amount: data.amount,
            currency: "INR",
            name: "SmartStore",
            description: productName,
            order_id: data.order_id,
            handler: function(response) {
                btn.textContent = 'Verifying...';
                fetch("{{ route('store.verify.payment') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        razorpay_order_id: response.razorpay_order_id,
                        razorpay_payment_id: response.razorpay_payment_id,
                        razorpay_signature: response.razorpay_signature,
                        product_name: productName
                    })
                })
                .then(r => r.json())
                .then(v => {
                    if (v.status === 'success') {
                        btn.textContent = '✅ Paid!';
                        btn.style.background = '#12B76A';
                        alert('🎉 Payment successful! Order confirmed for: ' + productName);
                    } else {
                        alert('Verification failed. Please contact support.');
                        resetBtn(btn, originalText);
                    }
                });
            },
            theme: { color: "#3B5BDB" },
            modal: {
                ondismiss: function() {
                    resetBtn(btn, originalText);
                }
            }
        };

        const rzp = new Razorpay(options);
        rzp.open();
        btn.textContent = 'Pay ₹299';
    })
    .catch(err => {
        console.error(err);
        alert('Something went wrong. Please try again.');
        resetBtn(btn, originalText);
    });
}

function resetBtn(btn, text) {
    btn.textContent = text;
    btn.disabled = false;
}

// Countdown timer
let totalSecs = 2 * 3600 + 45 * 60 + 30;
setInterval(() => {
    if (totalSecs <= 0) return;
    totalSecs--;
    document.getElementById('hrs').textContent = String(Math.floor(totalSecs / 3600)).padStart(2, '0');
    document.getElementById('mins').textContent = String(Math.floor((totalSecs % 3600) / 60)).padStart(2, '0');
    document.getElementById('secs').textContent = String(totalSecs % 60).padStart(2, '0');
}, 1000);
</script>
@endpush
@endsection
