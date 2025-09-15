// Basic client-side validation used on signup and book forms
function validateSignup() {
  const fullname = document.querySelector('input[name="fullname"]').value.trim();
  const email = document.querySelector('input[name="email"]').value.trim();
  const password = document.querySelector('input[name="password"]').value;
  if (!fullname || !email || !password) {
    alert("Please fill all required fields.");
    return false;
  }
  if (password.length < 6) {
    alert("Password should be at least 6 characters.");
    return false;
  }
  return true;
}

function validateBooking() {
  const name = document.querySelector('input[name="name"]').value.trim();
  const checkin = document.querySelector('input[name="checkin"]').value;
  const checkout = document.querySelector('input[name="checkout"]').value;
  if (!name || !checkin || !checkout) {
    alert("Please fill required fields.");
    return false;
  }
  if (new Date(checkout) <= new Date(checkin)) {
    alert("Checkout must be after checkin.");
    return false;
  }
  return true;
}
