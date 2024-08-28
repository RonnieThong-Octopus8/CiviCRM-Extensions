{* templates/CRM/AutoCreateContact/Page/AutoCreatePage.tpl *}

<style>
  .auto-create-container {
    font-family: 'Arial', sans-serif;
    line-height: 1.6;
    margin: 20px;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 800px;
    margin: 0 auto;
  }

  .auto-create-container h3 {
    color: #0056b3;
    text-align: center;
  }

  .auto-create-container form {
    margin-bottom: 20px;
    padding: 15px;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .auto-create-container label {
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
  }

  .auto-create-container select,
  .auto-create-container input[type="date"],
  .auto-create-container input[type="submit"] {
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    width: calc(100% - 24px);
    max-width: 100%;
    box-sizing: border-box;
  }

  .auto-create-container input[type="submit"] {
    background-color: #007bff;
    color: #ffffff;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
    display: block;
    margin: 0 auto;
  }

  .auto-create-container input[type="submit"]:hover {
    background-color: #0056b3;
  }

  #loadingIndicator {
    display: none;
    text-align: center;
    margin-top: 20px;
  }

  #loadingIndicator img {
    width: 50px; /* Adjust size as needed */
    height: 50px;
  }

  #resultsContainer {
    padding: 15px;
    background-color: #e9ecef;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-top: 20px;
  }

  .auto-create-container ul {
    list-style-type: none;
    padding: 0;
  }

  .auto-create-container li {
    background-color: #ffffff;
    padding: 10px;
    margin-bottom: 5px;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }
</style>

<div class="auto-create-container">
  <h3>Fetch and Process Recordings by Campaign and Date Range</h3>

  <form id="fetchForm" method="post" action="">
      <label for="campaign">Select Campaign:</label>
      <select id="campaign" name="campaign" required>
          <option value="jtc">JTC</option>
      </select>
      <label for="start_date">Start Date:</label>
      <input type="date" id="start_date" name="start_date" required>
      <label for="end_date">End Date:</label>
      <input type="date" id="end_date" name="end_date" required>
      <input type="submit" value="Fetch and Process Recordings" />
  </form>

  <!-- Loading Indicator -->
  <div id="loadingIndicator">
    <img src="https://i.gifer.com/ZKZg.gif" alt="Loading...">
    <p>Loading... Please wait.</p>
  </div>

  <div id="resultsContainer"></div>
</div>

{literal}
<script type="text/javascript">
  document.getElementById("fetchForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent form submission

    const button = event.target.querySelector('input[type="submit"]');
    const loadingIndicator = document.getElementById('loadingIndicator');
    button.disabled = true;
    loadingIndicator.style.display = 'block'; // Show loading indicator

    CRM.alert('Processing started. Please wait...', 'Processing', 'info'); // Show CiviCRM status message

    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData.entries());

    // Log the data being sent
    console.log('Sending data:', data);

    fetch(window.location.href, {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ action: "create_contact_activities", ...data })
    })
    .then(response => {
      console.log('Raw response:', response);
      if (!response.ok) {
        return response.text().then(text => { throw new Error(text); });
      }
      return response.json();
    })
    .then(data => {
      console.log('Parsed JSON response:', data);
      if (data.status === 'success') {
        displayResults(data.contacts, data.activities || []); // Ensure activities is an array if undefined
        CRM.alert('Processing completed successfully!', 'Success', 'success');
      } else {
        CRM.alert(data.message, 'Error', 'error');
      }
    })
    .catch(error => {
      console.error("Error:", error);
      CRM.alert('An unexpected error occurred: ' + error.message, 'Error', 'error');
    })
    .finally(() => {
      button.disabled = false;
      loadingIndicator.style.display = 'none'; // Hide loading indicator
    });
  });

  function displayResults(contacts, activities) {
    const resultsContainer = document.getElementById('resultsContainer');
    resultsContainer.innerHTML = ''; // Clear previous results

    // Display existing contacts
    const existingContacts = activities.filter(activity => contacts[activity.contact_id] === undefined);
    if (existingContacts.length > 0) {
      const existingContactList = document.createElement('ul');
      existingContactList.innerHTML = '<h3>Existing Contacts</h3>';

      // Group activities by contact ID
      const activitiesByContact = {};
      existingContacts.forEach(activity => {
        if (!activitiesByContact[activity.contact_id]) {
          activitiesByContact[activity.contact_id] = [];
        }
        activitiesByContact[activity.contact_id].push(activity);
      });

      for (const [contactID, activities] of Object.entries(activitiesByContact)) {
        const contactItem = document.createElement('li');
        contactItem.innerHTML = `<strong>Contact ID:</strong> ${contactID}, <strong>Contact Name:</strong> ${activities[0].contact_name}`;

        const activityList = document.createElement('ul');
        activityList.innerHTML = '<h4>Activities:</h4>';
        activities.forEach(activity => {
          const activityItem = document.createElement('li');
          activityItem.textContent = `Activity ID: ${activity.activity_id}`;
          activityList.appendChild(activityItem);
        });

        contactItem.appendChild(activityList);
        existingContactList.appendChild(contactItem);
      }

      resultsContainer.appendChild(existingContactList);
    } else {
      resultsContainer.innerHTML += '<h3>No Existing Contacts</h3>';
    }

    // Display contacts created
    if (Object.keys(contacts).length > 0) {
      const contactList = document.createElement('ul');
      contactList.innerHTML = '<h3>Contacts Created</h3>';

      for (const [contactID, phoneNumber] of Object.entries(contacts)) {
        const contactItem = document.createElement('li');
        contactItem.innerHTML = `<strong>Contact ID:</strong> ${contactID}, <strong>Phone:</strong> ${phoneNumber}`;

        // Convert contactID to String to ensure proper comparison
        const contactIDStr = String(contactID);

        // Append activities to each contact
        const activitiesForContact = activities.filter(a => String(a.contact_id) === contactIDStr);
        if (activitiesForContact.length > 0) {
          const activityList = document.createElement('ul');
          activitiesForContact.forEach(activity => {
            const activityItem = document.createElement('li');
            activityItem.textContent = `Activity ID: ${activity.activity_id}`;
            activityList.appendChild(activityItem);
          });
          contactItem.appendChild(activityList);
        } else {
          contactItem.innerHTML += '<p>No activities created for this contact.</p>';
        }

        contactList.appendChild(contactItem);
      }
      resultsContainer.appendChild(contactList);
    } else {
      resultsContainer.innerHTML += '<h3>No Contacts Created</h3>';
    }
  }
</script>
{/literal}
