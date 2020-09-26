<?php
/**
 * This file is part of SocialLogin4
 *
 * Copyright(c) Akira Kurozumi <info@a-zumi.net>
 *
 *  https://a-zumi.net
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\SocialLogin4\Tests\Web\Admin;


use Eccube\Common\Constant;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Eccube\Util\StringUtil;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Filesystem\Filesystem;

class ConfigControllerTest extends AbstractAdminWebTestCase
{
    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
    }

    public function tearDown()
    {
        parent::tearDown(); // TODO: Change the autogenerated stub
    }

    public function testAuth0情報を保存したらenvファイルに情報が追記されるか()
    {
        $this->client->request('POST', $this->generateUrl('social_login_admin_config'), [
            'config' => [
                'client_id' => 'dummy',
                'client_secret' => 'dummy',
                'custom_domain' => 'dummy',
                Constant::TOKEN_NAME => 'dummy'
            ]
        ]);

        $container = self::$kernel->getContainer();
        $envFile = $container->getParameter('kernel.project_dir') . '/.env';

        $fs = new Filesystem();
        $fs->copy($envFile, $envFile.'.backup');

        $env = file_get_contents($envFile);

        $keys = [
            'OAUTH_AUTH0_CLIENT_ID',
            'OAUTH_AUTH0_CLIENT_SECRET',
            'OAUTH_AUTH0_CUSTOM_DOMAIN'
        ];

        foreach ($keys as $key) {
            $pattern = '/^(' . $key . ')=(.*)/m';
            if (preg_match($pattern, $env, $matches)) {
                self::assertEquals('dummy', $matches[2]);
            } else {
                self::fail(sprintf("%sが見つかりませんでした。", $key));
            }
        }

        $fs->rename($envFile.'.backup', $envFile);
    }

    public function testENVファイルに上記の設定が残ったままか確認()
    {
        $container = self::$kernel->getContainer();
        $envFile = $container->getParameter('kernel.project_dir') . '/.env';

        $env = file_get_contents($envFile);

        $keys = [
            'OAUTH_AUTH0_CLIENT_ID',
            'OAUTH_AUTH0_CLIENT_SECRET',
            'OAUTH_AUTH0_CUSTOM_DOMAIN'
        ];

        foreach ($keys as $key) {
            $pattern = '/^(' . $key . ')=(.*)/m';
            if (preg_match($pattern, $env, $matches)) {
                self::assertEquals('dummy', $matches[2]);
            } else {
                self::fail(sprintf("%sが見つかりませんでした。", $key));
            }
        }
    }
}
