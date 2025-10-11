// Basic client-side validation used on signup and book forms
function validateSignup() {
    const fullname = document.querySelector('input[name="fullname"]').value.trim();
    const email = document.querySelector('input[name="email"]').value.trim();
    const password = document.querySelector('input[name="password"]').value;
    const nid = document.querySelector('input[name="nid_passport"]').value.trim();
    if (!fullname || !email || !password || !nid) {
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
    // Accept dd/mm/yyyy format
    const dregex = /^\d{2}\/\d{2}\/\d{4}$/;
    if (!dregex.test(checkin) || !dregex.test(checkout)) {
        alert("Dates must be in dd/mm/yyyy format.");
        return false;
    }
    function parse(d) {
        const parts = d.split('/');
        return new Date(parts[2] + '-' + parts[1] + '-' + parts[0]);
    }
    if (parse(checkout) <= parse(checkin)) {
        alert("Checkout must be after checkin.");
        return false;
    }
    return true;
}
