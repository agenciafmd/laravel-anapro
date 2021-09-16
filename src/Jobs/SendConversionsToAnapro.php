<?php

namespace Agenciafmd\Anapro\Jobs;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cookie;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class SendConversionsToAnapro implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function handle()
    {
        if (!config('laravel-anapro.key_agencia')) {
            return false;
        }

        $client = $this->getClientRequest();

        $defaultKeys = [
            "Key" => config('laravel-anapro.key'),
            "CampanhaKey" => config('laravel-anapro.campanha_key'),
            "ProdutoKey" => config('laravel-anapro.produto_key'),
            "CanalKey" => config('laravel-anapro.canal_key'),
            "KeyIntegradora" => config('laravel-anapro.key_integradora'),
            'AgenciaKey' => config('laravel-anapro.key_agencia'),
        ];


        $content = $this->data + $defaultKeys;


        $client->request('POST', 'http://crm.anapro.com.br/webcrm/webapi/integracao/v2/CadastrarProspect', [
            'json' => $content,
        ]);
    }

    private function getClientRequest()
    {
        $logger = new Logger('AnaPro');
        $logger->pushHandler(new StreamHandler(storage_path('logs/anapro-' . date('Y-m-d') . '.log')));

        $stack = HandlerStack::create();
        $stack->push(
            Middleware::log(
                $logger,
                new MessageFormatter("{method} {uri} HTTP/{version} {req_body} | RESPONSE: {code} - {res_body}")
            )
        );

        return new Client([
            'timeout' => 60,
            'connect_timeout' => 60,
            'http_errors' => false,
            'verify' => false,
            'handler' => $stack,
        ]);
    }
}
