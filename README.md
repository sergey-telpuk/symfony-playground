[![Tests](https://github.com/sergey-telpuk/symfony-playground/actions/workflows/ci.yml/badge.svg)](https://github.com/sergey-telpuk/symfony-playground/actions/workflows/ci.yml)

Symfony Playground project:

---

# Symfony Playground

Welcome to the **Symfony Playground**, a vibrant environment for exploring and experimenting with Symfony frameworks!

## Getting Started

Follow the steps below to get your Symfony Playground up and running quickly.

### Prerequisites

Make sure you have the following installed:

- Docker
- Docker compose
- Make

### Installation

Clone the repository and navigate into the project directory:

```bash
git clone https://github.com/sergey-telpuk/symfony-playground.git
cd symfony-playground
```

Run the application:

```bash
make start
```

### Accessing the Application

Once the server is running, you can access the application by opening your web browser and navigating to:

[http://localhost/games](http://localhost/games)

### Login Credentials

You can log in using the following credentials:

- **Email:** test@test.com
- **Password:** 123

## Testing

To ensure everything is functioning as expected, run the following commands:

### Run Tests

```bash
make test
```

### Test Coverage

To check the test coverage, execute:

```bash
make test_coverage
```

## License

This project is licensed under the [MIT License](LICENSE).
