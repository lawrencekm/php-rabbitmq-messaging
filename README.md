# PHP and RabbitMQ Integration for sending messages like SMS
PHP scripts for sending and receiving messages using RabbitMQ. 
RabbitMQ is an open-source message-broker software that originally implemented the Advanced Message Queuing Protocol and has since been extended with a plug-in architecture to support Streaming Text Oriented Messaging Protocol, MQ Telemetry Transport, and other protocols.
We will examine its use case in SMS messaging context where say, you need to send sms messages to your providers such as Sozuri, Twilio etc. You produce the sms from your application using logic in send.php and create receivers or consumers of these messages in the queue such as receive.php and receive2.php

RabbitMQ's scalability enables a bulk SMS Laravel application to efficiently handle the task of sending out large volumes of SMS messages by decoupling message generation from message sending and leveraging multiple producers and consumers. 
This architecture ensures high throughput, reliability, and scalability, making it ideal for handling massive SMS campaigns.

## 1. Requirements
PHP installed on your system.
RabbitMQ server running locally or accessible via network.
Composer installed for managing PHP dependencies.

## 2. Installation
Clone or download the repository containing the PHP scripts.
Install Composer dependencies by running composer install in the project directory.

## 3. Configuration
Ensure that RabbitMQ credentials (hostname, port, username, password) in both send.php and receive.php match your RabbitMQ server configuration.

## 4. Send Script (send.php)
The send.php script sends messages to a RabbitMQ queue named sms_queue.
Messages are sent as JSON data and can include various parameters like sdp_smses, raw_sms, and safcomPackageId.

To send a message, execute the script with appropriate parameters. For example:
```php send.php "Your Message Data"```

## 5. Receive Script (receive.php)
The receive.php script listens for messages from the sms_queue on RabbitMQ.
Upon receiving a message, it performs the following tasks:
//performs some expensive task, in this case we read a token from a file,insert data to sql database in chunks, and make a http request
Reads a token from a file named token.txt.
Parses the received message, extracts relevant data, and inserts it into a MySQL database table named sms.
Makes an HTTP request to a local endpoint with the extracted data.
To start receiving messages, run the script with PHP:
```php receive.php```

## 6. Execution
Start the RabbitMQ server if it's not already running.
Run the receive.php script to start listening for incoming messages.
Run the send.php script with appropriate message data to send messages to the queue.

## 7. Notes
Make sure RabbitMQ server is accessible and configured correctly.
Ensure proper error handling and logging mechanisms for production use.
Adjust the scripts as needed based on your specific requirements and environment.

If eg. you are using laravel, create:

Create Producers:
Queueing Jobs: When an API request is received, dispatch a job to queue the SMS message generation task. This job will be responsible for creating the SMS message and pushing it onto a RabbitMQ queue using Laravel's queue system.
Multiple Queues: Optionally, you can configure multiple queues in Laravel to handle different types of SMS messages or prioritize certain messages over others. Each API endpoint can dispatch jobs to different queues as needed.

Create Consumers:
Queue Workers: Set up Laravel queue workers to consume messages from RabbitMQ queues. These queue workers will act as consumers by retrieving messages from the queues and processing them.
Job Handlers: Create job handlers in Laravel to handle the processing of SMS messages. Each job handler should contain the logic for sending out the SMS message using an SMS provider like Twilio or Sozuri.
Scaling Workers: To handle large volumes of SMS messages, you can scale the number of queue workers horizontally. Laravel's queue system supports running multiple queue worker processes simultaneously to process messages concurrently.

## 8. To do
Implement this in Laravel by creating an application that has a form that accepts a csv with upto 1 million message details and recipients, queues them to rabbitmq before consuming them for database insert and http request to upstream provider.
