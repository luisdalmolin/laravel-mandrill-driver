<?php

namespace LaravelMandrill\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use LaravelMandrill\MandrillTransportException;
use MailchimpTransactional\ApiClient;
use Orchestra\Testbench\TestCase;

class MandrillTransportTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            'LaravelMandrill\MandrillServiceProvider',
        ];
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('mail.driver', 'mandrill');
        $app['config']->set('services.mandrill.headers', [
            'X-MC-Subaccount' => 'hello_world'
        ]);
    }

    /**
     * Check Mandrill message id is returned.
     * 
     */
    public function testMessageIdIsReturned()
    {
        // tracks activity in the Mock
        $history = [];

        // Mock Mandrill API as being successful.
        $mock = new MockHandler([
            new Response(200, 
                ['content-type' => 'application/json'], 
                json_encode([
                    [
                        "email" => "testemail@example.com",
                        "status" => "queued",
                        "_id" => "111111111111111"
                    ]
                ])
            )
        ]);
        $this->mockMandrillAPiResponces($mock, $history);

        // Setup test email.
        $testMail = $this->getMailable();

        // Ensure event contains expected data.
        Event::listen(MessageSent::class, function($event)
        {
            // Check Mandrill _id was passed back
            $this->assertEquals($event->sent->getMessageId(), "111111111111111");
            $this->assertEquals($event->message->getHeaders()->get('X-Message-ID')->getValue(), "111111111111111");

            // Check correct from email.
            $this->assertEquals($event->message->getFrom()[0]->getAddress(), "mandrill@test.com");
            // Check correct to email.
            $this->assertEquals($event->message->getTo()[0]->getAddress(), "testemail@example.com");
        });

        // Trigger event
        Mail::to('testemail@example.com')->send($testMail);

        // Ensure data all got posted to expected locations
        $this->assertEquals($history[0]['request']->getMethod(), 'POST');
        $this->assertEquals($history[0]['request']->getRequestTarget(), '/api/1.0/messages/send-raw');
        $this->assertCount(1, $history);
    }

    public function testHeadersAreSent()
    {
        // tracks activity in the Mock
        $history = [];

        // Mock Mandrill API as being successful.
        $mock = new MockHandler([
            new Response(200, 
                ['content-type' => 'application/json'], 
                json_encode([
                    [
                        "email" => "testemail@example.com",
                        "status" => "queued",
                        "_id" => "111111111111111"
                    ]
                ])
            )
        ]);
        $this->mockMandrillAPiResponces($mock, $history);

        $testMail = $this->getMailable();

        // Trigger event
        Mail::to('testemail@example.com')->send($testMail);
        
        $payload = urldecode($history[0]['request']->getBody()->getContents());

        // Ensure headers are set.
        $this->assertStringContainsString("X-MC-Subaccount: hello_world", $payload);
        $this->assertStringContainsString("X-Dump: dumpy", $payload);
        $this->assertStringContainsString("Subject: Testing things", $payload);
    }

    public function testThrowsException()
    {
        // tracks activity in the Mock
        $history = [];

        // Mock Mandrill API as being successful.
        $mock = new MockHandler([
            new Response(500,
                ['content-type' => 'application/json'],
                json_encode([
                    [
                        "type" => "https://mailchimp.com/developer/marketing/docs/errors/",
                        "title" => "Internal Server Error",
                        "status" => 500,
                        "detail" => "Internal Server Error",
                    ]
                ])
            )
        ]);
        $this->mockMandrillAPiResponces($mock, $history);

        $testMail = $this->getMailable();

        $this->expectException(MandrillTransportException::class);
        $this->expectExceptionCode(500);
        $this->expectExceptionMessage('500 Internal Server Error');

        Mail::to('testemail@example.com')->send($testMail);
    }

    /**
     * Mock the Mandrills underlying Guzzle instance
     * 
     * @param  MockHandler $handler    Used to define a stack of requests to mock
     * @param  array      &$container  Used to view history of requests made via the mock
     * @return void
     */
    protected function mockMandrillAPiResponces(MockHandler $handler, &$container): void
    {
        // Setup mocks
        $stackHandler = HandlerStack::create($handler);

        // Add history tracking middleware
        $history = Middleware::history($container);
        $stackHandler->push($history);

        // Inject a mocked instance of Guzzle into the underlying Mandrill transport.
        // This will allow us to test the mail right through to Mandrills APIs.
        $mockApiClient = new class($stackHandler) extends ApiClient {
            public function __construct($stackHandler)
            {
                parent::__construct();
                // Swap in mocked Guzzle instance
                $this->requestClient = new Client([
                    'handler' => HandlerStack::create($stackHandler)
                ]);
            }
        };

        // Inject this into the transport within the Mail Facade
        Mail::getFacadeRoot()->mailer('mandrill')->getSymfonyTransport()->setClient($mockApiClient);
    }

    protected function getMailable(): Mailable
    {
        return new class() extends Mailable {
            public function build()
            {
                return $this->from('mandrill@test.com', 'Test')
                    ->html('Hello World')
                    ->subject('Testing things');
            }
        };
    }
}