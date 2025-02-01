// map script
// Add markers for countries
function initMapMarker() {
  const promises = countries.map(fetchCountryAndMakeMarker);

  Promise.all(promises);
}
async function fetchCountryAndMakeMarker(countryName) {
  return fetch(
    `https://nominatim.openstreetmap.org/search?country=${countryName}&format=json`
  )
    .then((response) => response.json())
    .then((data) => {
      if (data.length > 0) {
        const { lat, lon } = data[0];

        const name =
          countryName.trim().split("")[0].toUpperCase() +
          countryName.trim().slice(1);

        const countryCoords = {
          name,
          coords: [Number(lon), Number(lat)],
        };

        console.log(`${countryName} coordinates:`, countryCoords);

        countries.push(countryCoords);
        addMarker(countryCoords);
      } else {
        console.error("Country not found!");
      }
    })
    .catch((error) => console.error("Error fetching data:", error));
}

// Initialize the map
const map = new maplibregl.Map({
  container: "map", // Container ID
  style: "https://basemaps.cartocdn.com/gl/positron-gl-style/style.json", // Map style URL
  center: [78.9629, 50.3511148],
  zoom: 0.1, // Fit the whole world
  interactive: true, // Disable interactions
});
// Disable zooming with mouse scroll or touchpad
map.scrollZoom.disable();
// Disable zooming via double click
map.doubleClickZoom.disable();
// Disable zooming using the keyboard (+/- keys)
map.keyboard.disable();

window.addEventListener("resize", (e) => {
  console.log(document.innerHight, document.innerHeight);

  map.zoomTo(0.1);
});

map.on("load", () => {
  console.log("Map loaded successfully");

  map.setMaxBounds([
    [-175, -85], // Southwest corner
    [180, 85], // Northeast corner
  ]);
});

function addMarker(country) {
  // Create a DOM element for the marker
  const el = document.createElement("div");
  el.style.backgroundImage =
    "url('https://cdn-icons-png.flaticon.com/512/684/684908.png')";
  el.className = "marker";

  // Add a click event to the marker
  el.addEventListener("click", () => {
    window.open(
      `https://www.google.com/maps/search/jis+university+in+${country.name}`,
      "_blank"
    );
  });

  // Add the marker to the map
  new maplibregl.Marker(el).setLngLat(country.coords).addTo(map);

  // Add hover tooltip
  const popup = new maplibregl.Popup({
    closeButton: false,
    closeOnClick: false,
    offset: 25,
  }).setHTML(`<div class="tooltip">${country.name}</div>`);

  el.addEventListener("mouseenter", () =>
    popup.setLngLat(country.coords).addTo(map)
  );
  el.addEventListener("mouseleave", () => popup.remove());
}

map.on("error", (e) => {
  console.error("Map-libre error:", e.error);
});

// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// success stories script
const cardContainer = document.querySelector(".swiper-wrapper");

function initStories() {
  stories.forEach(renderStory);
  initSwiper();
}
function renderStory(story) {
  const html = `
  <div class="swiper-slide">
  <div class="card animate-on-scroll">
              <div class="studentDetails">
                <img
                  src="${story.image}"
                  alt="${story.name}"
                />
                <p class="studentName">${story.name}</p>
              </div>
              <p class="story">${story.story}</p>
              <div class="educationDetails">
                <div class="stream">${story.stream}</div>
                <div class="universityImage">
                <img
                  src="${story.universityImage}"
                  alt="${story.university}"
                />
                </div>
              </div>
            </div>
            </div>
            `;

  cardContainer.innerHTML += html;
}

// Swiper Script
function initSwiper() {
  const swiper = new Swiper(".swiper", {
    slidesPerView: 1,
    spaceBetween: 20,
    loop: true,
    grabCursor: true,
    autoplay: {
      delay: 2500,
      disableOnInteraction: false,
    },
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    breakpoints: {
      770: {
        slidesPerView: 2,
        spaceBetween: 20,
      },
      1080: {
        slidesPerView: 3,
        spaceBetween: 30,
      },
    },
  });
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// form validation script
const form = document.querySelector("form");
const inputField = document.querySelectorAll(".input-field");
const formError = document.getElementById("form-error");

// method for call on error
function onError(field, message) {
  const parentField = field.parentElement;

  parentField.classList.add("error");
  parentField.classList.remove("success");
  const errorElement = parentField.querySelector(".error-message");
  errorElement.textContent = message;
}

// method for call on success
function onSuccess(field) {
  const parentField = field.parentElement;

  parentField.classList.remove("error");
  parentField.classList.add("success");
  formError.style.display = "none";
  const errorElement = parentField.querySelector(".error-message");
  errorElement.textContent = "";
}

// form validation
inputField.forEach((field) => {
  field.addEventListener("input", (e) => {
    const value = field.value.trim();
    const name = field.name.split("-").join(" ");

    if (value === "") {
      const message = `${name} is Required`;
      onError(field, message);
    } else if (field.type !== "email" && value.length < 3) {
      onError(field, `${name} must be at least 3 characters long`);
    } else {
      const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
      if (field.type === "email" && !emailPattern.test(value)) {
        onError(field, `Email Is Invalid`);
      } else {
        onSuccess(field);
      }
    }
  });
});

// form submit to backend
form.addEventListener("submit", (e) => {
  e.preventDefault();

  const successFields = document.querySelectorAll(".success");
  console.log(inputField.length, successFields.length);

  if (successFields.length !== inputField.length) {
    formError.style.display = "block";
    formError.textContent = "Please fill in all required fields.";
    return;
  }

  formError.style.display = "none";
  const formData = new FormData(form);
  const data = Object.fromEntries(formData.entries());
  console.log(data);
  form.reset();
});

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
const details = [
  {
    summary:
      "Identify potential partner institutions aligned with our strategic goals.",
    content:
      "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eveniet repudiandae nihil voluptas, necessitatibus porro veritatis assumenda quo sint, ipsam, corporis quos? Nostrum laboriosam odit autem blanditiis accusamus facere excepturi aliquam!",
  },
  {
    summary:
      "Conduct initial discussions to explore mutual interests and benefits.",
    content:
      "Lorem ipsum dolor sit, amet consectetur adipisicing elit. Ducimus, fugit in? Assumenda quam consequatur nisi soluta magnam ex eligendi. Dolorem quos non hic doloremque. Corporis quae cupiditate consequatur impedit officia veritatis culpa, eum eveniet. Et labore numquam ipsum architecto corporis.",
  },
  {
    summary:
      "Develop a detailed proposal outlining the scope of the partnership.",
    content:
      "Lorem ipsum dolor, sit amet consectetur adipisicing elit. Mollitia error id alias laboriosam maxime eveniet minima aliquid suscipit reiciendis eos!",
  },
  {
    summary:
      "Review and approval process by relevant committees and leadership.",
    content:
      "Lorem ipsum dolor sit amet consectetur adipisicing elit. Sequi exercitationem, aliquam porro minima ex accusantium quasi soluta ut repellendus nam nemo hic amet dignissimos repudiandae, ratione possimus vitae. Nemo molestias quam laborum similique cupiditate alias!",
  },
  {
    summary: "Draft and sign a Memorandum of Understanding (MoU).",
    content:
      "Lorem ipsum dolor sit amet consectetur adipisicing elit. Saepe neque in odit dicta voluptas iste, ut tempore, quo suscipit fugiat atque excepturi dolorum totam nisi animi, sapiente recusandae nulla accusantium minima minus harum et repudiandae. Delectus sapiente corrupti iusto velit! Amet repellendus quam necessitatibus accusamus, animi saepe voluptatem sint tenetur.",
  },
  {
    summary: "Implement the partnership activities and monitor progress.",
    content:
      "Lorem ipsum dolor sit amet consectetur, adipisicing elit. Explicabo ullam impedit sunt aut, laborum vitae porro adipisci ab nemo doloribus quidem, asperiores consectetur repellendus quos nam est natus ipsa voluptatibus veritatis. Quos voluptates modi excepturi corporis, laborum nam aliquam numquam? Nulla minus possimus, laboriosam ab nostrum quibusdam quisquam. Fugiat, consectetur necessitatibus voluptate in modi ea itaque quae animi non hic.",
  },
  {
    summary: "Regular evaluation and renewal of the partnership.",
    content:
      "Lorem ipsum dolor, sit amet consectetur adipisicing elit. Assumenda, quam necessitatibus odio sed ipsa magni autem rerum, amet at placeat fugiat libero, dolor est? Quisquam asperiores unde culpa enim sit doloremque impedit magni consequatur optio neque maiores nulla laboriosam nam facere amet, corrupti blanditiis fugiat.",
  },
];

initializeDetails(details);
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// All render Data
const countries = ["USA", "India", "Germany", "France", "Canada"];
initMapMarker();
const stories = [
  {
    name: "Naveenkumar",
    image: "https://publicassets.leverageedu.com/stories/naveen.webp",
    story:
      "I applied to 4 universities and I got offers from 2 universities. GEPO helped me through entire process to pursue masters. They have separate teams to handle all this.",
    stream: "Data Science",
    university: "Central Michigan University",
    universityImage:
      "https://lepublicassets.leverageedu.com/testimonials/universities/2260.png",
  },
  {
    name: "Shreya",
    image: "https://publicassets.leverageedu.com/stories/shreya.webp",
    story:
      "GEPO is proactive, detail-oriented, and trustworthy. They made my study abroad dream a breeze. Shoutout to the team for showcasing their excellence!",
    stream: "Management",
    university: "Berlin School of Business and Innovation",
    universityImage:
      "https://publicassets.leverageedu.com/testimonials/universities/1032.png",
  },
  {
    name: "Hanna",
    image: "https://publicassets.leverageedu.com/stories/hanna.webp",
    story:
      "GEPO made it incredibly convenient. Deepa, the consultant, provided excellent guidance. I'm thrilled with the all-in-one support for loans, forex, and accommodation.",
    stream: "Data Science",
    university: "Humber University",
    universityImage:
      "https://lepublicassets.leverageedu.com/testimonials/universities/2092.png",
  },
  {
    name: "Kshitij",
    image: "https://publicassets.leverageedu.com/stories/kshitij.webp",
    story:
      "My GEPO coach made studying abroad a breeze. From university shortlisting to visa application, they guided me every step of the way.",
    stream: "Data Science",
    university: "University of Birmingham",
    universityImage:
      "https://lepublicassets.leverageedu.com/testimonials/universities/109.png",
  },
  {
    name: "Samad",
    image: "https://publicassets.leverageedu.com/stories/samad.webp",
    story:
      "Extremely satisfied with GEPO for my college application process. Deserves a perfect 5/5 rating!",
    stream: "Data Science",
    university: "Queen Mary University of London",
    universityImage:
      "https://lepublicassets.leverageedu.com/testimonials/universities/128.png",
  },
  {
    name: "Shubham",
    image: "https://publicassets.leverageedu.com/stories/shubham.webp",
    story:
      "Smooth process, supportive loan team, highly satisfied with GEPO's loan experience. Great service!",
    university: "University of Illinois at Urbana-Champaign",
    universityImage:
      "https://lepublicassets.leverageedu.com/testimonials/universities/26.png",
  },
];
initStories();
