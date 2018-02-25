<?php

namespace tests\unit;

use alexeevdv\mailer\MailgunMailer;
use alexeevdv\mailer\MailgunMailerMessage;
use Codeception\Stub;
use Mailgun\Exception\HttpClientException;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Class MailgunMailerTest
 * @package tests\unit
 */
class MailgunMailerTest extends \Codeception\Test\Unit
{
    /**
     * @var \tests\UnitTester
     */
    public $tester;

    /**
     * @test
     */
    public function testInit()
    {
        $this->tester->expectException(InvalidConfigException::class, function () {
            new MailgunMailer;
        });
        $this->tester->expectException(InvalidConfigException::class, function () {
            new MailgunMailer(['apiKey' => '123']);
        });
        $this->tester->expectException(InvalidConfigException::class, function () {
            new MailgunMailer(['domain' => 'xxx.com']);
        });
        new MailgunMailer(['apiKey' => '123', 'domain' => 'xxx.com']);
    }

    /**
     * @test
     */
    public function testSuccessfulSend()
    {
        Yii::$container->set(\Mailgun\Mailgun::class, function () {
            return Stub::make(\Mailgun\Mailgun::class, [
                'messages' => function () {
                    return Stub::make(\Mailgun\Api\Message::class, [
                        'send' => function () {
                            return \Mailgun\Model\Message\SendResponse::create([
                                'id' => 123,
                                'message' => 'OK',
                            ]);
                        },
                    ]);
                },
            ]);
        });
        $message = new MailgunMailerMessage([
            'to' => 'admin@example.org',
        ]);
        $mailer = new MailgunMailer([
            'apiKey' => '123',
            'domain' => 'xxx.com',
            'useFileTransport' => false,
        ]);
        $result = $mailer->send($message);
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function testFailedSend()
    {
        Yii::$container->set(\Mailgun\Mailgun::class, function () {
            return Stub::make(\Mailgun\Mailgun::class, [
                'messages' => function () {
                    return Stub::make(\Mailgun\Api\Message::class, [
                        'send' => function () {
                            throw new HttpClientException('Smth wrong!', 500);
                        },
                    ]);
                },
            ]);
        });
        $message = new MailgunMailerMessage([
            'to' => 'admin@example.org',
        ]);
        $mailer = new MailgunMailer([
            'apiKey' => '123',
            'domain' => 'xxx.com',
            'useFileTransport' => false,
        ]);
        $result = $mailer->send($message);
        $this->assertFalse($result);
    }
}
