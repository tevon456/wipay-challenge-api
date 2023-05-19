<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Getting started

Requires [PHP](https://www.php.net/manual/en/install.php) and [Composer](https://getcomposer.org/)

1. Clone this repo to a local directory `git clone https://github.com/tevon456/wipay-challenge-api.git`
2. Change directory to project `cd wipay-challenge-api`
3. Run `composer install `
4. Create database file `touch database/database.sqlite`
5. Copy example env `cp .env.example .env`
6. Generate Laravel APP_KEY `php artisan key:generate`
7. Seed database with example data and admin `php artisan db:seed`
8. Run migrations `php artisan migrate`
9. Serve project locally `php artisan serve`

## Endpoints

I recommend using [Postman](https://www.postman.com/) to interact with the api.

### Auth

-   URL: `/api/auth/register`
-   Method: `POST`
-   Description: Register a customer.
-   Payload:

```json
{
    "phone_number": "1234567890",
    "address_line_1": "123 Main Street",
    "address_line_2": null,
    "city": "New York",
    "parish": "Manhattan",
    "name": "John Doe",
    "email": "johndoe@example.com",
    "password": "secretpassword"
}
```

---

-   URL: `/api/auth/login`
-   Method: `POST`
-   Description: Returns bearer token to auth request.
-   Payload:

```json
{
    "email": "johndoe@example.com",
    "password": "secretpassword"
}
```

---

-   URL: `/api/auth/logout`
-   Method: `POST`
-   Description: Revoke bearer auth token used to make request.
-   Payload: N/A

---

-   URL: `/api/user`
-   Method: `GET`
-   Description: Return authenticated user requires bearer token.
-   Payload:

```json
{
    "name": "John Doe",
    "email": "johndoe@example.com",
    "password": "secretpassword"
}
```

---

### Book

-   URL: `/api/book?per_page=10`
-   Method: `GET`
-   Description: Return paginated list of books accepts per_page query param for pagination.
-   Payload: N/A

---

-   URL: `/api/book/{id}`
-   Method: `GET`
-   Description: Return a single bookk by id.
-   Payload:N/A

---

-   URL: `/api/book/search?query={string}&per_page={integer}`
-   Method: `GET`
-   Description: Return paginated list of books accepts per_page query param for pagination and query param for search query.
-   Payload: N/A

---

-   URL: `/api/book/purchase`
-   Method: `POST`
-   Description: Initiate payment request returns wipay url for selecting ard type and entering card details.
-   Payload:

```json
{
    "book_id": 1,
    "quantity": 2
}
```

---

-   URL: `/api/book/purchase/callback`
-   Method: `GET`
-   Description: Wipay callback url with query param response.
-   Payload: N/A

---

-   URL: `/api/book/`
-   Method: `POST`
-   Description: Post a new book entry, admin.
-   Payload:

```json
{
    "author_name": "John Smith",
    "title": "Sample Book",
    "isbn": "9781234567890",
    "price": 19.99,
    "inventory_count": 100
}
```

---

-   URL: `/api/book/{id}`
-   Method: `PUT`
-   Description: Update an existing book by id, admin.
-   Payload:

```json
{
    "author_name": "John Smith",
    "title": "Sample Book",
    "isbn": "9781234567890",
    "price": 19.99,
    "inventory_count": 102
}
```

---

-   URL: `/api/book/{id}`
-   Method: `DELETE`
-   Description: Delete a book by id, admin.
-   Payload: N/A

---

-   URL: `/api/book/sales?filter={success|failed|pending}&per_page={integer}`
-   Method: `GET`
-   Description: Returns a paginated list of book sales an existing customer by id, admin.
-   Payload: N/A

---

### Customer

-   URL: `/api/customer?per_page=10`
-   Method: `GET`
-   Description: Return paginated list of customers, admin.
-   Payload: N/A

---

-   URL: `/api/customer/{id}`
-   Method: `GET`
-   Description: Returns an existing book by id.
-   Payload: N/A

---

-   URL: `/api/customer/{id}`
-   Method: `PUT`
-   Description: Update an existing customer by id.
-   Payload:

```json
{
    "phone_number": "1234567890",
    "address_line_1": "123 Main Street",
    "address_line_2": null,
    "city": "Montego Bay",
    "parish": "St. James",
    "name": "John Doe",
    "email": "johndoe@example.com"
}
```

---

-   URL: `/api/customer/{id}`
-   Method: `DELETE`
-   Description: Delete an existing customer by id, admin.
-   Payload: N/A

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

-   [Simple, fast routing engine](https://laravel.com/docs/routing).
-   [Powerful dependency injection container](https://laravel.com/docs/container).
-   Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
-   Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
-   Database agnostic [schema migrations](https://laravel.com/docs/migrations).
-   [Robust background job processing](https://laravel.com/docs/queues).
-   [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

-   **[Vehikl](https://vehikl.com/)**
-   **[Tighten Co.](https://tighten.co)**
-   **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
-   **[64 Robots](https://64robots.com)**
-   **[Cubet Techno Labs](https://cubettech.com)**
-   **[Cyber-Duck](https://cyber-duck.co.uk)**
-   **[Many](https://www.many.co.uk)**
-   **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
-   **[DevSquad](https://devsquad.com)**
-   **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
-   **[OP.GG](https://op.gg)**
-   **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
-   **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
