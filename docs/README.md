# Notes

Notes for installing the theme

## Pre-requisites

Ensure the following are installed:

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

