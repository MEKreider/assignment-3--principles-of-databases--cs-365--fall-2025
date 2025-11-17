<?php

function get_db() {
    return new PDO(
        "mysql:host=" . DBHOST . ";dbname=" . DBNAME . ";charset=utf8mb4",
        DBUSER,
        DBPASS,
        PDO_OPTIONS
    );
}

// Read: Search Tables
function search($search_term, $table_to_search) {
    $db = get_db();
    $db->query("SET block_encryption_mode = 'aes-256-cbc';");

    $like = "%{$search_term}%";

    if ($table_to_search === "Full Entries") {
        $query = "
        SELECT
            u.user_id,
            u.first_name,
            u.last_name,
            u.username,
            u.email,
            w.site_id,
            w.site_name,
            w.site_url,
            CAST(AES_DECRYPT(r.encrypted_password, " . KEY_STR . ", " . INIT_VECTOR . ") AS CHAR) AS password,
            r.comment,
            r.created_at
        FROM registers_for r
        JOIN users u ON r.user_id = u.user_id
        JOIN websites w ON r.site_id = w.site_id
        WHERE
            u.first_name LIKE ?
            OR u.last_name LIKE ?
            OR w.site_name LIKE ?
            OR w.site_url LIKE ?
            OR r.comment LIKE ?
            OR CAST(AES_DECRYPT(r.encrypted_password," . KEY_STR . "," . INIT_VECTOR . ") AS CHAR) LIKE ?";

        $params = [$like, $like, $like, $like, $like, $like];
        output_table($db, $query, $params);
        return;
    }

    if ($table_to_search === "Users") {
        $query = "
        SELECT user_id, first_name, last_name, username, email
        FROM users
        WHERE
            first_name LIKE ?
            OR last_name LIKE ?
            OR username LIKE ?
            OR email LIKE ?
            OR user_id LIKE ?";
        $params = [$like, $like, $like, $like, $like];
        output_table($db, $query, $params);
        return;
    }

    if ($table_to_search === "Websites") {
        $query = "
        SELECT site_id, site_name, site_url
        FROM websites
        WHERE
            site_name LIKE ?
            OR site_url LIKE ?
            OR site_id LIKE ?";
        $params = [$like, $like, $like];
        output_table($db, $query, $params);
        return;
    }

    if ($table_to_search === "Accounts") {
        $query = "
        SELECT
            user_id,
            site_id,
            CAST(AES_DECRYPT(encrypted_password," . KEY_STR . "," . INIT_VECTOR . ") AS CHAR) AS password,
            comment,
            created_at
        FROM registers_for
        WHERE
            user_id LIKE ?
            OR site_id LIKE ?
            OR comment LIKE ?
            OR created_at LIKE ?
            OR CAST(AES_DECRYPT(encrypted_password," . KEY_STR . "," . INIT_VECTOR . ") AS CHAR) LIKE ?";
        $params = [$like, $like, $like, $like, $like];
        output_table($db, $query, $params);
    }
}

// Read: Output
function output_table($db, $query, $params) {
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    if (!$rows) {
        echo "<h2>No Results Found</h2>";
        return;
    }

    echo "<table><thead><tr>";
    foreach (array_keys($rows[0]) as $col) {
        echo "<th>" . htmlspecialchars($col) . "</th>";
    }
    echo "</tr></thead><tbody>";

    foreach ($rows as $row) {
        echo "<tr>";
        foreach ($row as $col => $val) {
            if ($col === 'password') {
                $val = htmlspecialchars($val ?? "");
            }
            echo "<td>" . htmlspecialchars($val ?? "") . "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table>";
}

// Create User
function insert_user($first_name, $last_name, $username, $email) {
    $db = get_db();
    $stmt = $db->prepare("INSERT INTO users (first_name, last_name, username, email) VALUES (?, ?, ?, ?)");
    $stmt->execute([$first_name, $last_name, $username, $email]);
    echo "<h2>User Added</h2>";
}

// Create Website
function insert_website($website_name, $website_url) {
    $db = get_db();
    $stmt = $db->prepare("INSERT INTO websites (site_name, site_url) VALUES (?, ?)");
    $stmt->execute([$website_name, $website_url]);
    echo "<h2>Website Added</h2>";
}

// Create Account
function insert_account($site_id, $user_id, $password, $comment) {
    $db = get_db();
    $db->query("SET block_encryption_mode = 'aes-256-cbc';");

    $stmt = $db->prepare("
        INSERT INTO registers_for (user_id, site_id, encrypted_password, comment)
        VALUES (?, ?, AES_ENCRYPT(?, " . KEY_STR . ", " . INIT_VECTOR . "), ?)
    ");
    $stmt->execute([$user_id, $site_id, $password, $comment]);
    echo "<h2>Account Added</h2>";
}

function update($update_attribute, $new_value, $query_attribute, $pattern) {
    $db = get_db();

    try {
        $allowed_user_attrs = ["first_name", "last_name", "username", "email"];
        $allowed_site_attrs = ["site_name", "site_url"];
        $allowed_account_attrs = ["comment"];

        // USER UPDATE
        if (in_array($update_attribute, $allowed_user_attrs)) {
            if (!in_array($query_attribute, $allowed_user_attrs)) {
                echo "<h2>Invalid search attribute for Users. Please try again.</h2>";
                return;
            }

            $stmt = $db->prepare("UPDATE users SET $update_attribute=? WHERE $query_attribute LIKE ?");
            $stmt->execute([$new_value, "%$pattern%"]);
            echo "<h2>Users Updated: {$stmt->rowCount()}</h2>";
            return;
        }

        // WEBSITE UPDATE
        if (in_array($update_attribute, $allowed_site_attrs)) {
            if (!in_array($query_attribute, $allowed_site_attrs)) {
                echo "<h2>Invalid search attribute for Websites. Please try again.</h2>";
                return;
            }

            $stmt = $db->prepare("UPDATE websites SET $update_attribute=? WHERE $query_attribute LIKE ?");
            $stmt->execute([$new_value, "%$pattern%"]);
            echo "<h2>Websites Updated: {$stmt->rowCount()}</h2>";
            return;
        }

        // ACCOUNT UPDATE
        if ($update_attribute === "comment") {
            $allowed_register_attrs = ["comment"];

            if (!in_array($query_attribute, $allowed_register_attrs)) {
                echo "<h2>Invalid search attribute for Accounts. Please try again.</h2>";
                return;
            }

            $stmt = $db->prepare("UPDATE registers_for SET comment=? WHERE $query_attribute LIKE ?");
            $stmt->execute([$new_value, "%$pattern%"]);
            echo "<h2>Accounts Updated: {$stmt->rowCount()}</h2>";
            return;
        }

        echo "<h2>Invalid attribute given. Please try again.</h2>";

    } catch (PDOException $e) {
        echo "<h2>Invalid update. Please try again.</h2>";
    }
}

function delete($table_to_delete, $delete_attribute, $pattern) {
    $db = get_db();

    try {
        if ($table_to_delete === "users") {
            $valid = ["first_name","last_name","username","email"];
            if (!in_array($delete_attribute, $valid)) {
                echo "<h2>Invalid attribute given for Users. Please try again.</h2>";
                return;
            }

            $stmt = $db->prepare("DELETE FROM users WHERE $delete_attribute LIKE ?");
            $stmt->execute(["%$pattern%"]);
            echo "<h2>Users Deleted: {$stmt->rowCount()}</h2>";
            return;
        }

        if ($table_to_delete === "websites") {
            $valid = ["site_name","site_url"];
            if (!in_array($delete_attribute, $valid)) {
                echo "<h2>Invalid attribute given for Websites. Please try again.</h2>";
                return;
            }

            $stmt = $db->prepare("DELETE FROM websites WHERE $delete_attribute LIKE ?");
            $stmt->execute(["%$pattern%"]);
            echo "<h2>Websites Deleted: {$stmt->rowCount()}</h2>";
            return;
        }

        if ($table_to_delete === "accounts") {
            $valid = ["comment"];
            if (!in_array($delete_attribute, $valid)) {
                echo "<h2>Invalid attribute given for Accounts. Please try again.</h2>";
                return;
            }

            $stmt = $db->prepare("DELETE FROM registers_for WHERE $delete_attribute LIKE ?");
            $stmt->execute(["%$pattern%"]);
            echo "<h2>Accounts Deleted: {$stmt->rowCount()}</h2>";
            return;
        }

        echo "<h2>Invalid table — try again.</h2>";

    } catch (PDOException $e) {
        echo "<h2>Invalid delete — please try again.</h2>";
    }
}

?>
