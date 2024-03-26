<?php

require 'connection.php';

function getUserCount()
{
    global $conn;
    try {
        $query = "SELECT COUNT(mod_num) as count FROM edu_modules";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return strval($row['count']);
        }
        return "0";
    } catch (Exception $e) {
        error_log("Database Error: " . $e->getMessage());
        return "0";
    }
}

function getTrailModulesCount()
{
    global $conn;
    try {
        $query = "SELECT COUNT(mod_num) as count FROM edu_modules_trial";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return strval($row['count']);
        }
        return "0";
    } catch (Exception $e) {
        error_log("Database Error: " . $e->getMessage());
        return "0";
    }
}

function getUserCounts()
{
    global $conn;
    try {
        $query = "SELECT COUNT(user_id) as count FROM edu_users";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return strval($row['count']);
        }
        return "0";
    } catch (Exception $e) {
        error_log("Database Error: " . $e->getMessage());
        return "0";
    }
}
?>
