# Backend Input Validator

The Backend Input Validator is a PHP library to be used in the Backend of your website. The objective of this library is to provide a simple, easy to use and low code system for validating text or file uploads in your website Backend APIs. Which is very common in all types of websites, such as registration forms for example.

This library, when used in conjunction with <a href="https://github.com/marcos4503/frontend-input-validator">Frontend Input Validator</a>, provides EXTREMELY easy way to validate user/client inputs in the Frontend or Backend of your website.

# How it works?

The operation of this API is very simple. There are several ways to validate entries received on POST, GET or UPLOAD. But normally, what we do a lot is get the input we want from POST or GET, and then we program our logic to validate each field... More or less like this...

```php
<?php

//Get the inputs from client...
$userName = $_POST["name"] ?? "";
$nickName = $_POST["nickname"] ?? "";

//Now, runs the validation logic...
if($userName != "" && strlen($userName) <= 12 /*...*/)
    //and continues...
>?
```

The big problem with this is that it can be VERY annoying. Not to mention the amount of code we need to repeat in each PHP API, the tests we need to do, the chances of having bugs or unexpected things and etc...

It was with these problems in mind that this library was created. It was created to facilitate the validation of client inputs in PHP APIs in a uniform, easy, non-repetitive way, and that uses little code. Using this library you will have two methods available for your PHP codes. The `GetHttpFieldValueIfIsValid()` method and the `ReceiveUploadedFileFromClientIfIsValid()` method.

<h2>GetHttpFieldValueIfIsValid()</h2>

This method has the task of validating inputs that arrive through GET and POST. It is quite easy to use this method. See sample code below...

```php
<?php

//Receive the processed inputs...
$nickname = InputValidator::GetHttpFieldValueIfIsValid($_POST["nickname"], "STRING", array("allowEmpty"=>false, "allowNumbers"=>false), null);
$age = InputValidator::GetHttpFieldValueIfIsValid($_POST["age"], "INT", array("allowEmpty"=>false, "minNumberValue"=>13), null);

//Check if all fields is valid
if (is_null($nickname) == false && is_null($age) == false){

    //... run the API logic...

}
if (is_null($nickname) == true || is_null($age) == true)
    //... show the invalid inputs error...

?>
```

Now let's understand the code! To validate a value using this library, you must call method `GetHttpFieldValueIfIsValid()` and then pass the following parameters...

- <b>1st</b> - The value. You must pass `$_POST[]` or `$_GET[]`, including the name of the POST/GET field you expect to get the value.
- <b>2nd</b> - The type of value you expect to get from this field. Of course the value is always a text value, however sometimes we expect content of a certain type. For example, in an Age field, we expect a numerical value only, so it is necessary to inform the type of value you expect from this field. If you inform that you expect a value of type `INT`, and the field contains a text, it will not be considered valid by the library. Currently, the value types supported by the library are `STRING`, `INT`, `FLOAT`, and `BOOL`.
- <b>3rd</b> - The Validation Parameters. They act as "rules" that you tell method `GetHttpFieldValueIfIsValid()` to take into account in validation. For example, Parameter `"allowEmpty"=>false` will cause an empty value to be considered invalid, Parameter `"allowLetters"=>false` will cause a value that contains letters to be considered invalid, and so on. Later you will see a table with all Validation Parameters supported by this library.
- <b>4th</b> - We will talk about this parameter later in this documentation...

After calling this method and informing all these parameters, it can return you one of the following things...

- <b>NULL</b> - The method will return a `NULL` value if the field that you validated did not pass the validation, taking into account the Validation Parameters that you entered.
- <b>The Value</b> - The method will return the value exactly as it was inside the `$_POST[]` or `$_GET[]`, which means that the field value passed the validation!

So, basically if the method `GetHttpFieldValueIfIsValid()` returns you the value of `$_POST[]`/`$_GET[]`, it means that the value is valid, but if the method returns you `NULL`, it means that the value of `$_POST[]`/`$_GET[]` was invalid.

<h3>Now, let's talk about the 4th parameter...</h3>

As you may know, if you plan to use input sent from a client in an SQL query, you must escape the string. For this we normally use PHP `mysqli_real_escape_string()` method. Here comes the 4th parameter of method `GetHttpFieldValueIfIsValid()`!

If you created a connection to a MySQL or MariaDB database using `mysqli_connect()` you should have a variable resulting from the connection. If you pass your connection to the 4th parameter of method `GetHttpFieldValueIfIsValid()`, it will already return you an automatically escaped string, IF the string is considered valid! See example below...

```php
<?php

//Do connection to MySQL/MariaDB
$connection = mysqli_connect("127.0.0.1", "mariadb", "databasePassword123", "db_name", "3306");



//$_POST["message"] content is: [       Hi! My name is "Luan"!       ]
$messageToSend = InputValidator::GetHttpFieldValueIfIsValid($_POST["message"], "STRING", array("allowEmpty"=>false), $connection);



//$messageToSend content is: [       Hi! My name is \"Luan\"!       ]

?>
```

<h2>ReceiveUploadedFileFromClientIfIsValid()</h2>

a

# How to use?