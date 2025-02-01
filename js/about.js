const details = [
  {
    summary: "What services does GEPO provide?",
    content:
      "GEPO offers a wide range of services including study abroad programs, international partnerships, faculty exchange programs, and support for international students and scholars.",
  },
  {
    summary: "How can I participate in a study abroad program?",
    content:
      "Visit our Programs & Initiatives page to explore available study abroad opportunities and application procedures.",
  },
  {
    summary: "Does GEPO offer scholarships for international programs?",
    content:
      "Yes, we offer various scholarships and financial aid options for students participating in our international programs. Check our Resources & Support page for more information.",
  },
];

initializeDetails(details);

function showSection(sectionId) {
  // Hide all sections
  document.querySelectorAll('.section-content').forEach((section) => {
    section.classList.remove('active');
  });

  // Remove active class from all buttons
  document.querySelectorAll('.tab').forEach((tab) => {
    tab.classList.remove('active');
  });

  // Show the selected section and highlight the button
  document.getElementById(sectionId).classList.add('active');
  document.querySelector(`button[onclick="showSection('${sectionId}')"]`).classList.add('active');
}

