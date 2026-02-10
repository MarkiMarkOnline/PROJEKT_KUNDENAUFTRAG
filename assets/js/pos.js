//  POS SYSTEM

//  DOM CACHE
const DOM = {
  searchInput: document.querySelector(".pos-search-input"),
  searchResults: document.querySelector(".pos-search-results"),
  cartItems: document.querySelector(".pos-cart-items"),
  cartTotal: document.getElementById("cartTotal"),
  checkoutTotal: document.getElementById("checkoutTotal"),
  paymentPanel: document.getElementById("paymentPanel"),
  paymentTitle: document.getElementById("paymentTitle"),
  barOptions: document.getElementById("barOptions"),
  cashInput: document.getElementById("cashAmount"),
};

//   STATE
const cart = new Map(); // store cart items keyed by product ID

// HELPER

function getCartTotal() {
  // calculate total cart price
  let total = 0;
  cart.forEach((item) => {
    total += item.price * item.quantity; // sum subtotals
  });
  return total;
}

function formatMoney(value) {
  // format number as currency
  return value.toFixed(2) + " €";
}

// SEARCH

function debounce(fn, delay = 250) {
  let timer;
  return (...args) => {
    clearTimeout(timer);
    timer = setTimeout(() => fn(...args), delay);
  };
}

DOM.searchInput.addEventListener("input", debounce(handleSearch)); // attach search handler

async function handleSearch() {
  const query = DOM.searchInput.value.trim();
  if (!query) {
    DOM.searchResults.style.display = "none"; // hide if empty
    return;
  }

  try {
    const res = await fetch(`search.php?q=${encodeURIComponent(query)}`); // fetch from server
    const data = await res.json();
    renderSearchResults(data); // render results
  } catch (err) {
    console.error("Search error:", err); // log errors
  }
}

function renderSearchResults(items) {
  DOM.searchResults.innerHTML = ""; // clear old results

  if (!items.length) {
    DOM.searchResults.style.display = "none"; // hide if no matches
    return;
  }

  DOM.searchResults.style.display = "block"; // show dropdown

  items.forEach((item) => {
    const li = document.createElement("li");
    li.textContent = `${item.artikelbezeichnung} - ${Number(item.preis).toFixed(2)} €`;
    li.addEventListener("click", () => {
      addToCart(item); // add clicked product to cart
      DOM.searchInput.value = ""; // clear input
      DOM.searchResults.style.display = "none"; // hide dropdown
      DOM.searchInput.focus(); // keep focus
    });
    DOM.searchResults.appendChild(li); // add to DOM
  });
}

// hide dropdown on outside click
document.addEventListener("click", (e) => {
  if (
    !DOM.searchInput.contains(e.target) &&
    !DOM.searchResults.contains(e.target)
  ) {
    DOM.searchResults.style.display = "none";
  }
});

// CART

function addToCart(item) {
  const id = item.id_artikel;
  if (cart.has(id)) {
    cart.get(id).quantity++; // increment if exists
  } else {
    cart.set(id, {
      id,
      name: item.artikelbezeichnung,
      price: Number(item.preis),
      quantity: 1, // new item
    });
  }
  renderCart(); // refresh cart display
}

function increaseQty(id) {
  // + button
  const item = cart.get(id);
  item.quantity++;
  renderCart();
}

function decreaseQty(id) {
  // - button
  const item = cart.get(id);
  if (item.quantity > 1) {
    item.quantity--;
  } else {
    cart.delete(id); // remove if 0
  }
  renderCart();
}

function removeFromCart(id) {
  // remove ✕ button
  cart.delete(id);
  renderCart();
}

function renderCart() {
  // display cart items
  DOM.cartItems.innerHTML = "";
  const total = getCartTotal();

  cart.forEach((item) => {
    const row = document.createElement("div");
    row.className = "pos-item";
    const subtotal = item.price * item.quantity;

    row.innerHTML = `
      <span>${item.name}</span>
      <span style="display:flex; align-items:center; gap:6px;">
        <button class="qty-btn minus">-</button> <!-- decrease qty -->
        <strong>${item.quantity}</strong> <!-- show qty -->
        <button class="qty-btn plus">+</button> <!-- increase qty -->
        <span style="width:90px; text-align:right;">
          ${formatMoney(subtotal)} <!-- show subtotal -->
        </span>
        <button class="remove-item-btn">✕</button> <!-- remove item -->
      </span>
    `;

    row.querySelector(".plus").onclick = () => increaseQty(item.id);
    row.querySelector(".minus").onclick = () => decreaseQty(item.id);
    row.querySelector(".remove-item-btn").onclick = () =>
      removeFromCart(item.id);

    DOM.cartItems.appendChild(row);
  });

  DOM.cartTotal.textContent = formatMoney(total); // update cart total
  DOM.checkoutTotal.textContent = formatMoney(total); // update checkout total
}

// PAYMENT

function openPayment(type) {
  if (cart.size === 0) {
    alert("Warenkorb ist leer!");
    return;
  }

  DOM.paymentTitle.textContent =
    type === "bar" ? "Barzahlung" : "Kartenzahlung";
  DOM.barOptions.style.display = type === "bar" ? "block" : "none"; // toggle cash section
  DOM.paymentPanel.classList.add("active"); // show panel

  if (type === "bar") {
    DOM.cashInput.value = ""; // clear input
    DOM.cashInput.focus(); // focus input
  }
}

function closePayment() {
  DOM.paymentPanel.classList.remove("active"); // hide panel
}

function pay(option) {
  const total = getCartTotal();

  if (DOM.barOptions.style.display === "block") {
    // cash payment
    const given = parseFloat(DOM.cashInput.value.replace(",", "."));

    if (isNaN(given)) {
      alert("Bitte Betrag eingeben!");
      return;
    }

    if (given < total) {
      alert("Nicht genügend Betrag!");
      return;
    }

    const change = given - total; // calculate change

    alert(
      `Bezahlung abgeschlossen (${option})\n\nGesamt: ${formatMoney(total)}\nErhalten: ${formatMoney(given)}\nRückgeld: ${formatMoney(change)}`,
    );
  } else {
    // card
    alert(`Kartenzahlung erfolgreich!\n\nGesamt: ${formatMoney(total)}`);
  }

  // RESET POS
  cart.clear();
  renderCart();
  DOM.cashInput.value = "";
  closePayment();
}

// NUMPAD

function addNumber(num) {
  DOM.cashInput.value += num;
} // append number
function clearCash() {
  DOM.cashInput.value = "";
} // reset input
function removeLast() {
  DOM.cashInput.value = DOM.cashInput.value.slice(0, -1);
} // delete last digit
