DRY - Script generator framework for project setup and deployment support
========================

You are so bored with many repeated tasks whenever you setup new projects. Most of the tasks like:
+ Create project folder on the demo, staging server
+ Clone project source code from a specific template, but changing the configuration for the new project.
+ Setup and add appropriate developers into the repository...

Ok, you got my pains! That's the reason I want to develop this project.

1) Architecture of DRY
----------------------------------
DRY is composed by below components:

+ A script generator and execution (for now, we support bash script only).
You will write a script in twig template with a configuration on input constraints.
Btw, you can create a simple JSON file if you're lazy to input value on the console.

+ Some built in commands that you can inject to your script to setup bitbucket repository, users.

Dry is developed based on Symfony 2.5 to use the Console components and its advantages.
For now, we develop the current version to support script execution on Linux based server only.

2) Installing DRY
----------------------------------

To install DRY, just need to follow below steps:

+ Download/clone the code to your server
+ Go to the project folder, run: composer update to download all vendor dependencies.
+ Change app/config/parameters.yml for your settings.

What you need to change in this file are:
+ bitbucket username
+ bitbucket password
+ bitbucket repository account (this is the owner of the repository when you created).

Actually, you only need to change this if you have the need to use command to create BitBucket repository and send User invitation.

3) Some first concepts before using
----------------------------------

### Script
In Dry, you are able to create many complex script based on a specific template to do whatever you wants.
So, scripts are located in the folder: src/Dry/ConsoleBundle/Resources/scripts.

"Script" is just a folder which included:

+ config.json file: A JSON file that defines parameters that users need to input.
This file contains a list of fields with constraints in inputting.
+ a Twig template file for bash script. You can write simple/complex bash script at there,
but using the Twig template - you can easily replace params or the logic of the script based on your need.

There is a constraint when you create a script for now:
+ The Twig template script must have the same name as the script folder.

### How DRY runs

When running a specific "script", you need to stay at the root folder of the project and run the command:

    ./run.sh script-folder-name


Example:

    ./run.sh setup-project-on-staging

When you run this command, this console app will find fields that you defined in scripts/script-folder-name/config.json
to find fields to be input.

Then, it will ask user to input these fields on the command line which follows constraints that you defined for inputting.

There is another option to run the script without inputting params manually is:

    ./run.sh script-folder-name data-file-path

data-file-path is a JSON file which is in key-value format for params associated with fields in scripts/script-folder-name/config.json

After having all of input, DRY will generate a bash script based on the the script template at scripts/script-folder-name.
The output is put at the folder run-scripts/ with the name: script-folder-name.sh and run it.


4) HOW to use DRY?
----------------------------------
DRY is just a skeleton. Using DRY, you are freely to create complex script that allows you to do many flexible stuffs.

Imagine the case that you need to setup the project folder for the team on a staging server.
You just need to create a script following your manual steps and use additional logic in Twig to control the logic inside the bash script.
Also, it provides you the first step to ask for user params inputting without any coding.

As mentioned before, DRY also supports other commands to  create repository, sending user invitation in BitBucket.
Example: if you want to create a repository, just add below line to your script template:

    php app/console bitbucket:create-repos "project-name" "project description"

To ask the user for project-name, description - just need to change the config.json as below:

    {
        "fields": [
            {
                "title": "Project name (no space)",
                "name": "project_name",
                "required": "yes"
            },
            {
                "title": "Project description",
                "name": "project_description",
                "required": "yes"
            }
        ]
    }


Then, you can change the script to:

    php app/console bitbucket:create-repos "{{ project_name }}" "{{project_description}}"

and let DRY helps you to ask user for inputting these values.

We will extend the support commands in a near future.


### Add more auto script to the project

Please refer to the script setup-project-on-staging and follow that structure to create your new script if you need.

