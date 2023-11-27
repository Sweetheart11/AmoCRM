<?php

declare(strict_types = 1);

use App\Config;
use Slim\Views\Twig;
use function DI\create;
use Doctrine\ORM\ORMSetup;
use App\Enum\AppEnvironment;
use Doctrine\ORM\EntityManager;
use Twig\Extra\Intl\IntlExtension;
use App\Services\AmoCRMAuthService;
use App\Services\AmoCRMCurlService;
use Symfony\Component\Asset\Package;
use Psr\Container\ContainerInterface;
use Symfony\Component\Asset\Packages;
use App\Contracts\CRMAuthServiceInterface;

use App\Contracts\CRMCurlServiceInterface;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\WebpackEncoreBundle\Asset\TagRenderer;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookup;
use Symfony\WebpackEncoreBundle\Twig\EntryFilesTwigExtension;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;

return [
    Config::class                 => create(Config::class)->constructor(require CONFIG_PATH . '/app.php'),

    EntityManager::class          => fn(Config $config) => EntityManager::create(
        $config->get('doctrine.connection'),
        ORMSetup::createAttributeMetadataConfiguration(
            $config->get('doctrine.entity_dir'),
            $config->get('doctrine.dev_mode')
        )
    ),
    Twig::class                   => function (Config $config, ContainerInterface $container) {
        $twig = Twig::create(VIEW_PATH, [
            'cache'       => STORAGE_PATH . '/cache/templates',
            'auto_reload' => AppEnvironment::isDevelopment($config->get('app_environment')),
        ]);

        $twig->addExtension(new IntlExtension());
        $twig->addExtension(new EntryFilesTwigExtension($container));
        $twig->addExtension(new AssetExtension($container->get('webpack_encore.packages')));

        return $twig;
    },


    /**
     * The following two bindings are needed for EntryFilesTwigExtension & AssetExtension to work for Twig
     */
    'webpack_encore.packages'     => fn() => new Packages(
        new Package(new JsonManifestVersionStrategy(BUILD_PATH . '/manifest.json'))
    ),
    'webpack_encore.tag_renderer' => fn(ContainerInterface $container) => new TagRenderer(
        new EntrypointLookup(BUILD_PATH . '/entrypoints.json'),
        $container->get('webpack_encore.packages')
    ),
    AmoCRMCurlService::class => function(Config $config) {
        return new AmoCRMCurlService(
            $config->get('amo_crm.subdomain'),
            $config->get('amo_crm.pipeline_id'),
        );
    },
    AmoCRMAuthService::class =>     function (Config $config, ContainerInterface $container) {
        return new AmoCRMAuthService(
            $config->get('amo_crm.subdomain'),
            $config->get('amo_crm.client_secret'),
            $config->get('amo_crm.client_id'),
            $config->get('amo_crm.auth_code'),
            $config->get('amo_crm.token_file'),
            $config->get('amo_crm.redirect_uri'),
            $container->get(AmoCRMCurlService::class),
        );
    },
    CRMAuthServiceInterface::class => fn(ContainerInterface $container) => $container->get(
        AmoCRMAuthService::class,
    ),
    CRMCurlServiceInterface::class => fn(ContainerInterface $container) => $container->get(
        AmoCRMCurlService::class,
    ),
];
