// Add FAQ toggle functionality
const faqToggles = document.querySelectorAll(".faq-toggle");

faqToggles.forEach((toggle) => {
  toggle.addEventListener("click", (event) => {
    const faqItem = event.target.closest(".faq-item");
    const answer = faqItem.querySelector(".faq-answer");
    const isActive = answer.style.display === "block";

    // Reset other FAQ answers
    document.querySelectorAll(".faq-answer").forEach((ans) => {
      ans.style.display = "none";
    });

    // Reset toggle buttons
    faqToggles.forEach((btn) => {
      btn.textContent = "+";
    });

    // Toggle current FAQ answer
    if (!isActive) {
      answer.style.display = "block";
      event.target.textContent = "-";
    } else {
      answer.style.display = "none";
    }
  });
});
