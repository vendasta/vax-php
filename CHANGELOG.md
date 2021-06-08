# 1.3.0
- Ignore new fields on the SDK by default

# 1.1.2
- Increase the `FetchVendastaAuthToken` to have a 10s timeout instead of 5s
    - It's a reasonable case for the api takes longer than 5s
- Functions that have a string return type can't return null

# 1.1.1
- Fix function being defined multiple times bug

# 1.1.0
- Added a new request option: Exponential Backoff Retries.

# 1.0.0
- Initial Commit
