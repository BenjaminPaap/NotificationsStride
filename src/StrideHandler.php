<?php

namespace Bpa\Notifications\Handler;

use Bpa\Notifications\Notification\MessageInterface;
use GuzzleHttp\Client;

/**
 * Handler for Stride
 */
class StrideHandler implements HandlerInterface
{
    const URL = 'https://api.atlassian.com/site/{cloudId}/conversation/{roomId}/message';

    /**
     * @var string
     */
    private $cloudId;

    /**
     * @var string
     */
    private $token;

    /**
     * @var Client
     */
    private $client;

    /**
     * StrideHandler constructor.
     *
     * @param string $cloudId
     * @param string $token
     * @param Client $client
     */
    public function __construct($cloudId, $token, Client $client)
    {
        $this->cloudId = $cloudId;
        $this->token = $token;
        $this->client = $client;
    }

    /**
     * @param MessageInterface $message
     *
     * @return bool|void
     */
    public function notify(MessageInterface $message)
    {
        if (false === $message->getRoom() instanceof StrideRoom) {
            return false;
        }

        $body = json_encode($this->getContent($message));

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \Exception(json_last_error_msg());
        }

        $request = $this->client->request(
            'POST',
            strtr(self::URL, [
                '{cloudId}' => $this->cloudId,
                '{roomId}' => $message->getRoom()->getIdentifier(),
            ]),
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => strtr('Bearer {token}', [
                        '{token}' => $this->token,
                    ]),
                ],
                'body' => $body,
            ]
        );

        $response = $this->client->sendRequest($request);

        return true;
    }

    /**
     * @param MessageInterface $message
     */
    private function getContent(MessageInterface $message)
    {
        $data = [
            'body' => [
                'version' => 1,
                'type' => 'doc',
                'content' => [],
            ],
        ];

        if (null !== $message->getTitle()) {
            $data['body']['content'][] = [
                'type' => 'heading',
                'attrs' => [
                    'level' => 1,
                ],
                'content' => [
                    [
                        'type' => 'text',
                        'text' => $message->getTitle(),
                    ],
                ],
            ];
        }

        if (null !== $message->getMessage()) {
            $data['body']['content'][] = [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => $message->getMessage(),
                    ],
                ],
            ];
        }

        return $data;
    }
}
