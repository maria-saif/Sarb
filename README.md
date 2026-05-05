# Sarb – AI Voice Command and Security System

Sarb is an AI-powered system inspired by voice phishing detection technologies. 
It allows users to interact with devices and enhance security through voice commands. 
Sarb leverages Speech-to-Text and Natural Language Processing (NLP) to analyze audio inputs, detect suspicious patterns, and respond intelligently.

## Features
- Voice command recognition
- Security monitoring
- AI-powered automation
- Real-time analysis and risk detection

## Technologies Used
- Python
- Flask
- Scikit-learn
- Speech-to-Text
- Natural Language Processing (NLP)

## Purpose
The main goal of Sarb is to provide a practical, AI-driven system for security and automation, suitable for competitions, hackathons, and real-world applications.

---

## Local run (XAMPP / Windows) — updated 2026-05-05

- **Stack in this repo:** PHP + MySQL (`db_connect.php`), plus optional Python scripts for transcription. The old README mention of Flask is not what ships in `htdocs`.
- **Start:** XAMPP Control Panel → run **Apache** and **MySQL**.
- **URL:** Open the project in the browser using the **same HTTP port Apache listens on**.
  - Default XAMPP is often **`http://localhost/sarb/`** or **`http://localhost/sarb/index.php`** (port **80**).
  - If you see **`ERR_CONNECTION_REFUSED`** on **`http://localhost:8080/...`**, nothing is listening on **8080** on your PC — use port **80** instead, or change Apache’s `Listen` in `httpd.conf` and use that port consistently.
- **Database:** Create database `sarb` and import `sarb.sql`. On older MySQL/MariaDB, replace collation `utf8mb4_0900_ai_ci` with `utf8mb4_unicode_ci` before import.
- **Credentials:** Edit **`db_connect.php`** (`$username`, `$password`, `$dbname`) so they match phpMyAdmin. Default XAMPP is often `root` with an **empty** password; the app loads every page through `db_connect.php` or `includes/db_connect.php` / `includes/db_connection.php` (wrappers to the same file).
- **Simple Browser / embedded preview:** If the IDE shows a `chrome-error` / unsafe frame message, open the same URL in **Chrome/Edge** normally (not inside a restricted frame).
