<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Passwords Database</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
<h1>Personal Passwords Database</h1>

<form id="clear-results" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input id="clear-form-button" type="submit" value="Clear Results and Fields">
</form>

<?php
require_once "includes/config.php";
require_once "includes/helpers.php";

$option = ($_POST['submitted'] ?? null);

if ($option !== null) {
    switch ($option) {
        case 1:
            search($_POST['search_term'], $_POST['table_to_search']);
            break;

        case 2:
            update(
                $_POST['update_attribute'],
                $_POST['new_value'],
                $_POST['query_attribute'],
                $_POST['pattern']
            );
            break;

        case 3:
            insert_user(
                $_POST['first_name'],
                $_POST['last_name'],
                $_POST['username'],
                $_POST['email']
            );
            break;

        case 4:
            insert_website($_POST['website_name'], $_POST['website_url']);
            break;

        case 5:
            insert_account(
                $_POST['website_id'],
                $_POST['user_id'],
                $_POST['password'],
                $_POST['comment']
            );
            break;

        case 6:
            delete(
                $_POST['delete_from'],
                $_POST['delete_query_attribute'],
                $_POST['delete_pattern']
            );
            break;
    }
}
?>

<!-- Read/Search -->
<form id="search" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <fieldset>
        <legend>Search for this Term</legend>

        <p>
            <label for="table_to_search">Select the Table to Search in:</label>
            <select name="table_to_search" id="table_to_search">
                <option>Users</option>
                <option>Websites</option>
                <option>Accounts</option>
                <option>Full Entries</option>
            </select>
        </p>

        <p>
            <label for="search_term">Search Term:</label>
            <input type="text" id="search_term" name="search_term">
        </p>

        <input type="hidden" name="submitted" value="1">
        <p><input id="search_button" type="submit" value="Search" /></p>
    </fieldset>
</form>

<!-- Update -->
<form id="update" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <fieldset>
        <legend>Update a User, Website, or Account</legend>

        <p>
            <label for="update_attribute">UPDATE </label>
            <select name="update_attribute" id="update_attribute">
                <!-- user -->
                <option value="first_name">first_name</option>
                <option value="last_name">last_name</option>
                <option value="username">username</option>
                <option value="email">email</option>

                <!-- website -->
                <option value="site_name">site_name</option>
                <option value="site_url">site_url</option>

                <!-- registers_for -->
                <option value="comment">comment</option>
            </select>

            <label for="new_value">TO </label>
            <input type="text" id="new_value" name="new_value">
        </p>

        <p>
            <label for="query_attribute">WHERE </label>
            <select name="query_attribute" id="query_attribute">
                <!-- user -->
                <option value="first_name">first_name</option>
                <option value="last_name">last_name</option>
                <option value="username">username</option>
                <option value="email">email</option>

                <!-- website -->
                <option value="site_name">site_name</option>
                <option value="site_url">site_url</option>

                <!-- registers_for -->
                <option value="comment">comment</option>
            </select>

            <label for="pattern">EQUALS </label>
            <input type="text" id="pattern" name="pattern">
        </p>

        <input type="hidden" name="submitted" value="2">
        <p><input id="update_button" type="submit" value="Update" /></p>
    </fieldset>
</form>

<!-- create user -->
<form id="insert_user" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <fieldset>
        <legend>Add a New User</legend>

        <p>
            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" required>
        </p>

        <p>
            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" required>
        </p>

        <p>
            <label for="username">Username:</label>
            <input type="text" name="username" required>
        </p>

        <p>
            <label for="email">Email:</label>
            <input type="email" name="email" required>
        </p>

        <input type="hidden" name="submitted" value="3">
        <p><input id="insert_user_button" type="submit" value="Add User" /></p>
    </fieldset>
</form>

<!-- create website -->
<form id="insert_website" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <fieldset>
        <legend>Add a New Website</legend>

        <p>
            <label for="website_name">Website Name:</label>
            <input type="text" name="website_name" placeholder="Example" required>
        </p>

        <p>
            <label for="website_url">Website URL:</label>
            <input type="url" name="website_url" placeholder="https://example.com" required>
        </p>

        <input type="hidden" name="submitted" value="4">
        <p><input id="insert_website_button" type="submit" value="Add Website" /></p>
    </fieldset>
</form>

<!-- create registers_for -->
<form id="insert_account" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <fieldset>
        <legend>Register a New Account</legend>

        <p>
            <label for="website_id">Website ID:</label>
            <input type="number" name="website_id" required>
        </p>

        <p>
            <label for="user_id">User ID:</label>
            <input type="number" name="user_id" required>
        </p>

        <p>
            <label for="password">Password:</label>
            <input type="password" name="password" required>
        </p>

        <p>
            <label for="comment">Comment:</label>
            <textarea id="comment" name="comment" rows="5" cols="35"></textarea>
        </p>

        <input type="hidden" name="submitted" value="5">
        <p><input id="insert_account_button" type="submit" value="Register" /></p>
    </fieldset>
</form>

<!-- delete -->
<form id="delete" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <fieldset>
        <legend>Delete a User, Website, or Account</legend>

        <p>
            <label for="delete_from">Table:</label>
            <select name="delete_from" id="delete_from">
                <option value="users">Users</option>
                <option value="websites">Websites</option>
                <option value="accounts">Accounts</option>
            </select>
        </p>

        <p>
            <label for="delete_query_attribute">Attribute:</label>
            <select name="delete_query_attribute" id="delete_query_attribute">
                <!-- user -->
                <option value="first_name">first_name</option>
                <option value="last_name">last_name</option>
                <option value="username">username</option>
                <option value="email">email</option>

                <!-- website -->
                <option value="site_name">site_name</option>
                <option value="site_url">site_url</option>

                <!-- account -->
                <option value="comment">comment</option>
            </select>

            <label for="delete_pattern">Value:</label>
            <input type="text" id="delete_pattern" name="delete_pattern">
        </p>

        <input type="hidden" name="submitted" value="6">
        <p><input id="delete_button" type="submit" value="Delete" /></p>
    </fieldset>
</form>

</body>
</html>
