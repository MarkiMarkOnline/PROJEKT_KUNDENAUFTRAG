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
  alert("Bezahlung abgeschlossen: " + option);
  closePayment();
}
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
