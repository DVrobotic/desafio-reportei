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

    public function changeRequest(string $requestType){
        $this->data['requestType'] = $requestType;
    }

    public function pulls($owner, $repo, $state = 'open', $query = '', $branch = null){
        $query .= "state=" . $state . '&';
        $query .= $branch != null ? ("base=" . $branch . '&') : '';
        try {
            $response = $this->client->request('GET', htmlspecialchars('/repos/' . $owner . '/' . $repo . '/pulls?' . $query),
                [
                    'auth' => [$this->data['username'], $this->data['token']],
                    'accept' => $this->data['accept'],
                    'verify' => true,
                ]
            );
            return $response->getBody();
        } catch (ClientException $e) {
            echo dd($e->getMessage());
        }
    }

    public function handle(string $requestType = null)
    {
        try {
            if($this->data['auth']){
                $response = $this->client->request('GET', $requestType ?? $this->data['requestType'],
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
            echo dd($e->getMessage());
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

            //events must be together with config on json, grizzle doesnt know very well hot to handle objects or arrays of arrays
            $response = $this->client->request('POST', htmlspecialchars('/repos/' . $owner . '/' . $repo . '/hooks'),
                [
                    'auth' => [$this->data['username'], $this->data['token']],
                    'accept' => $this->data['accept'],
                    "name" => "web2", //standarized name for webhooks
                    "active" => true,
                    'json' => [
                        'config' => $config,
                        "events" => ["push", "pull_request"],
                    ]
                ]
            );
            return $response->getBody();

        } catch (ClientException $e) {
            echo dd($e->getMessage());
        }
    }

    public function patch($owner, $repo, $hook_id)
    {
        try {
            // /repos/{owner}/{repo}/hooks
            $config =
                [
                    "url" => 'https://desafio-reportei.codejunior.com.br/user/201',
                    "content_type" => "json",
                    "insecure_ssl" => "0",
                ];

            $response = $this->client->request('PATCH', htmlspecialchars('/repos/' . $owner . '/' . $repo . '/hooks/' . $hook_id),
                [
                    'auth' => [$this->data['username'], $this->data['token']],
                    'accept' => $this->data['accept'],
                    "name" => "web2", //standarized name for webhooks
                    "active" => true,
                    'json' => [
                        'config' => $config,
                        "events" => ['*'],
                    ]
                ]
            );
            return $response->getBody();

        } catch (ClientException $e) {
            echo dd($e->getMessage());
        }
    }

    public function delete($owner, $repo, $hook_id)
    {
        try {
            // /repos/{owner}/{repo}/hooks
            $response = $this->client->request('DELETE', htmlspecialchars('/repos/' . $owner . '/' . $repo . '/hooks/' . $hook_id),
                [
                    'auth' => [$this->data['username'], $this->data['token']],
                    'accept' => $this->data['accept'],
                ]
            );
            return $response->getReasonPhrase();
        } catch (ClientException $e) {
            echo dd($e->getMessage());
        }
    }
}
