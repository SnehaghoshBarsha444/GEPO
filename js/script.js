document.addEventListener("DOMContentLoaded", () => {
  // adding favicon for all page
  const link = document.createElement("link");
  link.rel = "shortcut icon";
  link.href = `${
    location.pathname.includes("index.html") ? "." : ".."
  }/assets/images/logo.png`;
  link.type = "image/x-icon";
  document.head.appendChild(link);

  // Animate on scroll
  const animateOnScroll = () => {
    const elements = document.querySelectorAll(".animate-on-scroll");
    elements.forEach((element) => {
      const elementTop = element.getBoundingClientRect().top;
      const windowHeight = window.innerHeight;
      if (elementTop < windowHeight - 130) {
        element.classList.add("animated");
      } else if (elementTop > windowHeight + 50) {
        element.classList.remove("animated");
      }
    });
  };

  window.addEventListener("scroll", animateOnScroll);
  animateOnScroll(); // Initial check on page load

  // Add floating button
  renderFloatingButton();
});

function renderFloatingButton() {
  const floatingButtonsContainer = document.createElement("div");
  floatingButtonsContainer.classList.add("floating-buttons");

  // Create WhatsApp button
  const whatsappButton = document.createElement("a");
  whatsappButton.href = "https://wa.me/123456789";
  whatsappButton.classList.add("floating-button");

  const whatsappIcon = document.createElement("i");
  whatsappIcon.classList.add("fab", "fa-whatsapp");

  // Append WhatsApp icon to the button
  whatsappButton.appendChild(whatsappIcon);

  // Create Phone button
  const phoneButton = document.createElement("a");
  phoneButton.href = "tel:123456789";
  phoneButton.classList.add("floating-button");

  const phoneIcon = document.createElement("i");
  phoneIcon.classList.add("fas", "fa-phone");

  // Append Phone icon to the button
  phoneButton.appendChild(phoneIcon);

  // Append buttons to the container
  floatingButtonsContainer.appendChild(whatsappButton);
  floatingButtonsContainer.appendChild(phoneButton);

  // Append the container to the body (or any other parent element)
  document.body.appendChild(floatingButtonsContainer);
}

// hamburger section
let hamburger = document.querySelector(".hamburger");
let navBar = document.querySelector("#navBar");

hamburger.addEventListener("click", (e) => {
  let menuClosed = navBar.className.includes("close");
  if (menuClosed) {
    const menuHeight = navBar.querySelector("ul").scrollHeight + 10;
    console.log(menuHeight);
    navBar.classList.remove("close"); // Change the class to open;

    navBar.style.height = `${menuHeight}px`; // Show the menu
    e.target.style.transform = "rotate(180deg)";
  } else {
    navBar.classList.add("close");
    navBar.style.height = "0"; // Hide the menu
    e.target.style.transform = "rotate(-180deg)";
  }
});

window.addEventListener("resize", () => {
  if (window.innerWidth >= 1134) {
    navBar.style.height = "0";
    navBar.classList.add("close");
  }
});

document.addEventListener("DOMContentLoaded", () => {
  if (window.innerWidth <= 1134) {
    navBar.style.height = "0";
    navBar.classList.add("close");
  }
});
