{* templates/CRM/Extest/Page/MyCustomPage.tpl *}

<h3>Fetch Recordings by Campaign and Date Range</h3>

{* Add campaign selection and date range picker form *}
<form method="post" action="">
    <label for="campaign">Select Campaign:</label>
    <select id="campaign" name="campaign" required>
        <option value="cpf">CPF</option>
        <option value="jtc">JTC</option>
        <!-- Add more campaigns as needed -->
    </select>
    <br><br>
    <label for="start_date">Start Date:</label>
    <input type="date" id="start_date" name="start_date" required>
    <label for="end_date">End Date:</label>
    <input type="date" id="end_date" name="end_date" required>
    <br><br>
    <input type="submit" name="fetch_files" value="Fetch Recordings" />
</form>
