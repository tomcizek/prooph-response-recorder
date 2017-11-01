# tomcizek/prooph-response-recorder

[![Build Status](https://img.shields.io/travis/tomcizek/prooph-response-recorder.svg?style=flat-square)](https://travis-ci.org/tomcizek/prooph-response-recorder)
[![Quality Score](https://img.shields.io/scrutinizer/g/tomcizek/prooph-response-recorder.svg?style=flat-square)](https://scrutinizer-ci.com/g/tomcizek/prooph-response-recorder)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/tomcizek/prooph-response-recorder.svg?style=flat-square)](https://scrutinizer-ci.com/g/tomcizek/prooph-response-recorder)


This is "evil" tool for prooph toolbox. 

## Why bother then?
If you want to release minimum viable product and you dont want to deal with asynchronous stuff
on frontend, like optimistic UI and error handling, because you do not care your commands are processed
synchronously, you can hide your synchronous CQRS backend behind request/response interface.
 
Then you can have nice "proophful backend", well ready to make it asynchronous, but use it 
with simple or legacy request/response REST or WebApp controllers for frontend. Using this is 
considered as bad practice, because you loose all good thing prooph can offer to frontend. 

But you can use this and when you prove your business concept is good, you can make your frontend 
shiny and asynchronousful. 

# Quick start

## 1) Install this library through composer
`composer require tomcizek/prooph-response-recorder`

## Contribute

Please feel free to fork and extend existing or add new features and send a pull request with your changes! 
To establish a consistent code quality, please provide unit tests for all your changes and may adapt the documentation.
