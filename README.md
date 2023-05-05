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
$nickname = InputValidator::GetHttpFieldValueIfIsValid($_POST["nickname"], "STRING", array(
            "allowEmpty"=>false,
            "allowNumbers"=>false
            ), null);
$age = InputValidator::GetHttpFieldValueIfIsValid($_POST["age"], "INT", array(
       "allowEmpty"=>false,
       "minNumberValue"=>13
       ), null);

//Check if all fields is valid
if (is_null($nickname) == false && is_null($age) == false){

    //... run the API logic...

}
if (is_null($nickname) == true || is_null($age) == true)
    //... show the invalid inputs error...

?>
```

Now let's understand the code! To validate a value using this library, you must call method `GetHttpFieldValueIfIsValid()` and then pass the following parameters...

- <b>1st</b> - The value to be validated. You must pass `$_POST[]` or `$_GET[]`, including the name of the POST/GET field you expect to get the value.
- <b>2nd</b> - The type of value you expect to get from the field. Of course the value is always a text value, however sometimes we expect content of a certain type. For example, in an Age field, we expect a numerical value only, so it is necessary to inform the type of value you expect from this field. If you inform that you expect a value of type `INT`, and the field contains a text, it will not be considered valid by the library. Currently, the value types supported by the library are `STRING`, `INT`, `FLOAT`, and `BOOL`.
- <b>3rd</b> - The Validation Parameters. They act as "rules" that you tell method `GetHttpFieldValueIfIsValid()` to take into account in validation. For example, Parameter `"allowEmpty"=>false` will cause an empty value to be considered invalid, Parameter `"allowLetters"=>false` will cause a value that contains letters to be considered invalid, and so on. Later you will see a table with all Validation Parameters supported by this library.
- <b>4th</b> - <s>We will talk about this parameter later in this documentation...</s>

After calling this method and informing all these parameters, it can return you one of the following things...

- <b>NULL</b> - The method will return a `NULL` value if the field that you validated did not pass the validation, taking into account the Validation Parameters that you entered.
- <b>The Value</b> - The method will return the value exactly as it was inside the `$_POST[]` or `$_GET[]`, which means that the field value passed the validation!

So, basically if the method `GetHttpFieldValueIfIsValid()` returns you the value of `$_POST[]`/`$_GET[]`, it means that the value is valid, but if the method returns you `NULL`, it means that the value of `$_POST[]`/`$_GET[]` was invalid.

<b>Example:</b> Let's say we need to validate an age that is in `$_POST["age"]`. We want an empty entry or an age less than 13 to be considered INVALID. For that, we can use the following code...

```php
//Get the validation result into the "$age" variable
$age = InputValidator::GetHttpFieldValueIfIsValid($_POST["age"], "INT", array(
    "allowEmpty"=>false,
    "minNumberValue"=>13
    ), null);

//If '$_POST["age"]' is   "  "          then "$age" will be   "NULL"  (INVALID)...
//If '$_POST["age"]' is   "24"          then "$age" will be   "24"    (VALID)...
//If '$_POST["age"]' is   "7"           then "$age" will be   "NULL"  (INVALID)...
//If '$_POST["age"]' is   "some text"   then "$age" will be   "NULL"  (INVALID)...
//and so on...
```

<h3>Now, let's talk about the 4th parameter...</h3>

As you may know, if you plan to use input sent from a client in an SQL query, you must escape the string. For this we normally use PHP `mysqli_real_escape_string()` method. Here comes the 4th parameter of method `GetHttpFieldValueIfIsValid()`!

If you created a connection to a MySQL or MariaDB database using `mysqli_connect()` you should have a variable resulting from the connection. If you pass your connection to the 4th parameter of method `GetHttpFieldValueIfIsValid()`, it will already return you an automatically escaped string, <b>**IF**</b> the string is considered valid! See example below...

```php
<?php

//Do connection to MySQL/MariaDB
$connection = mysqli_connect("127.0.0.1", "mariadb", "databasePassword123", "db_name", "3306");


//Do the validation of $_POST["message"] to get a escaped string if the value from it is valid...
//$_POST["message"] content is: [       Hi! My name is "Luan"!       ]
$messageToSend = InputValidator::GetHttpFieldValueIfIsValid($_POST["message"], "STRING", array(
    "allowEmpty"=>false
    ), $connection);

//$messageToSend content is:    [      Hi! My name is \"Luan\"!      ]

?>
```

<b>Remembering</b> that, this 4th parameter is completely **optional**. If you just inform `NULL` for this 4th parameter, method `GetHttpFieldValueIfIsValid()` will return you a normal string, without escaping it. It will just do the normal job of validating the input, as in the examples above in this documentation.

<h3>Validation Parameters</h3>

These are all validation parameters supported by method `GetHttpFieldValueIfIsValid()` at the moment.

| Parameter           | Works On Type | Description                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       |
| ------------------- | ------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| allowEmpty          | STRING        | Defines whether an empty Field should be allowed or not allowed. Requires a `Bool` value.                                                                                                                                                                                                                                                                                                                                                                                                                                                                         |
| minChars            | STRING        | Defines the minimum amount of characters that the Field must have. Requires a `Int` value.                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| maxChars            | STRING        | Defines the maximum amount of characters that the Field must have. Requires a `Int` value.                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| allowNumbers        | STRING        | Defines if the Field can or cannot have numbers. Requires a `Bool` value.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         |
| allowLetters        | STRING        | Defines if the Field may or may not have letters. This includes letters from A to Z and also letters that have accents (eg Á, Ç, Ñ etc). Requires a `Bool` value.                                                                                                                                                                                                                                                                                                                                                                                                 |
| allowSpace          | STRING        | Defines if the Field can or cannot have spaces. Requires a `Bool` value.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          |
| allowLineBreak      | STRING        | Defines if the Field can or cannot have line breaks. Requires a `Bool` value.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     |
| specialCharsAllowed | STRING        | Defines whether the Field should allow special characters, and if so, which characters will be allowed. You can use the value "none" to not allow any special characters, for example: '"specialCharsAllowed"=>"none"'. If you only want to allow some special characters, you can enter them in a list using the character "," as a separator, for example '"specialCharsAllowed"=>"@,#,&"'. If you want to include the "," character in this list of allowed characters, instead of typing the "," character, type the word "comma". Requires a `String` value. |
| customRegex         | STRING        | With this parameter you can define a custom Regex that will be used together with the other Validation Parameters to validate the Field. The Regex must be in the format "/regex/". Without a Flag letter at the end. For example '"customRegex"=>"/^[0-9]*$/"'. You can use a regex when you want the field to be validated to also obey a certain form or pattern. Requires a `String` value.                                                                                                                                                                   |
| minNumberValue      | INT or FLOAT  | Defines the minimum value (in numeral) that is allowed in the Field. Requires a `Int` or `Float` value.                                                                                                                                                                                                                                                                                                                                                                                                                                                           |
| maxNumberValue      | INT or FLOAT  | Defines the maximum value (in numeral) that is allowed in the Field. Requires a `Int` or `Float` value.                                                                                                                                                                                                                                                                                                                                                                                                                                                           |
| allowNumberZero     | INT or FLOAT  | Defines if the zero numeral should be allowed or not in the Field. Requires a `Bool` value.                                                                                                                                                                                                                                                                                                                                                                                                                                                                       |
| allowNumberNegative | INT or FLOAT  | Defines if negative numerals should be allowed in the Field. Requires a `Bool` value.                                                                                                                                                                                                                                                                                                                                                                                                                                                                             |
| allowNumberPositive | INT or FLOAT  | Defines if positive numerals should be allowed in the Field. Requires a `Bool` value.                                                                                                                                                                                                                                                                                                                                                                                                                                                                             |
| mustBe              | BOOL          | Defines whether the Boolean value in the Field must be TRUE or FALSE to be considered valid. For example '"mustBe"=>"true"'. Requires a `String` value.                                                                                                                                                                                                                                                                                                                                                                                                           |

If you have a validation parameter suggestion, please send it through the "<b>Issues</b>" tab, your suggestion will be very welcome!

<h3>Some quick Regex!</h3>

Before we move on to the `ReceiveUploadedFileFromClientIfIsValid()` method, here are some quick Regex that can be used to validate Values that come out of commonly used Inputs in HTML such as "&lt;input type="email" /&gt;", "&lt;input type="date" /&gt;" etc!

```html
<!-- DATE -->
<input type="date" />
<!-- produces values formatted as "YYYY-MM-DD" -->
<!-- /^\d{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])$/ -->

<!-- DATETIME-LOCAL -->
<input type="datetime-local" />
<!-- produces values formatted as "YYYY-MM-DDTHH:MM" -->
<!-- /(19|20)[0-9][0-9]-(0[0-9]|1[0-2])-(0[1-9]|([12][0-9]|3[01]))T([01][0-9]|2[0-3]):[0-5][0-9]/ -->

<!-- EMAIL -->
<input type="email" />
<!-- produces values formatted as "account@example.com" -->
<!-- /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/ -->

<!-- MONTH -->
<input type="month" />
<!-- produces values formatted as "YYYY-MM" -->
<!-- /([12]\d{3}-(0[1-9]|1[0-2]))/ -->

<!-- TIME -->
<input type="time" />
<!-- produces values formatted as "HH:MM" -->
<!-- /^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/ -->

<!-- URL -->
<input type="url" />
<!-- produces values formatted as "https://domain.com/folder/page" -->
<!-- /((([A-Za-z]{3,9}:(?:\/\/)?)(?:[-;:&=\+\$,\w]+@)?[A-Za-z0-9.-]+|(?:www.|[-;:&=\+\$,\w]+@)[A-Za-z0-9.-]+)((?:\/[\+~%\/.\w-_]*)?\??(?:[-\+=&;%@.\w_]*)#?(?:[\w]*))?)/ -->

<!-- WEEK -->
<input type="week" />
<!-- produces values formatted as "YYYY-WNN" -->
<!-- /^(\d{4})-W(0[1-9]|[1-4][0-9]|5[0-3])$/ -->
```

<h2>ReceiveUploadedFileFromClientIfIsValid()</h2>

a

# How to use?