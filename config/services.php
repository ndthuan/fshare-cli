<?php

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\CookieJarInterface;
use Interop\Container\ContainerInterface;
use JsonRPC\Client;
use Ndthuan\Aria2RpcAdapter\Adapter;
use Ndthuan\FshareCli\Downloading\Aria2RpcDownloader;
use Ndthuan\FshareCli\Downloading\DownloaderInterface;
use Ndthuan\FshareCli\Downloading\History\DummyHistoryProvider;
use Ndthuan\FshareCli\Downloading\History\HistoryProviderInterface;
use Ndthuan\FshareLib\Api\FileFetcherInterface;
use Ndthuan\FshareLib\Api\FolderFetcherInterface;
use Ndthuan\FshareLib\Api\FshareClientInterface;
use Ndthuan\FshareLib\HtmlClient\Auth\AuthenticatorInterface;
use Ndthuan\FshareLib\HtmlClient\Auth\CookieBasedAuthenticator;
use Ndthuan\FshareLib\HtmlClient\HtmlBasedFileFetcher;
use Ndthuan\FshareLib\HtmlClient\HtmlBasedFolderFetcher;
use Ndthuan\FshareLib\HtmlClient\HtmlClient;
use Ndthuan\FshareLib\HtmlClient\RequestDecorator\ReferralRequestDecorator;
use Ndthuan\FshareLib\HtmlClient\RequestDecorator\RequestDecoratorInterface;

$configs = parse_ini_file(__DIR__ . '/config.ini');

return array_merge($configs, [
    Adapter::class => function (ContainerInterface $container) {
        return new Adapter(
            new Client($container->get('aria2.rpc.url')),
            $container->has('aria2.rpc.token') ? $container->get('aria2.rpc.token') : ''
        );
    },
    GearmanWorker::class => function (ContainerInterface $container) {
        $gearmanWorker = new GearmanWorker();
        $gearmanWorker->addServer(
            $container->has('gearman.host') ? $container->get('gearman.host') : '127.0.0.1',
            $container->has('gearman.port') ? $container->get('gearman.port') : 4730
        );
        $gearmanWorker->addFunction(
            'downloadFshareFile',
            [$container->get(DownloaderInterface::class), 'downloadFshareFile']
        );

        return $gearmanWorker;
    },
    GearmanClient::class => function (ContainerInterface $container) {
        $gearmanClient = new GearmanClient();
        $gearmanClient->addServer(
            $container->has('gearman.host') ? $container->get('gearman.host') : '127.0.0.1',
            $container->has('gearman.port') ? $container->get('gearman.port') : 4730
        );

        return $gearmanClient;
    },
    AuthenticatorInterface::class => function (ContainerInterface $container) {
        return new CookieBasedAuthenticator(
            $container->get('fshare.email'),
            $container->get('fshare.password'),
            $container->get(RequestDecoratorInterface::class)
        );
    },
    ClientInterface::class => function (ContainerInterface $container) {
        return new \GuzzleHttp\Client([
            'cookies' => $container->get(CookieJarInterface::class),
            'user-agent' => $container->get('http.client.userAgent')
        ]);
    },
    DownloaderInterface::class          => DI\object(Aria2RpcDownloader::class),
    FshareClientInterface::class        => DI\object(HtmlClient::class),
    FolderFetcherInterface::class       => DI\object(HtmlBasedFolderFetcher::class),
    FileFetcherInterface::class         => DI\object(HtmlBasedFileFetcher::class),
    RequestDecoratorInterface::class    => DI\object(ReferralRequestDecorator::class),
    CookieJarInterface::class           => DI\object(CookieJar::class),
    HistoryProviderInterface::class     => DI\object(DummyHistoryProvider::class),
]);
