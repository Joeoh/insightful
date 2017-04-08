# Insightful
Web Application for analysing online review sentiment using Azure Cognitive Services API

![Insightful gathers and analyses online reviews about your business and display the results in an easy to digest format](demonstration.gif)

## Contributors

**Project Manager:** Joe O'Hara

| Back-End Team | Charting Team | Front-End Team  |
| ------------- |---------------| ----------------|
| GC            | JCZ           | RMM             |
| Joe O'Hara    | CK            | Rob Cooney      |

## Installation Instructions

This web application can be installed like any other Laravel web app.
The basic steps are:
- Set up a new virtual host and set the web root to /public in this repo.
- Setup up the required .htaccess or Nginx config
- Import the database included in the repo
- Copy `.env.example` to `.env` and fill in the required fields
- Install dependancies with `php composer install`
### Importing new reviews
To pull in the latest reviews for a business the scrapers must be called.
This can be done by setting up a Cron job for an appropriate time (daily) to run the command `php artisan scrape`
A cronjob must also be setup for parsing `php artisan parse`

### Requirements
- PHP 7
- Cron Job Access
- Database
