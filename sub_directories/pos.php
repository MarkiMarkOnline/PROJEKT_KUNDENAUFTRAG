<?php
require_once __DIR__ . '/../config.php';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>

<main id="pos">
    <!-- Searchbar -->
    <div class="pos-searchbar">
        <input type="text"
               class="pos-search-input"
               placeholder="Barcode scannen oder Artikel suchen..."
               autofocus>
    </div>

        <!-- Warenkorb -->
    <div class="grid-pos">
        <section class="pos-module pos-cart">
            <h2>Warenkorb</h2>

            <div class="pos-cart-items">
                <div class="pos-item">
                    <span>Apfel</span>
                    <span>2,50 €</span>
                </div>
                      <div class="pos-item">
                    <span>Kartoffeln</span>
                    <span>2,50 €</span>
                </div>
            </div>
        </section>

            <!-- Checkout -->
        <section class="pos-module pos-checkout">
            <div class="pos-total">5,00 €</div>

            <div class="pos-payments">
                <button class="pos-kasse-btn" onclick="openPayment('karte')">Karte</button>
                <button class="pos-kasse-btn" onclick="openPayment('bar')">Bar</button>
            </div>
        </section>
    </div>
</main>

    <!-- Payment panel -->
<div class="pos-payment-panel" id="paymentPanel">
    <div class="pos-payment-box">
        <h3 id="paymentTitle">Zahlung</h3>

        <div id="barOptions" style="display:none;">
    <label>Betrag erhalten:</label>
    <input type="text" id="cashAmount" placeholder="0,00 €">

    <div class="pos-numpad">
        <button onclick="addNumber('7')">7</button>
        <button onclick="addNumber('8')">8</button>
        <button onclick="addNumber('9')">9</button>

        <button onclick="addNumber('4')">4</button>
        <button onclick="addNumber('5')">5</button>
        <button onclick="addNumber('6')">6</button>

        <button onclick="addNumber('1')">1</button>
        <button onclick="addNumber('2')">2</button>
        <button onclick="addNumber('3')">3</button>

        <button onclick="clearCash()">C</button>
        <button onclick="addNumber('0')">0</button>
        <button onclick="removeLast()">←</button>
    </div>
</div>
        <div class="payment-buttons">
            <button onclick="pay('mitBon')">Mit Bon</button>
            <button onclick="pay('ohneBon')">Ohne Bon</button>
            <button onclick="closePayment()">Abbrechen</button>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script src="../assets/js/pos.js"></script>
