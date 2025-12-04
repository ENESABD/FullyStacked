#!/bin/bash
echo "1. Logging in..."
response=$(curl -s -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}')

echo "Login Response: $response"

# Extract token using grep and cut (simple parsing)
token=$(echo $response | grep -o '"token":"[^"]*"' | cut -d'"' -f4)

if [ -n "$token" ]; then
  echo
  echo "2. Token extracted successfully."
  echo "----------------------------------------"
  echo "3. Fetching Recipients..."
  curl -s -X GET http://localhost:8000/api/recipients \
    -H "Authorization: Bearer $token"
else
  echo
  echo "Failed to extract token. Check login response."
fi
