# SeAT API

This document will serve as a quick-reference guide to the SeAT API.

### SeAT has an API?

Yes!

### Tell me more!

SeAT exposes a RESTful API to allow for 3rd party application integration. The API only accepts connections over HTTPs and is protected using authentication is source IP restrictions.

### Current endpoints

https://{seat_base_URL}/api/v1/authenticate  
https://{seat_base_URL}/api/v1/key

# Authenticate

This endpoint can be used to authenticate user credentials against SeAT. The endpoint takes the username and password as POST data and requires you to pass basic auth credentials (these must be provisioned in SeAT first).

API Endpoint:
https://{seat_base_URL}/api/v1/authenticate

Example:

```
curl -X 'POST' --user "testappuser:testapppass" https://{seat_base_URL}/api/v1/authenticate --data "username=admin&password=adminpass"
```
Output:

```JSON
{
  "error": false,
  "user": {
    "id": "1",
    "username": "admin",
    "email": "admin@seat.local",
    "last_login": "2014-12-13 21:31:49",
    "last_login_source": "192.168.22.1",
    "created_at": "2014-12-11 02:45:20",
    "updated_at": "2014-12-13 21:31:49",
    "deleted_at": null,
    "remember_token": null,
    "activation_code": null,
    "activated": "1"
  },
  "groups": [
    {
      "id": "1",
      "name": "Administrators",
      "permissions": {
        "superuser": 1
      },
      "created_at": "2014-12-12 18:19:27",
      "updated_at": "2014-12-13 00:03:12"
    }
  ]
}%
```

## Errors

Upon an error occuring, the "error" field will be set to true, and a "message" field will contain more information.

# Key

This endpoint can be used to manipulated EVE API keys within SeAT. The endpoint may be consumed as follows:
  - POST https://{seat_base_URL}/api/v1/key. Add a new API Key or update an existing one.
  - GET https://{seat_base_URL}/api/v1/key. List ALL API keys in SeAT.
  - DELETE https://{seat_base_URL}/api/v1/key/<key_id>. Delete a key from SeAT defined by the keyID.

Example:

```bash
$ curl -X POST --user "1234UbfrDAu3:V9nf0YuqH8Onzb2n" http://localhost:8000/api/v1/key --data "keyID=123456&vCode=qwertyqwertqwertyqwertyqwertyqwertyqwerty"
```

Output:

```json
{
  "error": false,
  "status": "Key has been added"
}
```