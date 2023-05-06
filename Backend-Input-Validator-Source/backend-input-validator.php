<?php

/*
 * Backend Input Validator
 * 
 * This class is part of "Backend Input Validator" library created with the mission of simplify user input validation in backend of websites
*/

/**
 * Backend Input Validator
 *
 * This class is part of "Backend Input Validator" library created with the mission of simplify user input validation in backend of websites
 *
 * @copyright  2023 Marcos Tomaz
 * @license    https://github.com/marcos4503/backend-input-validator/blob/main/LICENSE   MIT
 */ 
class InputValidator{

    //Private variables
    private $libraryName = "Backend Input Validator";
    
    //Static variables
    public static $placeHolder0 = "";

    //Core methods

    private function __construct() {}

    /**
     * This methods validate a value from "$_POST" or "$_GET" and return the value if is valid. If not, return a NULL.
     */ 
    public static function GetHttpFieldValueIfIsValid($valueToValidate, string $expectedType, array $validationParameters, $mySqliConnection){
        //If the expected type is not valida, cancel
        if($expectedType != "STRING" && $expectedType != "INT" && $expectedType != "FLOAT" && $expectedType != "BOOL"){
            echo("BackendInputValidator Error: " . "The expected value for the value to be validated is not valid! Expected value must be STRING, INT, FLOAT or BOOL!");
            return null;
        }

        //If the value to validate is null, set a empty string instead of a NULL value
        if(is_null($valueToValidate) == true)
            $valueToValidate = "";

        //Get the validation parameters informed
        $parameters = get_object_vars((object) $validationParameters);
        //Prepare the validation parameters                                                                                                        //Types that each parameter will be used to validate
        $allowEmpty = (array_key_exists("allowEmpty", $parameters) == true) ? $parameters["allowEmpty"] : true;                                    //STRING
        $minChars = (array_key_exists("minChars", $parameters) == true) ? $parameters["minChars"] : 0;                                             //STRING
        $maxChars = (array_key_exists("maxChars", $parameters) == true) ? $parameters["maxChars"] : 0;                                             //STRING
        $allowNumbers = (array_key_exists("allowNumbers", $parameters) == true) ? $parameters["allowNumbers"] : true;                              //STRING
        $allowLetters = (array_key_exists("allowLetters", $parameters) == true) ? $parameters["allowLetters"] : true;                              //STRING
        $allowSpace = (array_key_exists("allowSpace", $parameters) == true) ? $parameters["allowSpace"] : true;                                    //STRING
        $allowLineBreak = (array_key_exists("allowLineBreak", $parameters) == true) ? $parameters["allowLineBreak"] : true;                        //STRING
        $specialCharsAllowed = (array_key_exists("specialCharsAllowed", $parameters) == true) ? $parameters["specialCharsAllowed"] : "all";        //STRING
        $customRegex = (array_key_exists("customRegex", $parameters) == true) ? $parameters["customRegex"] : "/.*/";                               //STRING
        $minNumberValue = (array_key_exists("minNumberValue", $parameters) == true) ? $parameters["minNumberValue"] : -9223372036854775808;        //INT, FLOAT
        $maxNumberValue = (array_key_exists("maxNumberValue", $parameters) == true) ? $parameters["maxNumberValue"] : 9223372036854775808;         //INT, FLOAT
        $allowNumberZero = (array_key_exists("allowNumberZero", $parameters) == true) ? $parameters["allowNumberZero"] : true;                     //INT, FLOAT
        $allowNumberNegative = (array_key_exists("allowNumberNegative", $parameters) == true) ? $parameters["allowNumberNegative"] : true;         //INT, FLOAT
        $allowNumberPositive = (array_key_exists("allowNumberPositive", $parameters) == true) ? $parameters["allowNumberPositive"] : true;         //INT, FLOAT
        $mustBe = (array_key_exists("mustBe", $parameters) == true) ? $parameters["mustBe"] : "any";                                               //BOOL

        //Prepare the response
        $isValueValid = true;

        //============================================ If expected type is STRING ============================================//
        if($expectedType == "STRING"){
            //Get the value to be valided
            $value = $valueToValidate;

            //Check if is empty
            if($allowEmpty == false)
                if($value === null || trim($value) === "")
                    $isValueValid = false;
            //Check if have the min characters
            if($minChars > 0 && strlen($value) < $minChars)
                $isValueValid = false;
            //Check if have the max characters
            if($maxChars > 0 && strlen($value) > $maxChars)
                $isValueValid = false;
            //Check if have numbers
            if($allowNumbers == false && preg_match("/\d/", $value) == true)
                $isValueValid = false;
            //Check if have letters
            if($allowLetters == false && preg_match("/[A-Za-zÀ-ÖØ-öø-ÿčćČĆŠšŸœŒ]/", $value) == true)
                $isValueValid = false;
            //Check if have space
            if($allowSpace == false && preg_match("/\s/", $value) == true)
                $isValueValid = false;
            //Check if have line break
            if($allowLineBreak == false && preg_match("/\r|\n/", $value) == true)
                $isValueValid = false;
            
            //Check if have unallowed special characters
            if($specialCharsAllowed != "all"){
                //Prepare the array of special chars allowed
                $charsAllowed = array();
                //If have special chars allowed, insert into the array
                if($specialCharsAllowed != "none"){
                    $charsExploded = explode(",", $specialCharsAllowed);
                    for ($i = 0; $i < count($charsExploded); $i++) {
                        if($charsExploded[$i] == "comma")
                            array_push($charsAllowed, ",");
                        if($charsExploded[$i] != "comma")
                            array_push($charsAllowed, $charsExploded[$i]); 
                    }
                }

                //Build a custom regex to allow especified special characters (Supports A-z, 0-9, SPACE and "àáâãäåÀÁÂÃÄÅ çčćÇČĆ èéêëÈÉÊË ìíîïÌÍÎÏ ñÑ Šš òóôõöøÒÓÔÕÖØ ùúûüÙÚÛÜ ß ÿŸýÝ æÆ œŒ" by default!)
                $buildedRegex = "^[a-zA-Z0-9 À-Ö Ø-ö ø-ÿ čćČĆŠšŸœŒ ";
                for($i = 0; $i < count($charsAllowed); $i++){
                    //If is a special character for regex syntax
                    if($charsAllowed[$i] == ".") { $buildedRegex .= "\\."; continue; }
                    if($charsAllowed[$i] == "*") { $buildedRegex .= "\\*"; continue; }
                    if($charsAllowed[$i] == "+") { $buildedRegex .= "\\+"; continue; }
                    if($charsAllowed[$i] == "?") { $buildedRegex .= "\\?"; continue; }
                    if($charsAllowed[$i] == "^") { $buildedRegex .= "\\^"; continue; }
                    if($charsAllowed[$i] == "$") { $buildedRegex .= "\\$"; continue; }
                    if($charsAllowed[$i] == "|") { $buildedRegex .= "\\|"; continue; }
                    if($charsAllowed[$i] == "(") { $buildedRegex .= "\\("; continue; }
                    if($charsAllowed[$i] == ")") { $buildedRegex .= "\\)"; continue; }
                    if($charsAllowed[$i] == "[") { $buildedRegex .= "\\["; continue; }
                    if($charsAllowed[$i] == "]") { $buildedRegex .= "\\]"; continue; }
                    if($charsAllowed[$i] == "{") { $buildedRegex .= "\\{"; continue; }
                    if($charsAllowed[$i] == "}") { $buildedRegex .= "\\}"; continue; }
                    if($charsAllowed[$i] == "\\") { $buildedRegex .= "\\\\"; continue; }
                    //If is a normal character
                    $buildedRegex .= $charsAllowed[$i];
                }
                $buildedRegex .= "]+$";

                //If the string don't match with the custom regex, the string have unallowed special chars, so is invalid
                if($value !== null && trim($value) !== "")                             //<- Only validate if is not empty
                    if(preg_match(("/".$buildedRegex."/"), $value) == false)
                        $isValueValid = false;
            }

            //Check if match with the custom regex (if has provided a custom regex)
            if($customRegex != "/.*/" && preg_match($customRegex, $value) == false)
                $isValueValid = false;
        }

        //============================================ If expected type is INT ============================================//
        if($expectedType == "INT"){
            //Get the value to be valided
            $value = $valueToValidate;

            //First, check if the value is numeric
            if(is_numeric($value) == false)
                $isValueValid = false;
            //Check if is a integer
            if(is_int(((int) $value)) == false)
                $isValueValid = false;
            //Check if have line break
            if(preg_match("/\r|\n/", $value) == true)
                $isValueValid = false;
            //Check if is a float number
            if(str_contains($value, ".") == true || str_contains($value, ",") == true)
                $isValueValid = false;
            
            //Get the value converted to INT
            $valueConvertedToInt = (int) $value;

            //Check if respects the min value
            if($minNumberValue != -9223372036854775808 && $valueConvertedToInt < $minNumberValue)
                $isValueValid = false;
            //Check if respects the max value
            if($maxNumberValue != 9223372036854775808 && $valueConvertedToInt > $maxNumberValue)
                $isValueValid = false;
            //Check if is zero
            if($allowNumberZero == false && $valueConvertedToInt == 0)
                $isValueValid = false;
            //Check if is negative
            if($allowNumberNegative == false && $valueConvertedToInt < 0)
                $isValueValid = false;
            //Check if is positive
            if($allowNumberPositive == false && $valueConvertedToInt > 0)
                $isValueValid = false;
        }

        //============================================ If expected type is FLOAT ============================================//
        if($expectedType == "FLOAT"){
            //Get the value to be valided
            $value = $valueToValidate;

            //First, check if the value is numeric
            if(is_numeric($value) == false)
                $isValueValid = false;
            //Check if is a float
            if(is_float(((float) $value)) == false)
                $isValueValid = false;
            //Check if have line break
            if(preg_match("/\r|\n/", $value) == true)
                $isValueValid = false;
            
            //Get the value converted to FLOAT
            $valueConvertedToFloat = (float) $value;

            //Check if respects the min value
            if($minNumberValue != -9223372036854775808 && $valueConvertedToFloat < $minNumberValue)
                $isValueValid = false;
            //Check if respects the max value
            if($maxNumberValue != 9223372036854775808 && $valueConvertedToFloat > $maxNumberValue)
                $isValueValid = false;
            //Check if is zero
            if($allowNumberZero == false && $valueConvertedToFloat == 0)
                $isValueValid = false;
            //Check if is negative
            if($allowNumberNegative == false && $valueConvertedToFloat < 0)
                $isValueValid = false;
            //Check if is positive
            if($allowNumberPositive == false && $valueConvertedToFloat > 0)
                $isValueValid = false;
        }

        //============================================ If expected type is BOOL ============================================//
        if($expectedType == "BOOL"){
            //Get the value to be valided
            $value = $valueToValidate;

            //Check if is empty
            if($value === null || trim($value) === "")
                $isValueValid = false;
            //Check if is a float number
            if(str_contains($value, ".") == true || str_contains($value, ",") == true)
                $isValueValid = false;
            
            //Get the lowered value
            $valueLowered = strtolower($value);

            //Check if is a boolean
            if($valueLowered != "1" && $valueLowered != "0" && $valueLowered != "true" && $valueLowered != "false")
                $isValueValid = false;

            //Get the value converted to bool
            $valueConvertedToBool = filter_var($valueLowered, FILTER_VALIDATE_BOOLEAN);

            //Check if respects must be TRUE
            if($mustBe == "true" && $valueConvertedToBool == false)
                $isValueValid = false;
            //Check if respects must be FALSE
            if($mustBe == "false" && $valueConvertedToBool == true)
                $isValueValid = false;
        }

        //Return the value if is valid...
        if($isValueValid == true){
            if(is_null($mySqliConnection) == true)     //<- Return the pure string value if not provided a MySQLi Connection
                return $valueToValidate;
            if(is_null($mySqliConnection) == false)    //<- Return the escaped string value if provided a MySQLi Connection!
                return mysqli_real_escape_string($mySqliConnection, $valueToValidate);
        }
        //If the value is not valid, return NULL
        if($isValueValid == false)
            return null;
    }

    /**
     * This methods validate a uploaded file present in "$_FILES". If the uploaded file is valid, it will be moved to the desired location and the PATH to it will be returned. If it is not valid, it is not moved anywhere and NULL is returned.
     */ 
    public static function ReceiveUploadedFileFromClientIfIsValid($uploadFileToValidate, string $destinationDirectoryIfValid, array $validationParameters){
        //If the destination directory is not a directory, cancel
        if(is_dir($destinationDirectoryIfValid) == false){
            echo("BackendInputValidator Error: " . "The destination directory informed to receive the uploaded file is not a valid directory!");
            return null;
        }

        //If the file to validate is null, cancel and return NULL
        if(is_null($uploadFileToValidate) == true)
            return null;

        //Get the validation parameters informed
        $parameters = get_object_vars((object) $validationParameters);
        //Prepare the validation parameters 
        $allowedExtensions = (array_key_exists("allowedExtensions", $parameters) == true) ? $parameters["allowedExtensions"] : "any";
        $mustBeRealImageFile = (array_key_exists("mustBeRealImageFile", $parameters) == true) ? $parameters["mustBeRealImageFile"] : false;
        $maxFileSizeInKb = (array_key_exists("maxFileSizeInKb", $parameters) == true) ? $parameters["maxFileSizeInKb"] : 0;

        //Prepare the response
        $isFileValid = true;

        //============================================ START THE UPLOADED FILE VALIDATION ============================================//
        //Get the uploaded file data
        $uploadedFileName = pathinfo($uploadFileToValidate["name"], PATHINFO_FILENAME);
        $uploadedFileExtension = strtolower(pathinfo($uploadFileToValidate["name"], PATHINFO_EXTENSION));
        $uploadedFileTempPath = $uploadFileToValidate["tmp_name"];
        $uploadedFileSize = $uploadFileToValidate["size"];

        //Check if have a allowed extension
        if($allowedExtensions != "any"){
            //If not have a extension, is invalid
            if(str_contains($uploadFileToValidate["name"], ".") == false)
                $isFileValid = false;

            //Prepare the array of extensions allowed
            $extensionsAllowed = explode(",", $allowedExtensions);
            //Check if the file have a allowed extension
            $theFileHaveAllowedExtension = false;
            for ($i = 0; $i < count($extensionsAllowed); $i++)
                if($uploadedFileExtension == strtolower($extensionsAllowed[$i]))
                    $theFileHaveAllowedExtension = true;
            //If the file don't have a allowed extension, is invalid
            if($theFileHaveAllowedExtension == false)
                $isFileValid = false;
        }

        //Check if is a image
        if($mustBeRealImageFile == true && getimagesize($uploadedFileTempPath) === false)
            $isFileValid = false;
        //Check if the file respect the max file size
        if($maxFileSizeInKb > 0 && ($uploadedFileSize / 1024) > $maxFileSizeInKb)
            $isFileValid = false;
        //============================================ END THE UPLOADED FILE VALIDATION ============================================//
    
        //If the file is valid, move the file to destination path and return the path
        if($isFileValid == true){
            //Build the final path for the file
            $finalPath = ($destinationDirectoryIfValid . "/" . $uploadedFileName . "." . $uploadedFileExtension);

            //Try to move the uploaded file to final path
            $isSuccessfully = move_uploaded_file($uploadedFileTempPath, $finalPath);

            //If error...
            if($isSuccessfully == false){
                echo("BackendInputValidator Error: " . "An error occurred while moving the validated uploaded file to the destination directory. Perhaps the destination directory provided is not valid, or is not an absolute path.");
                return null;
            }
            //If have success
            if($isSuccessfully == true)
                return $finalPath;
        }
        //If the file is not valid, return null
        if($isFileValid == false)
            return null;
    }
}

?>