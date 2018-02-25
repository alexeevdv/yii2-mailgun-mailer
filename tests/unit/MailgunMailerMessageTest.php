<?php

namespace tests\unit;

use alexeevdv\mailer\MailgunMailerMessage;
use yii\base\InvalidArgumentException;

/**
 * Class MailgunMailerMessageTest
 * @package tests\unit
 */
class MailgunMailerMessageTest extends \Codeception\Test\Unit
{
    /**
     * @var \tests\UnitTester
     */
    public $tester;

    /**
     * @test
     */
    public function setAndGetSubject()
    {
        $message = new MailgunMailerMessage([
            'subject' => 'Test subject',
        ]);
        $this->tester->assertEquals('Test subject', $message->getSubject());

        $result = $message->setSubject('Test2 subject');
        $this->tester->assertEquals('Test2 subject', $message->getSubject());
        $this->tester->assertInstanceOf(MailgunMailerMessage::class, $result);
    }

    /**
     * @test
     */
    public function setAndGetCharset()
    {
        $message = new MailgunMailerMessage;
        $result = $message->setCharset('utf-8');
        $this->tester->assertEquals('utf-8', $message->getCharset());
        $this->tester->assertInstanceOf(MailgunMailerMessage::class, $result);
    }

    /**
     * @dataProvider singleEmailProvider
     * @test
     */
    public function setAndGetFrom($email)
    {
        $message = new MailgunMailerMessage;
        $result = $message->setFrom($email);
        $this->tester->assertEquals($email, $message->getFrom());
        $this->tester->assertInstanceOf(MailgunMailerMessage::class, $result);
    }

    /**
     * @dataProvider multipleEmailProvider
     * @test
     */
    public function setAndGetTo($emails)
    {
        $message = new MailgunMailerMessage;
        $result = $message->setTo($emails);
        $this->tester->assertEquals($emails, $message->getTo());
        $this->tester->assertInstanceOf(MailgunMailerMessage::class, $result);
    }

    /**
     * @dataProvider singleEmailProvider
     * @test
     */
    public function setAndGetReplyTo($email)
    {
        $message = new MailgunMailerMessage;
        $result = $message->setReplyTo($email);
        $this->tester->assertEquals($email, $message->getReplyTo());
        $this->tester->assertInstanceOf(MailgunMailerMessage::class, $result);
    }

    /**
     * @dataProvider multipleEmailProvider
     * @test
     */
    public function setAndGetCc($emails)
    {
        $message = new MailgunMailerMessage;
        $result = $message->setCc($emails);
        $this->tester->assertEquals($emails, $message->getCc());
        $this->tester->assertInstanceOf(MailgunMailerMessage::class, $result);
    }

    /**
     * @dataProvider multipleEmailProvider
     * @test
     */
    public function setAndGetBcc($emails)
    {
        $message = new MailgunMailerMessage;
        $result = $message->setBcc($emails);
        $this->tester->assertEquals($emails, $message->getBcc());
        $this->tester->assertInstanceOf(MailgunMailerMessage::class, $result);
    }

    /**
     * @test
     */
    public function setHtmlBody()
    {
        $message = new MailgunMailerMessage;
        $result = $message->setHtmlBody('HTML');
        $this->tester->assertInstanceOf(MailgunMailerMessage::class, $result);
    }

    /**
     * @test
     */
    public function setTextBody()
    {
        $message = new MailgunMailerMessage;
        $result = $message->setTextBody('TEXT');
        $this->tester->assertInstanceOf(MailgunMailerMessage::class, $result);
    }

    /**
     * @test
     */
    public function testToString()
    {
        $message = new MailgunMailerMessage;
        $string = $message->toString();
        $this->tester->assertNotEmpty($string);
    }

    /**
     * @test
     */
    public function attachContent()
    {
        $message = new MailgunMailerMessage;

        $this->tester->expectException(InvalidArgumentException::class, function () use ($message) {
            $message->attachContent('content');
        });

        $result = $message->attachContent('content', ['fileName' => 'myCustomFile']);
        $this->tester->assertInstanceOf(MailgunMailerMessage::class, $result);
    }

    /**
     * @test
     */
    public function embedContent()
    {
        $message = new MailgunMailerMessage;

        $this->tester->expectException(InvalidArgumentException::class, function () use ($message) {
            $message->embedContent('content');
        });

        $result = $message->embedContent('content', ['fileName' => 'myEmbededFile']);
        $this->tester->assertStringStartsWith('mailgun', $result);
    }

    /**
     * @return array
     */
    public function multipleEmailProvider()
    {
        return [
            [['admin@example.org' => 'John Doe']],
            ['admin@example.org'],
            [[
                'admin@example.org' => 'John Doe',
                'admin2@example.org',
            ]],
        ];
    }

    /**
     * @return array
     */
    public function singleEmailProvider()
    {
        return [
            [['admin@example.org' => 'John Doe']],
            ['admin@example.org'],
        ];
    }
}
