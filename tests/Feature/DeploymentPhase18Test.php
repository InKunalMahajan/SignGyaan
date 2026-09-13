<?php

namespace Tests\Feature;

use Tests\TestCase;

class DeploymentPhase18Test extends TestCase
{
    public function test_health_endpoint_is_available(): void
    {
        $this->get('/up')->assertOk();
    }

    public function test_production_environment_template_is_safe_and_explicit(): void
    {
        $template = file_get_contents(base_path('.env.production.example'));

        $this->assertIsString($template);
        $this->assertStringContainsString('APP_ENV=production', $template);
        $this->assertStringContainsString('APP_DEBUG=false', $template);
        $this->assertStringContainsString('DB_CONNECTION=mysql', $template);
        $this->assertStringContainsString('SESSION_SECURE_COOKIE=true', $template);
        $this->assertStringContainsString('QUEUE_CONNECTION=database', $template);
        $this->assertStringContainsString('CACHE_STORE=database', $template);
        $this->assertStringNotContainsString('sk-', $template);
    }

    public function test_deployment_assets_exist(): void
    {
        $paths = [
            'scripts/deploy-production.sh',
            'scripts/rollback-production.sh',
            'ops/nginx/signgyaan.conf.example',
            'ops/systemd/signgyaan-queue.service.example',
            'ops/systemd/signgyaan-scheduler.service.example',
            'ops/systemd/signgyaan-scheduler.timer.example',
            '.github/workflows/deploy.yml',
            'docs/deployment.md',
        ];

        foreach ($paths as $path) {
            $this->assertFileExists(base_path($path), $path.' should exist');
        }
    }
}
