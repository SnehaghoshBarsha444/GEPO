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
    location.href = "/html_files/login.html";
  }
});

async function initAdmin() {
  // Simulating data loading
  // fetch Data from database

  try {
    const fields = [
      { tag: "activePartnerships", route: "partners" },
      { tag: "ongoingPrograms", route: "study-abroad-programs" },
      { tag: "upcomingEvents", route: "upcoming-events" },
      { tag: "pendingApplications", route: "partner-inquiry" },
    ];
    const requestPromises = fields.map((field) => {
      return axios.get(`http://localhost:8080/api/${field.route}`, {
        withCredentials: true,
      });
    });

    const responses = await Promise.all(requestPromises);

    responses.forEach((response, index) => {
      const data = response.data.data;
      console.log(data.length);
      document.getElementById(fields[index].tag).textContent = data.length;
    });
  } catch (error) {
    console.error(error);
  }

  // setTimeout(() => {
  //   document.getElementById("activePartnerships").textContent = "42";
  //   document.getElementById("ongoingPrograms").textContent = "15";
  //   document.getElementById("upcomingEvents").textContent = "8";
  //   document.getElementById("pendingApplications").textContent = "23";
  // }, 1000);

  // Chart.js implementation
  const ctx = document.getElementById("applicationsChart").getContext("2d");
  new Chart(ctx, {
    type: "bar",
    data: {
      labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
      datasets: [
        {
          label: "Applications Received",
          data: [12, 19, 3, 5, 2, 3],
          backgroundColor: "rgba(0, 123, 255, 0.5)",
          borderColor: "rgba(0, 123, 255, 1)",
          borderWidth: 1,
        },
      ],
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
        },
      },
    },
  });

  // Simulating recent activities
  const recentActivities = [
    "New partnership application received from University XYZ",
    "Study Abroad Program for Fall 2023 published",
    "Faculty Exchange Program with ABC University approved",
    "5 new student applications for International Summer School",
    "Annual Report for 2022 generated",
  ];

  const recentActivitiesList = document.getElementById("recentActivitiesList");
  recentActivities.forEach((activity) => {
    const li = document.createElement("li");
    li.textContent = activity;
    recentActivitiesList.appendChild(li);
  });
}
