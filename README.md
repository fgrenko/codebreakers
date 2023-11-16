Setup instructions:
1. `composer install`
2. `docker compose up -d` for creating the database
3. Create `.env.local` where you will provide the updated DATABASE_URL if the database container
port is different than `3306`
4. `symfony console doctrine:migrations:migrate`
5. `symfony serve -d` for starting up the project
6. `yarn install` and `yarn build`
7. `symfony console app:add-categories` for adding some categories
8. Access the site at [http://127.0.0.1:8000/](http://127.0.0.1:8000/)


Unfortunately I hadn't managed to set up a Swagger endpoint due to some errors.
But JSON documentation of Api Platform can be generated with the following command:
`symfony console api:openapi:export`

\
\
Hope you'll enjoy the site. \
Fran :) 
