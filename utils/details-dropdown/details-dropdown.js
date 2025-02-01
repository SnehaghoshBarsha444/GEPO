/**
 * Initializes the details for the application.
 *
 * @param {Array<Object>} details - An array of objects representing details to initialize.
 * @param {string} details[].summary - A brief summary of the guideline or topic.
 * @param {string} details[].content - Detailed description or content of the guideline.
 *
 * @example
 * initializeDetails([
 *   {
 *     summary: "Identify potential partner institutions aligned with our strategic goals.",
 *     content: "Lorem ipsum dolor sit amet, consectetur adipisicing elit.",
 *   },
 *   {
 *     summary: "Develop a detailed proposal outlining the scope of the partnership.",
 *     content: "Lorem ipsum dolor, sit amet consectetur adipisicing elit.",
 *   },
 * ]);
 */

function initializeDetails(details = []) {
  const container = document.getElementById("details-container");

  details.forEach((detail) => {
    const html = `
  <div class="details-box animate-on-scroll">
    <div class="details-summary">
      <h5>${detail.summary}</h5>
      <span>+</span>
    </div>
    <div class="details-content">
      <p>
        ${detail.content}
      </p>
    </div>
  </div>
    `;

    container.innerHTML += html;
  });

  const detailSummaries = document.querySelectorAll(".details-summary");

  detailSummaries.forEach((summary) => {
    const parent = summary.parentElement;
    const content = parent.querySelector(".details-content");

    summary.addEventListener("click", () => {
      if (parent.classList.contains("open")) {
        // Closing the content
        content.style.height = "0";
        parent.classList.remove("open");
      } else {
        // Opening the content
        const contentHeight = content.scrollHeight + "px";
        parent.classList.add("open");
        content.style.height = contentHeight;
      }
    });
  });
}
