document.addEventListener("DOMContentLoaded", async () => {
  try {
    const response = await axios.get("http://localhost:8080/api/users", {
      withCredentials: true,
    });

    const data = response.data.data;
    if (data.role !== "admin") {
      throw new Error("must be an admin");
    }

    initAdmin();
  } catch (error) {
    console.error("Error fetching data:", error);
    // location.href = "/html_files/login.html";
  }
});

async function initAdmin() {
  const partnershipsList = document.querySelector(".partnerships-list");
  function renderPartnerships(partnerships) {
    partnershipsList.innerHTML = "";
    partnerships.forEach((partnership) => {
      const partnershipElement = document.createElement("div");

      partnershipElement.classList.add("partnership-list");
      //change partnership-list background color
      partnershipElement.style.backgroundColor = "skyblue";

      partnershipElement.classList.add("partnership-item");
      partnershipElement.style.border = "1px solid #ccc";
      partnershipElement.style.padding = "10px";
      partnershipElement.style.margin = "10px 0";
      partnershipElement.style.backgroundColor = "#f9f9f9";
      partnershipElement.style.boxShadow = "0 4px 8px 0 rgba(0,0,0,0.2)";
      partnershipElement.innerHTML = `
            <h3 style="color: #333;">${partnership.name}</h3>
            <p style="color: #666;">Country: ${partnership.country}</p>
            <p style="color: #666;">Type: ${partnership.type || "Unknown"}</p>
            <p style="color: ${
              partnership.status === "Active" ? "green" : "orange"
            };">Status: ${partnership.status || "Active"}</p>
            <button class="edit-btn" data-id="${
              partnership.id
            }" style="margin-right: 5px;">Edit</button>
            <button class="delete-btn" data-id="${
              partnership.id
            }">Delete</button>
        `;
      partnershipsList.appendChild(partnershipElement);
    });
  }

  const response = await axios("http://localhost:8080/api/partners", {
    withCredentials: true,
  });

  renderPartnerships(response.data.data);

  document.getElementById("addPartnershipBtn").addEventListener("click", () => {
    // In a real application, this would open a form to add a new partnership
    alert("Add New Partnership form would appear here.");
  });

  partnershipsList.addEventListener("click", (e) => {
    if (e.target.classList.contains("edit-btn")) {
      const id = e.target.getAttribute("data-id");
      alert(`Edit partnership with ID: ${id}`);
    } else if (e.target.classList.contains("delete-btn")) {
      const id = e.target.getAttribute("data-id");
      if (confirm("Are you sure you want to delete this partnership?")) {
        // In a real application, this would send a request to the server
        alert(`Partnership with ID: ${id} deleted.`);
        partnerships = partnerships.filter((p) => p.id !== parseInt(id));
        renderPartnerships();
      }
    }
  });
}

// It form is submitted, add a new partnership

const addPartnershipForm = document.getElementById("addPartnershipForm");
document
  .getElementById("addPartnershipForm")
  .addEventListener("submit", (e) => {
    e.preventDefault();

    const newPartnership = {
      id: partnerships.length + 1,
      name: document.getElementById("instituteName").value,
      country: document.getElementById("countryName").value,
      type: document.getElementById("partnershipType").value,
      status: "Pending", // Default status for new partnerships
    };

    const fileInput = document.getElementById("partnershipImage");
    const file = fileInput.files[0];
    const reader = new FileReader();

    reader.onloadend = () => {
      newPartnership.image = reader.result; // Base64 encoded image
      partnerships.push(newPartnership);
      renderPartnerships();
      document.getElementById("addPartnershipForm").reset();
    };

    if (file) {
      reader.readAsDataURL(file);
    } else {
      alert("Please upload an image.");
    }
  });
