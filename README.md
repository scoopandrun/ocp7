# BileMo

[![SymfonyInsight](https://insight.symfony.com/projects/adf07793-246b-40dc-bd38-7a2c3b8e3b1f/big.svg)](https://insight.symfony.com/projects/adf07793-246b-40dc-bd38-7a2c3b8e3b1f)

This project is an API service exercise for the PHP course at OpenClassrooms.  
It is not meant to be used in production nor is it meant to be a showcase.

This repository is primarily a way of sharing code with the tutor.

## Installation

This project uses [Composer](https://getcomposer.org) with PHP `>= 7.4`.

Configure your database and email server in a `.env.local` file at the root of the project.  
Copy a `DATABASE_URL` line from the `.env` file and modify it to fit your configuration.

Clone and install the project.

```shell
# Clone the repository
git clone https://github.com/scoopandrun/ocp7
cd ocp7

# Install the dependencies
composer install

# Create your database
# Don't forget to configure 'DATABASE_URL' in your .env.local file with your local database information
php bin/console doctrine:database:create

# Execute the migrations
php bin/console doctrine:migrations:migrate

# (Recommended) Load the fixtures to get a starting data set.
# You can update the initial users information in the User data fixture.
php bin/console doctrine:fixtures:load
```

## Documentation

Once the project is installed, the API documentation is available at /api/doc (HTML) or /api/doc.json (JSON).
