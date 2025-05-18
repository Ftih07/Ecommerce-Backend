# How to Change User Roles

This guide demonstrates how to use the User Roles API endpoint in the Ecommerce Backend.

## Endpoint Details

**URL**: `PUT /api/users/{id}/roles`  
**Authentication**: Required (Bearer Token)  
**Authorization**: Admin only  

## Request Format

```json
{
  "roles": ["admin", "seller", "customer"]
}
```

You can include any combination of available roles: "admin", "seller", "customer".

## Example Usage

### Using cURL

```bash
curl -X PUT http://your-api-url/api/users/2/roles \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"roles": ["seller", "customer"]}'
```

### Using JavaScript Fetch API

```javascript
const updateUserRoles = async (userId, roles, token) => {
  try {
    const response = await fetch(`http://your-api-url/api/users/${userId}/roles`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ roles })
    });
    
    if (!response.ok) {
      throw new Error(`Error: ${response.status}`);
    }
    
    const data = await response.json();
    console.log('User roles updated:', data);
    return data;
  } catch (error) {
    console.error('Failed to update user roles:', error);
    throw error;
  }
};

// Example usage
updateUserRoles(2, ['seller'], 'your-auth-token')
  .then(data => console.log('Success:', data))
  .catch(error => console.error('Error:', error));
```

## Response Example

```json
{
  "message": "User roles updated successfully",
  "user": {
    "user_id": 2,
    "name": "John Doe",
    "email": "john@example.com",
    "profile_image": null,
    "address": "123 Main St",
    "created_at": "2025-05-18T10:30:00.000000Z",
    "updated_at": "2025-05-18T10:30:00.000000Z",
    "roles": [
      {
        "role_id": 2,
        "name": "seller",
        "description": "Store owner/seller",
        "created_at": "2025-05-18T10:30:00.000000Z",
        "updated_at": "2025-05-18T10:30:00.000000Z",
        "pivot": {
          "user_id": 2,
          "role_id": 2
        }
      }
    ]
  }
}
```

## Error Responses

### User Not Found (404)
```json
{
  "message": "User not found"
}
```

### Validation Error (422)
```json
{
  "message": "Validation failed",
  "errors": {
    "roles": ["The roles field is required."],
    "roles.0": ["The selected roles.0 is invalid."]
  }
}
```

### Unauthorized (401)
```json
{
  "message": "Unauthenticated."
}
```

### Forbidden (403)
```json
{
  "message": "Forbidden. You need one of these roles: admin,seller"
}
```
