<?php

namespace alexeevdv\mailer;

use Mailgun\Messages\MessageBuilder;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\mail\BaseMessage;

/**
 * Class MailgunMailerMessage
 * @package alexeevdv\mailer
 */
class MailgunMailerMessage extends BaseMessage
{
    /**
     * @var string
     */
    private $_charset;

    /**
     * @var MessageBuilder|array|string
     */
    public $messageBuilder = [
        'class' => MessageBuilder::class,
    ];

    /**
     * @return MessageBuilder
     * @throws InvalidConfigException
     */
    public function getMessageBuilder()
    {
        $this->messageBuilder = Instance::ensure($this->messageBuilder, MessageBuilder::class);
        return $this->messageBuilder;
    }

    /**
     * @inheritdoc
     */
    public function getCharset()
    {
        return $this->_charset;
    }

    /**
     * @inheritdoc
     */
    public function setCharset($charset)
    {
        $this->_charset = $charset;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFrom()
    {
        $from = $this->getMessagePart('from');
        return $this->extractEmail(reset($from));
    }

    /**
     * @inheritdoc
     */
    public function setFrom($from)
    {
        $this->unsetMessagePart('from');
        if (is_array($from)) {
            $this->getMessageBuilder()->setFromAddress(key($from), ['full_name' => current($from)]);
            return $this;
        }

        $this->getMessageBuilder()->setFromAddress($from);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTo()
    {
        $to = $this->getMessagePart('to');
        return $this->extractMultipleEmails($to);
    }

    /**
     * @inheritdoc
     */
    public function setTo($to)
    {
        $this->unsetMessagePart('to');
        foreach ((array) $to as $key => $value) {
            if (is_numeric($key)) {
                $this->getMessageBuilder()->addToRecipient($value);
            } else {
                $this->getMessageBuilder()->addToRecipient($key, ['full_name' => $value]);
            }
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getReplyTo()
    {
        $to = $this->getMessagePart('h:reply-to');
        return $this->extractEmail($to);
    }

    /**
     * @inheritdoc
     */
    public function setReplyTo($replyTo)
    {
        $this->unsetMessagePart('h:reply-to');
        if (is_array($replyTo)) {
            $this->getMessageBuilder()->setReplyToAddress(key($replyTo), ['full_name' => current($replyTo)]);
            return $this;
        }

        $this->getMessageBuilder()->setReplyToAddress($replyTo);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCc()
    {
        $to = $this->getMessagePart('cc');
        return $this->extractMultipleEmails($to);
    }

    /**
     * @inheritdoc
     */
    public function setCc($cc)
    {
        $this->unsetMessagePart('cc');
        foreach ((array)$cc as $key => $value) {
            if (!is_numeric($key)) {
                $this->getMessageBuilder()->addCcRecipient($key, ['full_name' => $value]);
            } else {
                $this->getMessageBuilder()->addCcRecipient($value);
            }
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBcc()
    {
        $to = $this->getMessagePart('bcc');
        return $this->extractMultipleEmails($to);
    }

    /**
     * @inheritdoc
     */
    public function setBcc($bcc)
    {
        $this->unsetMessagePart('bcc');
        foreach ((array)$bcc as $key => $value) {
            if (!is_numeric($key)) {
                $this->getMessageBuilder()->addBccRecipient($key, ['full_name' => $value]);
            } else {
                $this->getMessageBuilder()->addBccRecipient($value);
            }
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSubject()
    {
        return $this->getMessagePart('subject');
    }

    /**
     * @inheritdoc
     */
    public function setSubject($subject)
    {
        $this->unsetMessagePart('subject');
        $this->getMessageBuilder()->setSubject($subject);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setTextBody($text)
    {
        $this->getMessageBuilder()->setTextBody($text);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setHtmlBody($html)
    {
        $this->getMessageBuilder()->setHtmlBody($html);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function attach($fileName, array $options = [])
    {
        $this->getMessageBuilder()->addAttachment($fileName, ArrayHelper::getValue($options, 'fileName'));
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function attachContent($content, array $options = [])
    {
        $fileName = ArrayHelper::getValue($options, 'fileName');
        if ($fileName === null) {
            throw new InvalidArgumentException('`fileName` name is required.');
        }

        $tempPath = tempnam(sys_get_temp_dir(), 'mailgun');
        file_put_contents($tempPath, $content);
        return $this->attach($tempPath, $options);
    }

    /**
     * @inheritdoc
     */
    public function embed($fileName, array $options = [])
    {
        $this->getMessageBuilder()->addInlineImage('@' . $fileName, ArrayHelper::getValue($options, 'fileName'));
        return basename($fileName);
    }

    /**
     * @inheritdoc
     */
    public function embedContent($content, array $options = [])
    {
        $fileName = ArrayHelper::getValue($options, 'fileName');
        if ($fileName === null) {
            throw new InvalidArgumentException('`fileName` name is required.');
        }
        $tempPath = tempnam(sys_get_temp_dir(), 'mailgun');
        file_put_contents($tempPath, $content);
        return $this->embed($tempPath, $options);
    }

    /**
     * @inheritdoc
     */
    public function toString()
    {
        // TODO not the best way. But it is better than nothing
        return Json::encode($this->getMessageBuilder()->getMessage());
    }

    /**
     * @param string $part
     * @return mixed
     */
    protected function getMessagePart($part)
    {
        $message = $this->getMessageBuilder()->getMessage();
        return ArrayHelper::getValue($message, $part);
    }

    /**
     * @param string $part
     */
    protected function unsetMessagePart($part)
    {
        $message = $this->getMessageBuilder()->getMessage();
        if (isset($message[$part])) {
            unset($message[$part]);
        }
        $this->getMessageBuilder()->setMessage($message);
    }

    /**
     * @param string $string
     * @return array|string
     */
    protected function extractEmail($string)
    {
        if (strpos($string, '<') === false) {
            return $string;
        }

        list ($name, $email) = explode('" <', $string, 2);
        return [substr($email, 0, -1) => substr($name, 1)];
    }

    /**
     * @param array $strings
     * @return array|string
     */
    protected function extractMultipleEmails(array $strings)
    {
        if (count($strings) === 1) {
            return $this->extractEmail(reset($strings));
        }

        $emails = [];
        foreach ($strings as $string) {
            $email = $this->extractEmail($string);
            if (is_array($email)) {
                $emails[key($email)] = current($email);
            } else {
                $emails[] = $email;
            }
        }

        return $emails;
    }
}
