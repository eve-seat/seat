# SeAT API

This document will serve as a quick-reference guide to the SeAT API.

## SeAT has an API?

Yes!

## How do?

The API only accepts connections over https. Currently, there is only one endpoint in place:

### Authenticate

This endpoint can be used to authenticate user credentials against SeAT, the endpoint takes the username and password as URL parameters, and requires you to pass basic auth credentials (these must be provisioned in SeAT first). This endpoint MUST be called through a POST request.

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