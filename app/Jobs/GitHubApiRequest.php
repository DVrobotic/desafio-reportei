<?php

namespace App\Jobs;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class GitHubApiRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected const URL =  "https://api.github.com";
    protected $client, $data = ['username', 'token', 'requestType', 'auth', 'accept', 'debug'];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($username, $token, $requestType, $auth = true, $accept = 'application/vnd.github.v3+json' )
    {
        $this->data['username'] = $username;
        $this->data['token'] = $token;
        $this->data['requestType'] = htmlspecialchars(str_replace(self::URL, '', $requestType));
        $this->data['debug'] = true;
        $this->data['auth'] = $auth;
        $this->data['accept'] = $accept;
        $this->client = new Client(['base_uri' => self::URL]);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            if($this->data['auth']){
                $response = $this->client->request('GET', $this->data['requestType'],
                    [
                        'auth' => [$this->data['username'], $this->data['token']],
                        'verify' => false,
                        'accept' => $this->data['accept'],
                    ]
                );
            } else{
                $response = $this->client->request('GET', $this->data['requestType'],
                    [
                        'verify' => false,
                        'accept' => $this->data['accept'],
                    ]
                );
            }

            return $response->getBody();
        } catch (ClientException $e) {
            echo $e->getRequest();
            echo $e->getResponse();
        }
    }

    public function webhook($owner, $repo)
    {
        try {
            // /repos/{owner}/{repo}/hooks
            $config =
            [
                "url" => 'https://desafio-reportei.codejunior.com.br/user/4',
                "content_type" => "json",
                "insecure_ssl" => "0",
            ];

            $response = $this->client->request('POST', htmlspecialchars('/repos/' . $owner . '/' . $repo . '/hooks'),
                [
                    'auth' => [$this->data['username'], $this->data['token']],
                    'accept' => $this->data['accept'],
                    "name" => "web2", //standarized name for webhooks
                    "active" => true,
                    "events" => ["push", "pull_request"],
                    'json' => [
                        'config' => $config
                    ]
                ]
            );

        } catch (ClientException $e) {
            echo dd($e->getMessage());
        }
    }
}