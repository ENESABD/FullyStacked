# GiftList API - cURL Test Commands

## Base URL

Assuming the API is running on: http://localhost:8000

## 1. Health Check

curl -X GET http://localhost:8000/health

## 2. Authentication

### Register a new user

curl -X POST http://localhost:8000/auth/register \
 -H "Content-Type: application/json" \
 -d "{\"name\":\"John Doe\",\"email\":\"john@example.com\",\"password\":\"password123\"}"

### Login (save the token from response)

curl -X POST http://localhost:8000/auth/login \
 -H "Content-Type: application/json" \
 -d "{\"email\":\"john@example.com\",\"password\":\"password123\"}"

### Get current user (replace YOUR_TOKEN_HERE with actual token)

curl -X GET http://localhost:8000/auth/me \
 -H "Authorization: Bearer YOUR_TOKEN_HERE"

## 3. Recipients

### Create a recipient

curl -X POST http://localhost:8000/recipients \
 -H "Content-Type: application/json" \
 -H "Authorization: Bearer YOUR_TOKEN_HERE" \
 -d "{\"name\":\"Mom\",\"relationship\":\"Mother\",\"notes\":\"Birthday in June\"}"

### Get all recipients

curl -X GET http://localhost:8000/recipients \
 -H "Authorization: Bearer YOUR_TOKEN_HERE"

### Get single recipient (replace 1 with actual recipient ID)

curl -X GET http://localhost:8000/recipients/1 \
 -H "Authorization: Bearer YOUR_TOKEN_HERE"

### Update recipient

curl -X PUT http://localhost:8000/recipients/1 \
 -H "Content-Type: application/json" \
 -H "Authorization: Bearer YOUR_TOKEN_HERE" \
 -d "{\"name\":\"Mom\",\"relationship\":\"Mother\",\"notes\":\"Birthday in June - loves gardening\"}"

### Delete recipient

curl -X DELETE http://localhost:8000/recipients/1 \
 -H "Authorization: Bearer YOUR_TOKEN_HERE"

## 4. Gifts

### Create a gift

curl -X POST http://localhost:8000/gifts \
 -H "Content-Type: application/json" \
 -H "Authorization: Bearer YOUR_TOKEN_HERE" \
 -d "{\"recipient_id\":1,\"name\":\"Garden Tools Set\",\"description\":\"Premium garden tools\",\"price\":89.99,\"url\":\"https://example.com/product\",\"purchased\":false}"

### Get all gifts

curl -X GET http://localhost:8000/gifts \
 -H "Authorization: Bearer YOUR_TOKEN_HERE"

### Get gifts for a specific recipient

curl -X GET "http://localhost:8000/gifts?recipientId=1" \
 -H "Authorization: Bearer YOUR_TOKEN_HERE"

### Get gifts by recipient endpoint

curl -X GET http://localhost:8000/recipients/1/gifts \
 -H "Authorization: Bearer YOUR_TOKEN_HERE"

### Get single gift (replace 1 with actual gift ID)

curl -X GET http://localhost:8000/gifts/1 \
 -H "Authorization: Bearer YOUR_TOKEN_HERE"

### Update gift

curl -X PUT http://localhost:8000/gifts/1 \
 -H "Content-Type: application/json" \
 -H "Authorization: Bearer YOUR_TOKEN_HERE" \
 -d "{\"name\":\"Garden Tools Set\",\"description\":\"Premium garden tools with case\",\"price\":99.99,\"purchased\":true}"

### Delete gift

curl -X DELETE http://localhost:8000/gifts/1 \
 -H "Authorization: Bearer YOUR_TOKEN_HERE"

## 5. Complete Test Flow

# Step 1: Register

curl -X POST http://localhost:8000/auth/register \
 -H "Content-Type: application/json" \
 -d "{\"name\":\"Jane Smith\",\"email\":\"jane@example.com\",\"password\":\"secret123\"}"

# Step 2: Login and save token

curl -X POST http://localhost:8000/auth/login \
 -H "Content-Type: application/json" \
 -d "{\"email\":\"jane@example.com\",\"password\":\"secret123\"}"

# Step 3: Create recipient (use token from step 2)

curl -X POST http://localhost:8000/recipients \
 -H "Content-Type: application/json" \
 -H "Authorization: Bearer YOUR_TOKEN_HERE" \
 -d "{\"name\":\"Dad\",\"relationship\":\"Father\",\"notes\":\"Loves technology\"}"

# Step 4: Create gift for recipient (use recipient ID from step 3)

curl -X POST http://localhost:8000/gifts \
 -H "Content-Type: application/json" \
 -H "Authorization: Bearer YOUR_TOKEN_HERE" \
 -d "{\"recipient_id\":1,\"name\":\"Wireless Headphones\",\"description\":\"Noise cancelling\",\"price\":199.99,\"url\":\"https://example.com/headphones\"}"

# Step 5: View all gifts

curl -X GET http://localhost:8000/gifts \
 -H "Authorization: Bearer YOUR_TOKEN_HERE"

## Error Testing

### Test without authorization

curl -X GET http://localhost:8000/recipients

### Test with invalid token

curl -X GET http://localhost:8000/recipients \
 -H "Authorization: Bearer invalid_token"

### Test invalid login

curl -X POST http://localhost:8000/auth/login \
 -H "Content-Type: application/json" \
 -d "{\"email\":\"wrong@example.com\",\"password\":\"wrongpass\"}"

### Test missing required fields

curl -X POST http://localhost:8000/recipients \
 -H "Content-Type: application/json" \
 -H "Authorization: Bearer YOUR_TOKEN_HERE" \
 -d "{\"relationship\":\"Friend\"}"

## Notes:

- Replace YOUR_TOKEN_HERE with the actual JWT token from login response
- Replace ID numbers (1, 2, etc.) with actual IDs from your responses
- For Windows PowerShell, use different syntax (see below)

## PowerShell Syntax (Windows)

**RECOMMENDED: Use Invoke-RestMethod (see bottom section) - it's more reliable in PowerShell**

### Using curl.exe with JSON file (Most Reliable)

Create a file `register.json`:

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123"
}
```

Then run:

```powershell
curl.exe -X POST http://localhost:8000/auth/register -H "Content-Type: application/json" -d "@register.json"
```

### OR use single-line with proper escaping:

#### Register

```powershell
curl.exe -X POST http://localhost:8000/auth/register -H "Content-Type: application/json" -d """{\""name\"":\""John Doe\"",\""email\"":\""john@example.com\"",\""password\"":\""password123\""}"""
```

#### Login

```powershell
curl.exe -X POST http://localhost:8000/auth/login -H "Content-Type: application/json" -d """{\""email\"":\""john@example.com\"",\""password\"":\""password123\""}"""
```

### Get Current User (replace YOUR_TOKEN_HERE)

```powershell
curl.exe -X GET http://localhost:8000/auth/me -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Create Recipient

```powershell
curl.exe -X POST http://localhost:8000/recipients -H "Content-Type: application/json" -H "Authorization: Bearer YOUR_TOKEN_HERE" -d """{\""name\"":\""Mom\"",\""relationship\"":\""Mother\"",\""notes\"":\""Birthday in June\""}"""
```

### Get All Recipients

```powershell
curl.exe -X GET http://localhost:8000/recipients -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Create Gift

```powershell
curl.exe -X POST http://localhost:8000/gifts -H "Content-Type: application/json" -H "Authorization: Bearer YOUR_TOKEN_HERE" -d """{\""recipient_id\"":1,\""name\"":\""Garden Tools Set\"",\""description\"":\""Premium tools\"",\""price\"":89.99,\""purchased\"":false}"""
```

### Get All Gifts

```powershell
curl.exe -X GET http://localhost:8000/gifts -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Alternative: Using Invoke-RestMethod (PowerShell Native - RECOMMENDED)

#### Register

```powershell
Invoke-RestMethod -Uri "http://localhost:8000/auth/register" -Method POST -ContentType "application/json" -Body (@{name="John Doe";email="john@example.com";password="password123"}|ConvertTo-Json)
```

#### Login

```powershell
$response = Invoke-RestMethod -Uri "http://localhost:8000/auth/login" -Method POST -ContentType "application/json" -Body (@{email="john@example.com";password="password123"}|ConvertTo-Json)
$token = $response.data.token
```

#### Get Current User

```powershell
Invoke-RestMethod -Uri "http://localhost:8000/auth/me" -Method GET -Headers @{Authorization="Bearer $token"}
```

#### Get All Recipients

```powershell
Invoke-RestMethod -Uri "http://localhost:8000/recipients" -Method GET -Headers @{Authorization="Bearer $token"}
```

#### Create Recipient

```powershell
Invoke-RestMethod -Uri "http://localhost:8000/recipients" -Method POST -Headers @{Authorization="Bearer $token"} -ContentType "application/json" -Body (@{name="Mom";relationship="Mother";notes="Birthday in June"}|ConvertTo-Json)
```

#### Get Single Recipient (ID=1)

```powershell
Invoke-RestMethod -Uri "http://localhost:8000/recipients/1" -Method GET -Headers @{Authorization="Bearer $token"}
```

#### Update Recipient (ID=1)

```powershell
Invoke-RestMethod -Uri "http://localhost:8000/recipients/1" -Method PUT -Headers @{Authorization="Bearer $token"} -ContentType "application/json" -Body (@{name="Mom";relationship="Mother";notes="Birthday in June - loves gardening"}|ConvertTo-Json)
```

#### Delete Recipient (ID=1)

```powershell
Invoke-RestMethod -Uri "http://localhost:8000/recipients/1" -Method DELETE -Headers @{Authorization="Bearer $token"}
```

#### Get All Gifts

```powershell
Invoke-RestMethod -Uri "http://localhost:8000/gifts" -Method GET -Headers @{Authorization="Bearer $token"}
```

#### Get Gifts for Recipient (ID=1)

```powershell
Invoke-RestMethod -Uri "http://localhost:8000/gifts?recipientId=1" -Method GET -Headers @{Authorization="Bearer $token"}
```

#### Create Gift

```powershell
Invoke-RestMethod -Uri "http://localhost:8000/gifts" -Method POST -Headers @{Authorization="Bearer $token"} -ContentType "application/json" -Body (@{recipient_id=1;name="Garden Tools Set";description="Premium tools";price=89.99;purchased=0}|ConvertTo-Json)
```

#### Update Gift (ID=1)

```powershell
Invoke-RestMethod -Uri "http://localhost:8000/gifts/1" -Method PUT -Headers @{Authorization="Bearer $token"} -ContentType "application/json" -Body (@{name="Garden Tools Set";description="Premium tools with case";price=99.99;purchased=1}|ConvertTo-Json)
```

#### Delete Gift (ID=1)

```powershell
Invoke-RestMethod -Uri "http://localhost:8000/gifts/1" -Method DELETE -Headers @{Authorization="Bearer $token"}
```
