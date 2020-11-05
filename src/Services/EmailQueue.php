<?php

namespace Src\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Src\Repositories\EmailRepository;

class EmailQueue
{
    private $connection;

    private $queue_name;

    public function __construct()
    {
        $host       = $_ENV['RABBITMQ_HOST'];
        $port       = $_ENV['RABBITMQ_PORT'];
        $user       = $_ENV['RABBITMQ_USERNAME'];
        $pass       = $_ENV['RABBITMQ_PASSWORD'];

        $this->queue_name = $_ENV['RABBITMQ_QUEUE_NAME'];
        $this->connection = new AMQPStreamConnection($host, $port, $user, $pass);
    }

    public function send($data)
    {

        $channel = $this->connection->channel();

        $channel->queue_declare($this->queue_name, false, true, false, false);

        $message = new AMQPMessage(
            $data,
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );

        $channel->basic_publish($message, '', $this->queue_name);
        
        $channel->close();
        $this->connection->close();
    }

    public function consume()
    {
        $channel = $this->connection->channel();

        $channel->queue_declare($this->queue_name, false, true, false, false);

        echo " [*] Waiting for messages. To exit press CTRL+C\n";

        $callback = function ($message) {
            echo ' [x] Received ', $message->body, "\n";

            $data = json_decode($message->body, true);

            $this->sendAndSave($data);

            sleep(substr_count($message->body, '.'));

            echo " [x] Done\n";
            $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
        };

        $channel->basic_qos(null, 1, null);
        $channel->basic_consume($this->queue_name, '', false, false, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $this->connection->close();
    }

    private function sendAndSave($data)
    {
        $mail = new PHPMailer(true);

        $mail->From = $_ENV['FROM_EMAIL'];
        $mail->FromName = "Resi Dwi Thawasa";

        $mail->addAddress($data['email']);

        //Send HTML or Plain Text email
        $mail->isHTML(true);

        $mail->Subject = $data['emailSubject'];
        $mail->Body = $data['emailBody'];

        try {
            $mail->send();
            $emailRepository = new EmailRepository();
            $emailRepository->insert($data);

            echo " [x] Email has been sent successfully\n";
        } catch (Exception $e) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        }
    }
}
