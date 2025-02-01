document.addEventListener("DOMContentLoaded", () => {
    const calendarEl = document.getElementById("calendar")
    const addEventModal = document.getElementById("addEventModal")
    const addEventForm = document.getElementById("addEventForm")
    const cancelButton = document.getElementById("cancelButton")
    const notification = document.getElementById("notification")
  
    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: "dayGridMonth",
      headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: "dayGridMonth,timeGridWeek,timeGridDay",
      },
      events: [
        {
          title: "International Tech Conference",
          start: "2023-06-15",
          end: "2023-06-17",
          className: "conference",
        },
        {
          title: "AI in Healthcare Webinar",
          start: "2023-06-20T14:00:00",
          end: "2023-06-20T16:00:00",
          className: "webinar",
        },
      ],
      dateClick: (info) => {
        addEventModal.classList.remove("hidden")
        addEventModal.classList.add("flex")
        document.getElementById("eventStart").value = info.dateStr + "T00:00"
        document.getElementById("eventEnd").value = info.dateStr + "T23:59"
      },
      eventClick: (info) => {
        alert("Event: " + info.event.title + "\nType: " + info.event.classNames[0])
      },
      eventClassNames: (arg) => [arg.event.extendedProps.type],
    })
  
    calendar.render()
  
    addEventForm.addEventListener("submit", (e) => {
      e.preventDefault()
      const title = document.getElementById("eventTitle").value
      const start = document.getElementById("eventStart").value
      const end = document.getElementById("eventEnd").value
      const eventType = document.getElementById("eventType").value
  
      if (title && start && end && eventType) {
        calendar.addEvent({
          title: title,
          start: start,
          end: end,
          className: eventType,
          extendedProps: {
            type: eventType,
          },
        })
  
        addEventModal.classList.add("hidden")
        addEventModal.classList.remove("flex")
        addEventForm.reset()
  
        // Show notification
        notification.classList.remove("hidden")
        setTimeout(() => {
          notification.classList.add("hidden")
        }, 3000)
  
        // Force calendar to re-render
        calendar.refetchEvents()
      } else {
        alert("Please fill in all fields")
      }
    })
  
    cancelButton.addEventListener("click", () => {
      addEventModal.classList.add("hidden")
      addEventModal.classList.remove("flex")
      addEventForm.reset()
    })
  })
  
  