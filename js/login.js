document.addEventListener("DOMContentLoaded", () => {
  const loginForm = document.getElementById("loginForm");
  loginForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;

    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!emailRegex.test(email.trim())) {
      alert("Please enter a valid email address.");
      return;
    }

    if (password.trim().length < 8) {
      alert("Password must be at least 8 characters long.");
      return;
    }

    try {
      const response = await axios.post(
        "http://localhost:8080/api/login",
        { email, password },
        { withCredentials: true }
      );

      const data = response.data.data;
      if (data.role === "admin") {
        location.href = "/admin/admin.html";
      } else {
        location.href = "/index.html";
      }
    } catch (error) {
      console.error("Error:", error.message);
    }
  });
});
