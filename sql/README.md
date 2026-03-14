# Student Lab: Database Schema Vulnerabilities

This directory contains the SQL setup scripts for the laboratory.

## Vulnerabilities to Explore:

1. **Cleartext Passwords**: Examine the `users` table seeds. Admin and user passwords are often stored without encryption or with weak MD5.
2. **Missing Constraints**: The `accounts` table may lack `CHECK (balance >= 0)` constraints, enabling negative balance exploits in combination with application-level flaws.
3. **Information Schema**: Explore how table names (like `transactions_v2`) can be discovered via SQLi.
