Response Envelope
=======================


----------------------------------------

### Standard Trovebox API response envelope

Every API returns a JSON response adhering to the following format.

    {
      message: (string),
      code: (int),
      result: (mixed)
    }

#### Message

The _message_ is a string which describes the action taken.
It's purely for informational purposes and should never be used in your code or relied on.

#### Code

The _code_ is an integer representing the status of the API call.
Typically the _code_ value should be _200_ but anything between _200_ and _299_ indicates a successful response.
The photo upload API, for example, will return a _202_ response indicating that the resource has been created.

Below are some common codes:

* _200_, The API call was successful
* _202_, Resource was created successfully
* _403_, Authentication failed when trying to complete the API call
* _404_, The requested endpoint could not be found
* _500_, An unknown error occured and hopefully the message has more information

#### Result

The _result_ can be any simple or complex value.
Consult the documentation for the endpoint you're using for information on what the _result_ will be.
The purpose of the _result_ is to allow you to continue processing the request.
We'll try to return the information you'll most likely need and aim to keep you from having to make a subsequent call to get it.
