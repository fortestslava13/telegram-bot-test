# Telegram Bot Laravel Application

This is a Laravel application that integrates with the Telegram Bot API to handle webhook events and send notifications.

## Prerequisites

Before you begin, ensure you have the following installed on your system:
- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)
- A Telegram Bot Token (create one via [@BotFather](https://t.me/botfather))
- An Ngrok Authentication Token (sign up at [Ngrok](https://ngrok.com/) and get your token from the dashboard)

## Running the Application with Docker

### 1. Clone the Repository

```bash
git clone https://github.com/fortestslava13/telegram-bot-test.git
cd telegram-bot-test
```

### 2. Environment Configuration

Create a `.env` file by copying the example:

```bash
cp .env.example .env
```

Update the following variables in your `.env` file:

```
# Telegram Bot Configuration
TELEGRAM_BOT_TOKEN=your_telegram_bot_token
TELEGRAM_BOT_WEBHOOK_SECRET=your_webhook_secret
```

Also, update the NGROK_AUTHTOKEN in the docker-compose.yaml file with your Ngrok authentication token:

```yaml
  ngrok:
    image: ngrok/ngrok:latest
    command: "http nginx:80"
    environment:
        NGROK_AUTHTOKEN: "your_ngrok_auth_token"
```

### 3. Start the Docker Containers

```bash
docker-compose up -d
```

This will start the following services:
- **app**: PHP application with Laravel
- **nginx**: Web server
- **mysql**: Database
- **ngrok**: Exposes your local server to the internet (for Telegram webhooks)

### 4. Install Dependencies and Run Migrations

```bash
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
```

### 5. Set Up Telegram Webhook

Set up the webhook:
```bash
docker-compose exec app php artisan set-telegram-webhook
```

Or manually set it using the TelegramService:

Get your ngrok URL:
1. Open http://localhost:4040 in your browser
2. Copy the HTTPS URL displayed (e.g., https://xxxx-xxx-xxx-xx-xx.ngrok.io)

```bash
docker-compose exec app php artisan tinker
```

```php
app(App\Services\TelegramService::class)->setWebhook('{ngrok_url}/api/telegram/webhook');
```

### 6. Testing the Application

To run the tests:

```bash
docker-compose exec app php artisan test
```

## Available Commands

The Telegram bot supports the following commands:
- `/start` - Subscribe to notifications
- `/stop` - Unsubscribe from notifications

## API Documentation

The application includes Swagger/OpenAPI documentation for the API endpoints. To access the documentation:

1. Start the application with Docker:
```bash
docker-compose up -d
```

2. Access the Swagger UI at: http://localhost:8080/api/documentation

The documentation provides detailed information about:
- Available endpoints
- Request parameters and formats
- Response formats
- Authentication requirements

You can test the API directly from the Swagger UI interface.

## Queue Worker

The application uses Laravel's queue system for processing notifications. The queue worker is automatically started by Supervisor in the Docker container.

## Troubleshooting

### Database Connection Issues

If you encounter database connection issues, ensure the MySQL container is running:

```bash
docker-compose ps
```

You can check the MySQL logs:

```bash
docker-compose logs mysql
```

### Webhook Not Working

1. Verify your ngrok URL is correct and accessible
2. Check the Laravel logs:

```bash
docker-compose exec app tail -f storage/logs/laravel.log
```

3. Ensure your Telegram Bot Token is correct
4. Verify the webhook is set correctly:

```bash
curl -X GET https://api.telegram.org/bot{your_token}/getWebhookInfo
```

### Queue Not Processing

Check the worker logs:

```bash
docker-compose exec app tail -f storage/logs/worker.log
```
