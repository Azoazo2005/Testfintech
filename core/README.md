# Student Lab: Core Logic Vulnerabilities

This directory contains the central business logic of the FinTech application.

## Vulnerabilities to Explore:

1. **`Auth.php`**: 
   - **SQL Injection**: The `login()` method uses unsanitized input in SQL queries.
   - **Weak Hashing**: Passwords are stored using MD5 or cleartext (check implementation).

2. **`Transfer.php`**:
   - **Lack of Atomicity**: The `sendMoney()` method does not use SQL transactions correctly, allowing for **Race Condition** attacks.
   - **Input Validation**: Negative amounts are not checked, allowing users to "reverse" a transfer and credit themselves.
   - **IDOR**: The sender ID can be manipulated to withdraw funds from any account.
