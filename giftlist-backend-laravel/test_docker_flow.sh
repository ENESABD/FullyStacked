#!/bin/bash
BASE_URL="http://localhost:8080/api"
EMAIL="test_full_$(date +%s)@example.com"
PASSWORD="password123"

echo "--- 1. Register ---"
curl -s -X POST $BASE_URL/auth/register \
  -H "Content-Type: application/json" \
  -d "{\"name\":\"Full Test\",\"email\":\"$EMAIL\",\"password\":\"$PASSWORD\"}"
echo ""

echo "--- 2. Login ---"
LOGIN_RES=$(curl -s -X POST $BASE_URL/auth/login \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"$EMAIL\",\"password\":\"$PASSWORD\"}")
echo $LOGIN_RES
TOKEN=$(echo $LOGIN_RES | grep -o '"token":"[^"]*"' | cut -d'"' -f4)
echo "Token: $TOKEN"

if [ -z "$TOKEN" ]; then echo "Login failed"; exit 1; fi

AUTH_HEADER="Authorization: Bearer $TOKEN"

echo "--- 3. Get Me ---"
curl -s -X GET $BASE_URL/auth/me -H "$AUTH_HEADER"
echo ""

echo "--- 4. Create Recipient ---"
REC_RES=$(curl -s -X POST $BASE_URL/recipients \
  -H "Content-Type: application/json" \
  -H "$AUTH_HEADER" \
  -d '{"name":"Test Mom","relationship":"Mother","notes":"Test notes"}')
echo $REC_RES
REC_ID=$(echo $REC_RES | grep -o '"id":[0-9]*' | head -n1 | cut -d':' -f2)
echo "Recipient ID: $REC_ID"

echo "--- 5. Get Recipient ---"
curl -s -X GET $BASE_URL/recipients/$REC_ID -H "$AUTH_HEADER"
echo ""

echo "--- 6. Update Recipient ---"
curl -s -X PUT $BASE_URL/recipients/$REC_ID \
  -H "Content-Type: application/json" \
  -H "$AUTH_HEADER" \
  -d '{"name":"Updated Mom"}'
echo ""

echo "--- 7. Create Gift ---"
GIFT_RES=$(curl -s -X POST $BASE_URL/gifts \
  -H "Content-Type: application/json" \
  -H "$AUTH_HEADER" \
  -d "{\"recipient_id\":$REC_ID,\"name\":\"Test Gift\",\"price\":50.00}")
echo $GIFT_RES
GIFT_ID=$(echo $GIFT_RES | grep -o '"id":[0-9]*' | head -n1 | cut -d':' -f2)
echo "Gift ID: $GIFT_ID"

echo "--- 8. Get Gift ---"
curl -s -X GET $BASE_URL/gifts/$GIFT_ID -H "$AUTH_HEADER"
echo ""

echo "--- 9. Update Gift ---"
curl -s -X PUT $BASE_URL/gifts/$GIFT_ID \
  -H "Content-Type: application/json" \
  -H "$AUTH_HEADER" \
  -d '{"purchased":true}'
echo ""

echo "--- 10. Delete Gift ---"
curl -s -X DELETE $BASE_URL/gifts/$GIFT_ID -H "$AUTH_HEADER" -w "Status: %{http_code}"
echo ""

echo "--- 11. Delete Recipient ---"
curl -s -X DELETE $BASE_URL/recipients/$REC_ID -H "$AUTH_HEADER" -w "Status: %{http_code}"
echo ""
