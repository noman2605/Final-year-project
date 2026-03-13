/* =====================================
   FAQ SECTION
   Click করলে question open/close হবে
===================================== */

const questions = document.querySelectorAll(".faq-question");

questions.forEach(question => {

  // Question এ click event
  question.addEventListener("click", () => {

    // Answer element select
    const answer = question.nextElementSibling;

    // সব answer close করা
    document.querySelectorAll(".faq-answer").forEach(a => {
      if (a !== answer) {
        a.style.display = "none";
      }
    });

    // সব question থেকে active class remove
    questions.forEach(q => {
      if (q !== question) {
        q.classList.remove("active");
      }
    });

    // Toggle open/close
    if (answer.style.display === "block") {
      answer.style.display = "none";
      question.classList.remove("active");
    } else {
      answer.style.display = "block";
      question.classList.add("active");
    }

  });

});


/* =====================================
   BOOKING FORM SECTION
   Ticket booking + payment validation
===================================== */

const bookingForm = document.getElementById("bookingForm");

if (bookingForm) {

  bookingForm.addEventListener("submit", function (e) {

    // Page reload বন্ধ
    e.preventDefault();

    // Payment method check
    let payment = document.querySelector('input[name="payment"]:checked');

    if (!payment) {
      alert("Please select a payment method");
      return;
    }

    // Success message show
    document.getElementById("successMsg").innerText =
      "🎉 Payment Successful! Your ticket has been booked.";

    // Form reset
    bookingForm.reset();

  });

}


