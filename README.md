## Laravel - Anapro

[comment]: <> ([![Downloads]&#40;https://img.shields.io/packagist/dt/agenciafmd/laravel-rdstation.svg?style=flat-square&#41;]&#40;https://packagist.org/packages/agenciafmd/laravel-rdstation&#41;)

[comment]: <> ([![Licença]&#40;https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square&#41;]&#40;LICENSE.md&#41;)

- Envia as conversões para o Anapro

## Instalação

```bash
composer require agenciafmd/laravel-anapro:dev-master
```

## Configuração

Para que a integração seja realizada, precisaremos de algumas chaves

Para gerar essa chaves, vamos em *Em breve melhores instruções*

```dotenv
ANAPRO_KEY="c25e75969eefd3aa89b89e785748f868"
ANAPRO_CAMPANHA_KEY="ba3c379f4290982c71daaa7b834f782c"
ANAPRO_PRODUTO_KEY="9e4099553a4c4b47774a8979de1e8f50"
ANAPRO_CANAL_KEY="6d4be20f908bf28b6d12bf563a0b28bd"
ANAPRO_KEY_INTEGRADORA="69a3dea0b368365f3e37f67ba56a1a50"
ANAPRO_KEY_AGENCIA="6cd9778a179e740e49b72f1826fb1ddc"
```

## Uso

Envie os campos no formato de array para o SendConversionsToAnapro.

O campo **email** é obrigatório =)

Para que o processo funcione pelos **jobs**, é preciso passar os valores dos cookies conforme mostrado abaixo.

```php
use Agenciafmd\Anapro\Jobs\SendConversionsToAnapro;

$phone = preg_replace('/[^0-9]/', '', $data['phone']);
        $description = '** Agende uma visita **Nome:** ' . $data['name'] .
            ' **E-mail:** ' . $data['email'] .
            ' **Celular:** ' . $data['phone'] .
            $data['terms'] ? '**Termos de uso:** ' . 'Sim' : '**Termos de uso:** ' . 'Não';

        $data = [
            "Key" => 'xEFMUTTLENE1',
            "ProdutoKey" => "",
            "Midia" => Cookie::get('utm_source', ''),
            "Peca" => Cookie::get('utm_medium', ''),
            "UsuarioEmail" => "",
            "GrupoPeca" => "",
            "CampanhaPeca" => Cookie::get('utm_campaign', ''),
            "PessoaNome" => $data['name'],
            "ValidarEmail" => "false",
            "PessoaEmail" => $data['email'],
            "ValidarTelefone" => "false",
            "PessoaTelefones" => [
                [
                    "Tipo" => "OUTR",
                    'DDD' => substr($phone, 0, 2),
                    'Numero' => substr($phone, 2),
                    "Ramal" => null
                ]
            ],
            "Observacoes" => $description,
        ];

        SendConversionsToAnapro::dispatch($data);
```

Note que no nosso exemplo, enviamos o job para a fila **low**.

Certifique-se de estar rodando no seu queue:work esteja semelhante ao abaixo.

```shell
php artisan queue:work --tries=3 --delay=5 --timeout=60 --queue=high,default,low
```