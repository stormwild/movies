# Notes

Notes for installing the theme

## Pre-requisites

Ensure the following are installed:

- WordPress 4.7
- git
- php 7.1
- Composer
- node 8.2.1
- yarn

## Installation

Clone the Movie theme into your local `wp-content/themes` folder

```bash
cd wp-content/themes
git clone git@github.com:stormwild/movies.git 
```

Run the following commands:

```
composer install
yarn && yarn build
```

## Sage 9 Docs

Sage 9 [docs](https://github.com/roots/docs/tree/sage-9/sage) are a work-in-progress

Also check Sober [docs](https://github.com/soberwp/controller#usage)

## Register Cron

Create cron job.

```
0 0 * * * wget https://wordpress-stormwild.c9users.io/wp-cron.php?doing_wp_cron >/dev/null 2>&1
```

## Cron

Cron script still in progress.

Using Guzzle to request json.

Will populate using `wp_insert_post`.

## References

Cron - See [cron.md](docs/cron.md)
