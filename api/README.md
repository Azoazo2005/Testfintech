# Student Lab: API Endpoint Vulnerabilities

This directory contains the endpoints used by the frontend to perform operations.

## Vulnerabilities to Explore:

1. **`transfer/send.php`**:
   - **IDOR (Insecure Direct Object Reference)**: The endpoint accepts `from_user_id` as a POST parameter. Changing this value allows an attacker to send money from *any* account.
   - **Mass Assignment**: Check if extra parameters can be injected into the request.

2. **Data Leakage**:
   - Error messages may reveal database structure or internal file paths.
