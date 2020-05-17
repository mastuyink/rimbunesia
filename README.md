## Intro

This API build with yii2 basic template instead of advanced because this task only have 2 endpoints, for other project i prefer to use advanced template.

## Backend

I have been created simple backed (without login) to disable and enabled user, its can be access to **BASE_URL** of application.

## API Documentation

**1. HTTP RESPONSE CODE**

| Code | Name                            | Description                                                 |
| ---- | ------------------------------- | ----------------------------------------------------------- |
| 200  | OK                              | Response is success and normal                              |
| 202  | Accepted (Unregistered User)    | Response indicated user is not registered                   |
| 400  | Bad Request (Validation Failed) | Some Input Is Invalid, please check detail on response body |

**2. ATTRIBUTE ERROR CODE**

| Code           | Description                                            |
| -------------- | ------------------------------------------------------ |
| REQUIRED       | attribute value is required                            |
| INVALID_FORMAT | format as invalid, such as email                       |
| INVALID_VALUE  | value is invalid, such as wrong password, out of range |
| DUPLICATED     | unique value duplicated                                |
| TO_SMALL       | size of value to smaller then minimal                  |
| TO_BIG         | size of value to bigger then maximal                   |
| DISABLE        | current account/user is disabled                       |

**3. ENDPOINT**

- ***[BASE_URL]/user/login*** 

  http method : **POST**

  for simulated login, API will send the access token if login successfully.

  **1. Via Form**

   **Request Attribute**

| Name         | Type   | Required | Description                                                  |
| ------------ | ------ | -------- | ------------------------------------------------------------ |
| phone_number | string | Y        | min 6 digit, max, 25 digit, allowed value between **1-9** or **٩-٠** , both combination and spaces are not allowed |
| password     | string | Y        | -                                                            |

   **Response**

api will send access token if login successfully or show error if any input invalid  

  **2. Via Social Media**

> Primary login is via social media, if any submit containing one of **email**, **social_id** or **social_media** system will validate login via social media.

 **Request Attribute**

| Name         | Type   | Required | Description                                 |
| ------------ | ------ | -------- | ------------------------------------------- |
| email        | string | Y        | email format will be validated              |
| social_id    | string | Y        | -                                           |
| social_media | string | Y        | allowed value is **GOOGLE** or **FACEBOOK** |


 **Response**

 if user is registered, api will send access token, if not registered api will send **202**  http status code and send back your request body ,or response with error code if any input invalid.


- ***[BASE_URL]/user/register***

  http method : **POST**.
  
  **1. Via Form**
  
     **Request Attribute**
  
  | Name             | Type   | Required | Description                                                  |
  | ---------------- | ------ | -------- | ------------------------------------------------------------ |
  | phone_number     | string | Y        | min 6 digit, max, 25 digit, allowed value between 1-9 **or** ٩-٠ , both combination and spaces are not allowed |
  | password         | string | Y        | min 6 max 18                                                 |
  | password_confirm | string | Y        | retyping of password                                         |
  | full_name        | string | Y        | min 3 max 100, combination Arabic and English allowed ( can be restricted if you want) |
  | email            | string | Y        | unique, min 3 max 100                                        |
  | age              | int    | Y        | min 1 max 125                                                |
  | gender           | string | Y        | allowed value is **M** or  **F**                             |
  
     **Response** 
  
     If Request success Api will send user data as response body (without password), or response with error code if any input invalid.
  
     
  
  **2. Via Social Media**
  
  > Primary login is via social media, if any submit containing  **social_id** AND **social_media** and value is not **NULL **system  will validate register via social media.
  
     **Request Attribute**
  
  | Name         | Type   | Required | Description                                                  |
  | ------------ | ------ | -------- | ------------------------------------------------------------ |
  | phone_number | string | Y        | min 6 digit, max, 25 digit, allowed value between 1-9 **or** ٩-٠ , both combination and spaces are not allowed |
  | full_name    | string | Y        | min 3 max 100, combination Arabic and English allowed ( can be restricted if you want) |
  | email        | string | Y        | unique, min 3 max 100                                        |
  | age          | int    | Y        | min 1 max 125                                                |
  | gender       | string | Y        | allowed value is **M** or  **F**                             |
  | social_id    | string | Y        | unique for each social media                                 |
  | social_media | string | Y        | allowed value is **GOOGLE** or **FACEBOOK**                  |
  
     **Response** 
  
     If Request success Api will send user data as response body, or response with error code if any input invalid.
  

> if any question or suggestion please don't hesitate to contact directly