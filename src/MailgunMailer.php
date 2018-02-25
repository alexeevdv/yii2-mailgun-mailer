<?php

namespace alexeevdv\mailer;

use Mailgun\Exception\HttpClientException;
use Mailgun\HttpClientConfigurator;
use Mailgun\Mailgun;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\log\Logger;
use yii\mail\BaseMailer;

/**
 * Class MailgunMailer
 * @package alexeevdv\mailer
 */
class MailgunMailer extends BaseMailer
{
    /**
     * @inheritdoc
     */
    public $messageClass = MailgunMailerMessage::class;

    /**
     * @var string
     */
    public $apiKey;

    /**
     * @var string
     */
    public $domain;

    /**
     * @var Mailgun|array|string
     */
    public $client = [
        'class' => Mailgun::class,
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->apiKey === null) {
            throw new InvalidConfigException('`apiKey` is required.');
        }
        if ($this->domain === null) {
            throw new InvalidConfigException('`domain` is required.');
        }
        parent::init();
    }

    /**
     * @param MailgunMailerMessage $message
     * @inheritdoc
     */
    protected function sendMessage($message)
    {
        $client = $this->getClient();
        try {
            $response = $this->getClient()
                ->messages()
                ->send($this->domain, $message->getMessageBuilder()->getMessage());
        } catch (HttpClientException $e) {
            Yii::error($e->getMessage(), __METHOD__);
            return false;
        }
        return !!$response->getId();
    }

    /**
     * @return array|Mailgun|string
     * @throws InvalidConfigException
     */
    public function getClient()
    {
        if (is_array($this->client)) {
            $httpClientConfigurator = Yii::createObject(HttpClientConfigurator::class);
            $httpClientConfigurator->setApiKey($this->apiKey);
            $this->client = Yii::createObject(Mailgun::class, [$this->apiKey, $httpClientConfigurator->createConfiguredClient()]);
        }
        $this->client = Instance::ensure($this->client, Mailgun::class);
        return $this->client;
    }
}
