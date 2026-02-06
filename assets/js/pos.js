
/*POS SYSTEM JS*/

const searchInput = document.querySelector(".pos-search-input");
const searchResults = document.querySelector(".pos-search-results");
const cartItemsContainer = document.querySelector(".pos-cart-items");
const totalDisplay = document.querySelector(".pos-cart .pos-total");

let cart = [];

// ------------- SEARCH -------------
searchInput.addEventListener("input", async () => {
  const query = searchInput.value.trim();
  searchResults.innerHTML = "";

  if (!query) {
    searchResults.style.display = "none"; // hide when empty
    return;
  }

  try {
    const res = await fetch(`search.php?q=${encodeURIComponent(query)}`);
    const data = await res.json();

    if (data.length === 0) {
      searchResults.style.display = "none"; // hide if no results
      return;
    }

    searchResults.style.display = "block"; // show results
    data.forEach((item) => {
      const li = document.createElement("li");
      li.textContent = `${item.artikelbezeichnung} - ${item.preis.toFixed(2)} €`;
      li.dataset.id = item.id_artikel;
      li.dataset.name = item.artikelbezeichnung;
      li.dataset.price = item.preis;
      li.addEventListener("click", () => {
        addToCart(item);
        searchInput.value = "";
        searchResults.style.display = "none";
      });
      searchResults.appendChild(li);
    });
  } catch (err) {
    console.error("Search fetch error:", err);
    searchResults.style.display = "none";
  }
});

// ------------- HIDE SEARCH ON OUTSIDE CLICK -------------
document.addEventListener("click", (e) => {
  if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
    searchResults.style.display = "none";
  }
});

// ------------- ADD TO CART -------------
function addToCart(item) {
  const existing = cart.find((i) => i.id === item.id_artikel);
  if (existing) {
    existing.quantity += 1;
  } else {
    cart.push({
      id: item.id_artikel,
      name: item.artikelbezeichnung,
      price: parseFloat(item.preis),
      quantity: 1,
    });
  }
  renderCart();
}

// ------------- REMOVE FROM CART -------------
function removeFromCart(id) {
  cart = cart.filter((i) => i.id !== id);
  renderCart();
}

// ------------- RENDER CART -------------
function renderCart() {
  cartItemsContainer.innerHTML = "";
  let total = 0;

  cart.forEach((item) => {
    const div = document.createElement("div");
    div.className = "pos-item";
    div.innerHTML = `
            <span>${item.name} x${item.quantity}</span>
            <span>${(item.price * item.quantity).toFixed(2)} € 
                <button class="remove-item-btn" onclick="removeFromCart(${item.id})">✕</button>
            </span>
        `;
    cartItemsContainer.appendChild(div);
    total += item.price * item.quantity;
  });

  // Update cart panel total
  totalDisplay.textContent = total.toFixed(2) + " €";

  // Update checkout total
  const checkoutTotal = document.querySelector(".pos-checkout .pos-total");
  if (checkoutTotal) checkoutTotal.textContent = total.toFixed(2) + " €";
}

// ------------- PAYMENT PANEL -------------
function openPayment(type) {
  const panel = document.getElementById("paymentPanel");
  const barOptions = document.getElementById("barOptions");
  const title = document.getElementById("paymentTitle");

  if (type === "bar") {
    barOptions.style.display = "block";
    title.textContent = "Barzahlung";
  } else {
    barOptions.style.display = "none";
    title.textContent = "Kartenzahlung";
  }

  panel.classList.add("active");
}

function closePayment() {
  document.getElementById("paymentPanel").classList.remove("active");
}

function pay(option) {
  if (cart.length === 0) {
    alert("Warenkorb ist leer!");
    return;
  }
  alert(
    "Bezahlung abgeschlossen: " +
      option +
      "\nGesamt: " +
      totalDisplay.textContent,
  );
  cart = [];
  renderCart();
  closePayment();
}

// ------------- NUMPAD -------------
function addNumber(num) {
  const input = document.getElementById("cashAmount");
  input.value += num;
}

function clearCash() {
  document.getElementById("cashAmount").value = "";
}

function removeLast() {
  const input = document.getElementById("cashAmount");
  input.value = input.value.slice(0, -1);
}
