# Setup Guide

#### Starting the API
- Go to the directory root.
- Rename **/.env.example** to **/.env** and change required settings.
- Run **composer update** command to install dependencies.
- Run **php artisan serve** command to start the server.

#### Endpoints to test
- GET **<ip address>/api/users/<user_id>/rewards?at=<date>** to show the list of this week's rewards.
ex - *http://127.0.0.1:8000/api/users/1/rewards?at=2022-05-23T12:00:00Z*

- PATCH **<ip address>/api/users/<user_id>/rewards/<date>/redeem** to redeem a reward.
ex - *http://127.0.0.1:8000/api/users/1/rewards/2022-05-24T00:00:00Z/redeem*