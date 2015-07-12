# resume-generator
This is a simple web application, which can generate a resume (in docx format) by filling in the online form.
This application has been uploaded to Heroku as a php app.
http://resume-generator.herokuapp.com/

## How to run the app in your machine
The php files must be run under a web server (e.g. Apache) that can render php files, typically in htdocs/ or www/ directory. Then go to http://localhost/web/cv_generator (assume your web server listens to port 80).

## Directories
For the purpose code review, not all lines of codes need to be read. You might simply look at the following files:
- web/index.php
  - entrance point of the web page
- web/cv_generator/demo_ms_word.php
  - backend logic to render the
- web/cv_generator/demo_ms_word.docx
- web/config/cv_submit_config.php - configuration file

## Appealing Feature
- Validation form
- Dynamic fields
- Library to generate word document
- Highly configurable using

## Room for improvement
- Use MVC for better arhitecture (e.g. Laravel, Codeigniter)
- Use frontend framework (e.g. Angularjs), and with a better template engine
- Break down the code into modules
