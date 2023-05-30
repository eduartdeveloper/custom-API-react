# Custom API Plugin Documentation

The Custom API Plugin provides a custom API for interacting with posts in WordPress. This plugin allows you to retrieve, create, update, and delete posts using simple API endpoints. This README provides an overview of the plugin and its usage.

## Installation

1. Download the plugin zip file.
2. Upload and activate the plugin in your WordPress installation.
3. The API endpoints will be available at `/wp-json/react/v1`.

## API Documentation

### Base URL

The base URL for the API endpoints is `/wp-json/react/v1`.

### Routes

#### `GET /posts`

- Description: Retrieves all posts.
- Method: GET
- Parameters: None
- Response Format: JSON

##### Response

- Status Code: 200 (OK)
- Body: [See Response Format](#response-format)

#### `GET /posts/{id}`

- Description: Retrieves a specific post by ID.
- Method: GET
- Parameters:
  - id (required): The ID of the post.
- Response Format: JSON

##### Response

- Status Code: 200 (OK)
- Body: [See Response Format](#response-format)

#### `POST /posts`

- Description: Creates a new post.
- Method: POST
- Parameters:
  - title (required): The title of the post.
  - content (required): The content of the post.
  - meta_fields (optional): An array of meta fields associated with the post.
- Response Format: JSON

##### Request Body Example

```json
{
  "title": "New Post",
  "content": "New post content...",
  "meta_fields": [
    {
      "key": "meta_key1",
      "value": "meta_value1"
    },
    {
      "key": "meta_key2",
      "value": "meta_value2"
    }
  ]
}
```
##### Response
- Status Code: 200 (OK)
- Body: [See Response Format](#response-format)


##### `PUT /posts/{id}`
- Description: Updates an existing post
- Method: PUT
- Parameters:
	- id (required): The ID of the post to update.
	- title (optional): The new title of the post.
	- content (optional): The new content of the post.
	- meta_fields (optional): An array of updated meta fields associated with the post.
- Response Format: JSON

##### Request Body Example
```
{
  "title": "Updated Post Title",
  "meta_fields": [
    {
      "key": "meta_key1",
      "value": "new_meta_value1"
    }
  ]
}
```
##### Response
- Status Code: 200 (OK)
- Body: [See Response Format](#response-format)

##### `DELETE /posts/{id}`
- Description: Deletes a post by ID.
- Method: DELETE
- Parameters:
	- id (required): The ID of the post to delete.
- Response Format: JSON

##### Response
- Status Code: 200 (OK)
- Body: 
```
{
  "success": true,
  "message": "Post deleted successfully."
}
```
##### Authentication
Authentication is required for certain routes. Include the `Authorization` header with a valid token to authenticate the request.
- Header: `Authorization: Bearer {token}`
##### Error Handling
- Status Code: 400 (Bad Request)
- Body:
```
{
  "error": "Error message goes here."
}
```
- Status Code: 404 (Not Found)
- Body:
```
{
  "error": "Resource not found."
}
```