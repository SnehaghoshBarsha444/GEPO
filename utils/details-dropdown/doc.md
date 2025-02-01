# dropdown menu with custom details tag

`Step to initialize the dropdown menu`:-

Step 1.

```html
<!-- add css file inside head -->
<link rel="stylesheet" href="../utils/details-dropdown/details-dropdown.css" />

<!-- add this id to the container where you want the dropdown menu -->
<div id="details-container"></div>

<!-- add dropdown menu script -->
<script src="../utils/details-dropdown/details-dropdown.js"></script>
```

Step 2.

```javascript
   // initialize the dropdown menu in your script

    const details = [
      {
        summary: "summary of the content",
        content: "content here"
      },
      {...},
    {...}
    ]

    initializeDetails(details);
```
