# WEB-APPLICATION-TO-CONVERT-YOUTUBE-CAPTIONS-INTO-SIGN-LANGUAGE

This basic idea of this project is that it converts the english text to sign language in the form of finger spellings and animation. It is using the JASigning library which is based on webgl and uses sigml files for animating the character.

The use of the above idea is shown by using the youtube where the input is the subtiles of the video and the output is sign language.The subttile of the given video is extrated by the video id by passing it to the youtube API

##################################################################

Instruction for Deployment

 
1.Put all the files on the  server/localhost and import the database file in the MySql database.
 
2.Either download the JASigning library package and keep it on the server/localhost or else fetched the required file from using their link of Javascript file and css file.


Important notes:

*To use it first you have to make an account on google developer console and enable the youtube api v3 and set the "redirect path" and "credential.json" in the new.php.

*Also if you are viewing the unzipped version then first unzip the "vendor.zip" and "lemmatizer.zip" in the newapi folder.
