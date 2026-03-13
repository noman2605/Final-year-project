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


