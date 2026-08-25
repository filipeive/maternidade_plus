<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlertaOfflineTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    // ==========================================
    // TIER 1: SERVICE WORKER & MANIFEST EXISTENCE
    // ==========================================

    public function test_service_worker_file_exists_and_contains_caching_logic(): void
    {
        $swPath = public_path('sw.js');
        $this->assertFileExists($swPath, 'Service Worker file public/sw.js must exist');

        $content = file_get_contents($swPath);
        $this->assertNotEmpty($content);

        // Verify standard Service Worker lifecycle events and offline caching logic
        $this->assertStringContainsString('install', $content);
        $this->assertStringContainsString('fetch', $content);
        $this->assertStringContainsString('caches.open', $content);
    }

    public function test_web_app_manifest_exists_and_is_valid_json(): void
    {
        $manifestPath = public_path('manifest.json');
        $this->assertFileExists($manifestPath, 'Web App Manifest public/manifest.json must exist');

        $content = file_get_contents($manifestPath);
        $json = json_decode($content, true);

        $this->assertNotNull($json, 'Manifest must be valid JSON');
        $this->assertArrayHasKey('name', $json);
        $this->assertArrayHasKey('short_name', $json);
        $this->assertArrayHasKey('start_url', $json);
        $this->assertArrayHasKey('display', $json);
        $this->assertArrayHasKey('icons', $json);
    }

    public function test_offline_alerts_javascript_helper_exists(): void
    {
        $jsPath = public_path('js/offline-alerts.js');
        $this->assertFileExists($jsPath, 'public/js/offline-alerts.js must exist for IndexedDB action queue');

        $content = file_get_contents($jsPath);
        $this->assertStringContainsString('indexedDB', $content);
    }

    // ==========================================
    // TIER 1 & 2: UI INTEGRATION & OFFLINE BANNER
    // ==========================================

    public function test_layout_includes_offline_banner_and_service_worker_registration(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertStatus(200);

        // Check for offline banner element
        $content = $response->getContent();
        $this->assertTrue(
            str_contains($content, 'offline-banner') || str_contains($content, 'Sem conexão'),
            'Dashboard layout must contain offline banner element or text'
        );

        // Check for Service Worker registration script
        $this->assertTrue(
            str_contains($content, 'serviceWorker.register') || str_contains($content, 'sw.js'),
            'Dashboard layout must register Service Worker'
        );

        // Check for manifest link tag
        $this->assertStringContainsString('manifest.json', $content);
    }
}
