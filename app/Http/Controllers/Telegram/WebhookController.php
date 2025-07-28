<?php

namespace App\Http\Controllers\Telegram;

use App\Dto\Telegram\CommandDto;
use App\Http\Requests\Telegram\WebhookRequest;
use App\Services\TelegramService;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Telegram Bot API",
 *     version="1.0.0",
 *     description="API for Telegram Bot webhook integration",
 *     @OA\Contact(
 *         email="admin@example.com"
 *     )
 * )
 */
class WebhookController
{
    /**
     * Handle Telegram webhook request
     *
     * @OA\Post(
     *     path="/api/telegram/webhook",
     *     summary="Process incoming Telegram webhook",
     *     description="Handles incoming webhook requests from Telegram API",
     *     operationId="handleWebhook",
     *     tags={"Telegram"},
     *     @OA\Parameter(
     *         name="X-Telegram-Bot-Api-Secret-Token",
     *         in="header",
     *         required=true,
     *         description="Telegram webhook secret token for verification",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Telegram webhook payload",
     *         @OA\JsonContent(
     *             required={"message"},
     *             @OA\Property(
     *                 property="message",
     *                 type="object",
     *                 @OA\Property(
     *                     property="chat",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=123456789, description="Telegram chat ID"),
     *                     @OA\Property(property="username", type="string", example="username", description="Telegram username")
     *                 ),
     *                 @OA\Property(property="text", type="string", example="/start", description="Command text")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Webhook processed successfully"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized - Invalid or missing webhook secret token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="This action is unauthorized.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="message.chat.id", type="array", @OA\Items(type="string", example="The message.chat.id field is required.")),
     *                 @OA\Property(property="message.chat.username", type="array", @OA\Items(type="string", example="The message.chat.username field is required.")),
     *                 @OA\Property(property="message.text", type="array", @OA\Items(type="string", example="The message.text must be one of: /start, /stop."))
     *             )
     *         )
     *     )
     * )
     *
     * @param WebhookRequest $request
     * @param TelegramService $service
     * @return \Illuminate\Http\Response
     */
    public function __invoke(WebhookRequest $request, TelegramService $service)
    {
        $service->handleWebhook(new CommandDto(
            telegramId: $request->validated('message.chat.id'),
            username:  $request->input('message.chat.username'),
            command: $request->input('message.text')
        ));

        return \response()->noContent();
    }
}
